import pandas as pd
import numpy as np
import warnings
warnings.filterwarnings('ignore')

from sklearn.model_selection import train_test_split
from sklearn.preprocessing import LabelEncoder, StandardScaler
from sklearn.metrics import (
    accuracy_score, classification_report, confusion_matrix,
    f1_score, precision_score, recall_score, cohen_kappa_score, matthews_corrcoef
)
from sklearn.ensemble import VotingClassifier, RandomForestClassifier, ExtraTreesClassifier
from sklearn.linear_model import LogisticRegression

import xgboost as xgb
import lightgbm as lgb
import catboost as cb
from imblearn.over_sampling import SMOTE
from imblearn.combine import SMOTEENN
from collections import Counter
import pickle
import os
from datetime import datetime


class CreditScoringModelUltra:
    """Credit Scoring Model - Balanced Accuracy (No Overfitting)"""
    
    def __init__(self, random_state=42):
        self.random_state = random_state
        self.model = None
        self.le_dict = {}
        self.le_target = None
        self.scaler = None
        self.feature_names = []
        self.scaled_features = []  # Track which features are scaled
        self.inv_target_mapping = {}
        
    def parse_money_input(self, value):
        """Parse input uang"""
        try:
            if isinstance(value, (int, float)):
                return float(value)
            value = str(value).lower().strip()
            value = value.replace('rp', '').replace('.', '').replace(',', '').strip()
            
            multipliers = {'jt': 1_000_000, 'juta': 1_000_000, 'rb': 1_000, 'ribu': 1_000, 'm': 1_000_000, 'k': 1_000}
            
            for suffix, mult in multipliers.items():
                if suffix in value:
                    return float(value.replace(suffix, '').strip()) * mult
            return float(value)
        except:
            return 0.0

    def load_and_preprocess_data(self):
        """Load dan preprocess data dengan feature engineering optimized"""
        print("\n" + "="*80)
        print(" LOADING & PREPROCESSING DATA")
        print("="*80)
        
        # Load files
        files = [
            'data_dummy_koperasi.xlsx',
            'data_dummy_koperasi_1500_new.xlsx',
            'data_1000_rows_koperasi.xlsx',
            'data_dummy_koperasi_2000.xlsx',
            'data_dummy_koperasi_2500.xlsx',
            'dataset_kelayakan_2000.xlsx'
        ]
        
        dataframes = []
        for filename in files:
            try:
                df = pd.read_excel(filename, engine='openpyxl')
                if not df.empty:
                    print(f"  ✓ Loaded {filename}: {len(df)} rows")
                    dataframes.append(df)
            except FileNotFoundError:
                print(f"  ⚠ File tidak ditemukan: {filename}")
            except Exception as e:
                print(f"  ⚠ Error loading {filename}")
        
        if not dataframes:
            raise FileNotFoundError("Tidak ada file training data!")
        
        df = pd.concat(dataframes, ignore_index=True, sort=False)
        print(f"\n  ✓ Total data: {len(df)} rows")
        
        # Clean columns
        df.columns = df.columns.str.strip()
        
        # Drop unnecessary columns
        drop_cols = ['Nama', 'ID', 'No_KTP', 'NIK', 'No_Telepon', 'Alamat', 'Tanggal']
        for col in drop_cols:
            if col in df.columns:
                df.drop(columns=[col], inplace=True)
        
        # Remove duplicates and missing target
        df = df.drop_duplicates()
        df = df.dropna(subset=['Kelayakan'])
        print(f"  ✓ Setelah cleaning: {len(df)} rows")
        
        # Column mappings
        column_mappings = {
            'Jumlah_Pinjaman_Diajukan': 'Jumlah_Pinjaman',
            'Durasi_Pinjaman_Bulan': 'Lama_Tenor_Bulan',
            'Riwayat_Pinjam': 'Riwayat_Tunggakan',
        }
        
        for old_name, new_name in column_mappings.items():
            if old_name in df.columns and new_name not in df.columns:
                df.rename(columns={old_name: new_name}, inplace=True)
        
        # Required columns with defaults
        required_numeric = {
            'Umur': 30,
            'Jumlah_Tanggungan': 0,
            'Lama_Bekerja_Tahun': 1,
            'Pendapatan_Bulanan': 5000000,
            'Pengeluaran_Bulanan': 3000000,
            'Skor_Kredit': 50,
            'Jumlah_Pinjaman': 10000000,
            'Lama_Tenor_Bulan': 12
        }
        
        required_categorical = {
            'Status_Pernikahan': 'Lajang',
            'Pendidikan': 'SMA',
            'Jenis_Pekerjaan': 'Karyawan',
            'Riwayat_Tunggakan': 'Tidak',
            'Jaminan': 'Tidak Ada',
            'Tujuan_Pinjaman': 'Lainnya',
            'Status_Tempat_Tinggal': 'Milik Sendiri'
        }
        
        # Add missing columns
        for col, default_val in required_numeric.items():
            if col not in df.columns:
                df[col] = default_val
        
        for col, default_val in required_categorical.items():
            if col not in df.columns:
                df[col] = default_val
        
        # Convert numeric columns
        for col in required_numeric.keys():
            if col in df.columns:
                df[col] = pd.to_numeric(df[col], errors='coerce')
        
        # Fill missing values
        for col in df.select_dtypes(include=[np.number]).columns:
            if col != 'Kelayakan' and df[col].isna().any():
                median_val = df[col].median()
                if pd.isna(median_val):
                    median_val = required_numeric.get(col, 0)
                df[col].fillna(median_val, inplace=True)
        
        for col in df.select_dtypes(include=['object']).columns:
            if col != 'Kelayakan' and df[col].isna().any():
                mode_vals = df[col].mode()
                if len(mode_vals) > 0:
                    df[col].fillna(mode_vals[0], inplace=True)
                else:
                    df[col].fillna(required_categorical.get(col, 'Unknown'), inplace=True)
        
        df.replace([np.inf, -np.inf], np.nan, inplace=True)
        df.fillna(0, inplace=True)
        
        print(f"\n  [ADVANCED FEATURE ENGINEERING]")
        
        # Safe numerical operations
        pendapatan = df['Pendapatan_Bulanan'].replace(0, 1).clip(lower=1)
        pengeluaran = df['Pengeluaran_Bulanan'].replace(0, 1).clip(lower=1)
        pinjaman = df['Jumlah_Pinjaman'].replace(0, 1).clip(lower=1)
        tenor = df['Lama_Tenor_Bulan'].replace(0, 1).clip(lower=1)
        umur = df['Umur'].replace(0, 18).clip(lower=18, upper=100)
        lama_kerja = df['Lama_Bekerja_Tahun'].clip(lower=0, upper=50)
        skor = df['Skor_Kredit'].clip(lower=0, upper=100)
        tanggungan = df['Jumlah_Tanggungan'].clip(lower=0, upper=10)
        
        # === CORE FINANCIAL FEATURES ===
        df['Angsuran_Bulanan'] = (pinjaman / tenor).clip(0, 50_000_000)
        df['DTI_Ratio'] = (df['Angsuran_Bulanan'] / pendapatan).clip(0, 2)
        df['Pendapatan_Bersih'] = (pendapatan - pengeluaran).clip(-50_000_000, 100_000_000)
        df['Rasio_Pengeluaran'] = (pengeluaran / pendapatan).clip(0, 2)
        df['Kemampuan_Bayar'] = df['Pendapatan_Bersih'] - df['Angsuran_Bulanan']
        df['Loan_Income_Ratio'] = (pinjaman / (pendapatan * 12)).clip(0, 10)
        df['Payment_to_Income'] = (df['Angsuran_Bulanan'] / pendapatan).clip(0, 1)
        df['Disposable_Income_Ratio'] = (df['Pendapatan_Bersih'] / pendapatan).clip(-1, 1)
        df['Saving_Rate'] = ((pendapatan - pengeluaran) / pendapatan).clip(-1, 1)
        df['Expense_Coverage'] = (pendapatan / pengeluaran).clip(0, 10)
        
        # === CREDIT FEATURES ===
        df['Credit_Quality'] = (skor / 100).clip(0, 1)
        df['Credit_Risk'] = ((100 - skor) / 100).clip(0, 1)
        df['Credit_Score_Squared'] = (skor / 100) ** 2
        df['Credit_Score_Cubed'] = (skor / 100) ** 3
        df['Adjusted_Credit_Score'] = (skor / 100) * (1 - df['DTI_Ratio'].clip(0, 1))
        
        # Credit categories
        df['Credit_Excellent'] = (skor >= 80).astype(int)
        df['Credit_Good'] = ((skor >= 60) & (skor < 80)).astype(int)
        df['Credit_Poor'] = (skor < 40).astype(int)
        
        # === STABILITY FEATURES ===
        df['Work_Stability'] = (lama_kerja / (umur - 17).clip(1, 100)).clip(0, 1)
        df['Age_Risk'] = ((umur - 35).abs() / 35).clip(0, 2)
        df['Age_Maturity'] = ((umur - 25) / 40).clip(0, 1)
        df['Career_Progress'] = (lama_kerja / umur).clip(0, 1)
        df['Prime_Age'] = ((umur >= 30) & (umur <= 50)).astype(int)
        df['Stable_Job'] = (lama_kerja >= 3).astype(int)
        
        # === FAMILY BURDEN ===
        df['Per_Capita_Income'] = (pendapatan / (tanggungan + 1)).clip(0, 50_000_000)
        df['Burden_Ratio'] = (tanggungan / (umur / 10)).clip(0, 10)
        df['Family_Load'] = tanggungan * df['Rasio_Pengeluaran']
        df['No_Dependents'] = (tanggungan == 0).astype(int)
        df['High_Dependents'] = (tanggungan >= 3).astype(int)
        
        # === LOAN CHARACTERISTICS ===
        df['Tenor_Risk'] = (tenor / 60).clip(0, 2)
        df['Monthly_Burden'] = (df['Angsuran_Bulanan'] / pendapatan * 100).clip(0, 100)
        df['Loan_Size_Ratio'] = (pinjaman / 50_000_000).clip(0, 2)
        df['Short_Tenor'] = (tenor <= 12).astype(int)
        df['Long_Tenor'] = (tenor >= 36).astype(int)
        df['Small_Loan'] = (pinjaman < 10_000_000).astype(int)
        df['Large_Loan'] = (pinjaman > 30_000_000).astype(int)
        
        # === INTERACTION FEATURES (KEY!) ===
        df['DTI_x_Credit'] = df['DTI_Ratio'] * df['Credit_Risk']
        df['DTI_x_Credit_Quality'] = df['DTI_Ratio'] * (1 - df['Credit_Quality'])
        df['Income_x_Stability'] = ((pendapatan / 10_000_000) * df['Work_Stability']).clip(0, 10)
        df['Income_x_Credit'] = ((pendapatan / 10_000_000) * df['Credit_Quality']).clip(0, 10)
        df['Loan_x_Tenor'] = ((pinjaman / 10_000_000) * (tenor / 60)).clip(0, 20)
        df['Age_x_Credit'] = (umur / 100) * df['Credit_Quality']
        df['Credit_x_DTI'] = df['Credit_Quality'] * (1 - df['DTI_Ratio'].clip(0, 1))
        df['Saving_x_Credit'] = df['Saving_Rate'] * df['Credit_Quality']
        df['Stability_x_Credit'] = df['Work_Stability'] * df['Credit_Quality']
        
        # Triple interactions
        df['DTI_Credit_Stability'] = df['DTI_Ratio'] * df['Credit_Risk'] * (1 - df['Work_Stability'])
        df['Income_Credit_Age'] = (pendapatan / 10_000_000) * df['Credit_Quality'] * df['Age_Maturity']
        
        # === POLYNOMIAL FEATURES ===
        df['DTI_Squared'] = df['DTI_Ratio'] ** 2
        df['DTI_Cubed'] = df['DTI_Ratio'] ** 3
        df['Income_Log'] = np.log1p(pendapatan / 1_000_000)
        df['Income_Sqrt'] = np.sqrt(pendapatan / 1_000_000)
        df['Loan_Log'] = np.log1p(pinjaman / 1_000_000)
        df['Loan_Sqrt'] = np.sqrt(pinjaman / 1_000_000)
        df['Age_Squared'] = (umur / 100) ** 2
        df['Tenor_Log'] = np.log1p(tenor)
        
        # === COMPOSITE SCORES ===
        df['Financial_Health_Score'] = (
            df['Credit_Quality'] * 0.30 +
            (1 - df['DTI_Ratio'].clip(0, 1)) * 0.25 +
            df['Saving_Rate'].clip(0, 1) * 0.20 +
            df['Work_Stability'] * 0.15 +
            df['Age_Maturity'] * 0.10
        )
        
        df['Risk_Score'] = (
            df['Credit_Risk'] * 0.30 +
            df['DTI_Ratio'].clip(0, 1) * 0.25 +
            df['Age_Risk'] * 0.15 +
            df['Tenor_Risk'] * 0.15 +
            (tanggungan / 10) * 0.15
        )
        
        df['Approval_Score'] = (
            df['Credit_Quality'] * 0.35 +
            (1 - df['DTI_Ratio'].clip(0, 1)) * 0.30 +
            df['Work_Stability'] * 0.20 +
            (1 - df['Tenor_Risk']) * 0.15
        )
        
        # === DOMAIN BINARY FEATURES ===
        df['DTI_Acceptable'] = (df['DTI_Ratio'] <= 0.35).astype(int)
        df['DTI_Critical'] = (df['DTI_Ratio'] > 0.50).astype(int)
        df['Positive_Savings'] = (df['Saving_Rate'] > 0).astype(int)
        df['Deficit'] = (df['Saving_Rate'] < 0).astype(int)
        df['High_Income'] = (pendapatan >= 10_000_000).astype(int)
        df['Low_Income'] = (pendapatan < 3_000_000).astype(int)
        
        # Clean infinite values
        df.replace([np.inf, -np.inf], 0, inplace=True)
        df.fillna(0, inplace=True)
        
        feature_count = len([c for c in df.columns if c not in required_numeric and c not in required_categorical and c != 'Kelayakan'])
        print(f"    ✓ Created {feature_count} engineered features")
        
        # Encoding categorical features
        print(f"\n  [ENCODING]")
        cat_cols = [col for col in df.columns 
                   if df[col].dtype == 'object' and col != 'Kelayakan']
        
        for col in cat_cols:
            df[col].fillna('Unknown', inplace=True)
            le = LabelEncoder()
            df[col] = le.fit_transform(df[col].astype(str))
            self.le_dict[col] = le
        
        print(f"    ✓ Encoded {len(cat_cols)} categorical features")
        
        # Encode target
        self.le_target = LabelEncoder()
        df['Kelayakan'] = self.le_target.fit_transform(df['Kelayakan'])
        self.inv_target_mapping = dict(enumerate(self.le_target.classes_))
        print(f"    ✓ Target classes: {list(self.le_target.classes_)}")
        
        # Scaling
        print(f"\n  [SCALING]")
        num_cols = [col for col in df.columns 
                   if df[col].dtype in ['int64', 'float64'] and col != 'Kelayakan']
        
        self.scaler = StandardScaler()
        df[num_cols] = self.scaler.fit_transform(df[num_cols])
        self.scaled_features = num_cols  # Save which features were scaled
        print(f"    ✓ Scaled {len(num_cols)} features")
        
        self.feature_names = [col for col in df.columns if col != 'Kelayakan']
        
        # Class distribution
        print(f"\n  [CLASS DISTRIBUTION]")
        for cls, count in sorted(Counter(df['Kelayakan']).items()):
            pct = count / len(df) * 100
            print(f"    • {self.inv_target_mapping[cls]}: {count} ({pct:.1f}%)")
        
        X = df[self.feature_names]
        y = df['Kelayakan']
        
        print(f"\n  ✓ Final: {X.shape[0]} samples, {X.shape[1]} features\n")
        
        return X, y

    def train_model(self, X, y, target_accuracy=0.95):
        """Train optimized ensemble with proper regularization"""
        print("="*80)
        print(f" TRAINING OPTIMIZED MODEL (Target: Test {target_accuracy*100:.0f}%)")
        print("="*80)
        
        print(f"\n  [SPLIT DATA 80/20]")
        X_train, X_test, y_train, y_test = train_test_split(
            X, y, test_size=0.20, random_state=self.random_state, stratify=y
        )
        print(f"    Train: {len(X_train)} | Test: {len(X_test)}")
        
        # SMOTEENN balancing
        print(f"\n  [SMOTEENN BALANCING]")
        print(f"    Before: {Counter(y_train)}")
        
        X_train_clean = np.nan_to_num(X_train.values, nan=0.0, posinf=0.0, neginf=0.0)
        
        smoteenn = SMOTEENN(
            smote=SMOTE(sampling_strategy='auto', k_neighbors=5, random_state=self.random_state),
            random_state=self.random_state,
            n_jobs=1  # Avoid memory issues
        )
        X_train_balanced, y_train_balanced = smoteenn.fit_resample(X_train_clean, y_train)
        X_train_balanced = pd.DataFrame(X_train_balanced, columns=self.feature_names)
        
        print(f"    After:  {Counter(y_train_balanced)}")
        
        num_classes = len(np.unique(y))
        
        print(f"\n  [BUILDING BASE MODELS - REGULARIZED]")
        start_time = datetime.now()
        
        # Split for validation
        X_tr, X_val, y_tr, y_val = train_test_split(
            X_train_balanced, y_train_balanced, test_size=0.20,
            random_state=self.random_state, stratify=y_train_balanced
        )
        
        # XGBoost - Heavy regularization
        print(f"    Training XGBoost...")
        xgb_model = xgb.XGBClassifier(
            objective='multi:softprob' if num_classes > 2 else 'binary:logistic',
            n_estimators=400,
            max_depth=6,  # Reduced from 8
            learning_rate=0.05,
            min_child_weight=5,  # Increased regularization
            gamma=0.2,  # Increased
            subsample=0.8,
            colsample_bytree=0.8,
            reg_alpha=0.3,  # Increased
            reg_lambda=2.0,  # Increased
            random_state=self.random_state,
            tree_method='hist',
            n_jobs=-1,
            verbosity=0
        )
        xgb_model.fit(X_tr, y_tr, eval_set=[(X_val, y_val)], verbose=False)
        print(f"    ✓ XGBoost trained")
        
        # LightGBM - Heavy regularization
        print(f"    Training LightGBM...")
        lgb_model = lgb.LGBMClassifier(
            objective='multiclass' if num_classes > 2 else 'binary',
            n_estimators=400,
            max_depth=6,
            learning_rate=0.05,
            num_leaves=31,  # Reduced from 50
            min_child_samples=30,  # Increased
            subsample=0.8,
            colsample_bytree=0.8,
            reg_alpha=0.3,
            reg_lambda=2.0,
            random_state=self.random_state,
            verbose=-1,
            n_jobs=-1
        )
        lgb_model.fit(X_tr, y_tr, eval_set=[(X_val, y_val)], callbacks=[lgb.early_stopping(100, verbose=False)])
        print(f"    ✓ LightGBM trained")
        
        # CatBoost - Regularized
        print(f"    Training CatBoost...")
        cat_model = cb.CatBoostClassifier(
            iterations=400,
            depth=6,
            learning_rate=0.05,
            l2_leaf_reg=3.0,  # Increased
            random_state=self.random_state,
            verbose=0,
            thread_count=-1
        )
        cat_model.fit(X_tr, y_tr, eval_set=(X_val, y_val), verbose=False)
        print(f"    ✓ CatBoost trained")
        
        # Random Forest - Reduced complexity
        print(f"    Training Random Forest...")
        rf_model = RandomForestClassifier(
            n_estimators=200,
            max_depth=12,  # Reduced from 15
            min_samples_split=10,  # Increased
            min_samples_leaf=5,  # Increased
            max_features='sqrt',
            class_weight='balanced',
            random_state=self.random_state,
            n_jobs=-1
        )
        rf_model.fit(X_train_balanced, y_train_balanced)
        print(f"    ✓ Random Forest trained")
        
        # Extra Trees - Reduced complexity
        print(f"    Training Extra Trees...")
        et_model = ExtraTreesClassifier(
            n_estimators=200,
            max_depth=12,
            min_samples_split=10,
            min_samples_leaf=5,
            max_features='sqrt',
            class_weight='balanced',
            random_state=self.random_state,
            n_jobs=-1
        )
        et_model.fit(X_train_balanced, y_train_balanced)
        print(f"    ✓ Extra Trees trained")
        
        training_time = (datetime.now() - start_time).total_seconds()
        print(f"\n    Base models training time: {training_time:.1f}s")
        
        # VOTING ENSEMBLE (simpler than stacking to avoid overfitting)
        print(f"\n  [BUILDING VOTING ENSEMBLE]")
        
        voting_model = VotingClassifier(
            estimators=[
                ('xgb', xgb_model),
                ('lgb', lgb_model),
                ('cat', cat_model),
                ('rf', rf_model),
                ('et', et_model)
            ],
            voting='soft',
            weights=[3, 3, 3, 2, 2],
            n_jobs=1  # Avoid memory issues
        )
        
        print(f"    Fitting voting ensemble...")
        voting_model.fit(X_train_balanced, y_train_balanced)
        print(f"    ✓ Voting ensemble created (5 models)")
        
        # Evaluation
        print(f"\n" + "="*80)
        print(" EVALUATION")
        print("="*80)
        
        X_test_clean = np.nan_to_num(X_test.values, nan=0.0, posinf=0.0, neginf=0.0)
        X_test_clean = pd.DataFrame(X_test_clean, columns=self.feature_names)
        
        y_pred = voting_model.predict(X_test_clean)
        y_train_pred = voting_model.predict(X_train_balanced)
        
        train_acc = accuracy_score(y_train_balanced, y_train_pred)
        test_acc = accuracy_score(y_test, y_pred)
        gap = train_acc - test_acc
        
        print(f"\n  Training Accuracy:  {train_acc*100:.2f}%")
        print(f"  Test Accuracy:      {test_acc*100:.2f}%")
        print(f"  Gap:                {gap*100:.2f}%")
        
        print(f"\n  F1-Score (Macro):   {f1_score(y_test, y_pred, average='macro')*100:.2f}%")
        print(f"  F1-Score (Weighted):{f1_score(y_test, y_pred, average='weighted')*100:.2f}%")
        print(f"  Precision:          {precision_score(y_test, y_pred, average='weighted')*100:.2f}%")
        print(f"  Recall:             {recall_score(y_test, y_pred, average='weighted')*100:.2f}%")
        print(f"  Cohen's Kappa:      {cohen_kappa_score(y_test, y_pred):.4f}")
        print(f"  MCC:                {matthews_corrcoef(y_test, y_pred):.4f}")
        
        print(f"\n  [CONFUSION MATRIX]")
        cm = confusion_matrix(y_test, y_pred)
        classes = [self.inv_target_mapping[i] for i in range(len(self.inv_target_mapping))]
        
        for i, cls in enumerate(classes):
            print(f"    {cls}: {cm[i]}")
        
        # Per-class metrics
        print(f"\n  [PER-CLASS ACCURACY]")
        for i, cls in enumerate(classes):
            mask = y_test == i
            if mask.sum() > 0:
                cls_acc = (y_pred[mask] == i).sum() / mask.sum() * 100
                print(f"    {cls}: {cls_acc:.1f}%")
        
        print(f"\n" + "="*80)
        print(" SUMMARY")
        print("="*80)
        
        if test_acc >= target_accuracy:
            print(f"\n  ✅ TEST TARGET ACHIEVED! ({test_acc*100:.2f}% ≥ {target_accuracy*100:.0f}%)")
        else:
            print(f"\n  📊 Test Accuracy: {test_acc*100:.2f}% (target: {target_accuracy*100:.0f}%)")
        
        if gap <= 0.03:
            print(f"  ✅ EXCELLENT! No overfitting (gap: {gap*100:.2f}% ≤ 3%)")
        elif gap <= 0.05:
            print(f"  ✅ GOOD! Minimal overfitting (gap: {gap*100:.2f}% ≤ 5%)")
        elif gap <= 0.08:
            print(f"  ⚠ Acceptable overfitting (gap: {gap*100:.2f}% ≤ 8%)")
        else:
            print(f"  ⚠ High overfitting (gap: {gap*100:.2f}%)")
        
        if test_acc >= 0.93 and gap <= 0.05:
            print(f"  🎯 PERFECT MODEL! High accuracy with low overfitting!")
        
        print()
        
        self.model = voting_model
        
        return voting_model

    def save_model(self, path='credit_model_ultra.pkl'):
        """Save model"""
        with open(path, 'wb') as f:
            pickle.dump({
                'model': self.model,
                'le_dict': self.le_dict,
                'le_target': self.le_target,
                'scaler': self.scaler,
                'feature_names': self.feature_names,
                'scaled_features': self.scaled_features,  # Save scaled features list
                'inv_target_mapping': self.inv_target_mapping
            }, f)
        print(f"✓ Model saved: {path}\n")

    def load_model(self, path='credit_model_ultra.pkl'):
        """Load model"""
        with open(path, 'rb') as f:
            data = pickle.load(f)
        
        self.model = data['model']
        self.le_dict = data['le_dict']
        self.le_target = data['le_target']
        self.scaler = data['scaler']
        self.feature_names = data['feature_names']
        self.scaled_features = data.get('scaled_features', self.feature_names)  # Backward compatibility
        self.inv_target_mapping = data['inv_target_mapping']
        
        print(f"✓ Model loaded: {path}\n")

    def get_user_input(self):
        """Get user input"""
        print("="*70)
        print(" INPUT DATA PEMINJAM")
        print("="*70)
        
        data = {}
        
        data['Umur'] = int(input("\n  Umur: "))
        
        print("  Status Pernikahan: 1.Lajang 2.Menikah 3.Cerai")
        status = input("    Pilih: ")
        data['Status_Pernikahan'] = {'1':'Lajang','2':'Menikah','3':'Cerai'}.get(status, 'Lajang')
        
        data['Jumlah_Tanggungan'] = int(input("  Jumlah Tanggungan: "))
        
        print("  Pendidikan: 1.SD 2.SMP 3.SMA 4.Diploma 5.S1 6.S2 7.S3")
        edu = input("    Pilih: ")
        data['Pendidikan'] = {'1':'SD','2':'SMP','3':'SMA','4':'Diploma','5':'S1','6':'S2','7':'S3'}.get(edu, 'SMA')
        
        print("  Pekerjaan: 1.PNS 2.Karyawan 3.Wiraswasta 4.Buruh 5.Petani 6.Lainnya")
        job = input("    Pilih: ")
        data['Jenis_Pekerjaan'] = {'1':'PNS','2':'Karyawan','3':'Wiraswasta','4':'Buruh','5':'Petani','6':'Lainnya'}.get(job, 'Karyawan')
        
        data['Lama_Bekerja_Tahun'] = int(input("  Lama Bekerja (tahun): "))
        data['Pendapatan_Bulanan'] = self.parse_money_input(input("  Pendapatan Bulanan: "))
        data['Pengeluaran_Bulanan'] = self.parse_money_input(input("  Pengeluaran Bulanan: "))
        data['Skor_Kredit'] = int(input("  Skor Kredit (0-100, default 50): ") or "50")
        data['Jumlah_Pinjaman'] = self.parse_money_input(input("  Jumlah Pinjaman: "))
        data['Lama_Tenor_Bulan'] = int(input("  Tenor (bulan): "))
        
        print("  Riwayat Tunggakan: 1.Tidak 2.Pernah 3.Sering")
        tung = input("    Pilih (default 1): ") or '1'
        data['Riwayat_Tunggakan'] = {'1':'Tidak','2':'Pernah','3':'Sering'}.get(tung, 'Tidak')
        
        print("  Jaminan: 1.Ada 2.Tidak")
        jam = input("    Pilih (default 2): ") or '2'
        data['Jaminan'] = 'Ada' if jam == '1' else 'Tidak Ada'
        
        print("  Tujuan: 1.Modal Usaha 2.Darurat 3.Konsumtif 4.Pendidikan 5.Renovasi 6.Lainnya")
        tuj = input("    Pilih (default 1): ") or '1'
        data['Tujuan_Pinjaman'] = {'1':'Modal Usaha','2':'Darurat','3':'Konsumtif','4':'Pendidikan','5':'Renovasi Rumah','6':'Lainnya'}.get(tuj, 'Modal Usaha')
        
        print("  Status Tempat Tinggal: 1.Milik Sendiri 2.Sewa 3.Kontrak 4.Milik Orang Tua")
        tempat = input("    Pilih (default 1): ") or '1'
        data['Status_Tempat_Tinggal'] = {'1':'Milik Sendiri','2':'Sewa','3':'Kontrak','4':'Milik Orang Tua'}.get(tempat, 'Milik Sendiri')
        
        return data

    def predict_kelayakan(self, input_data):
        """Predict dengan feature recreation yang PERSIS SAMA dengan training"""
        
        # STEP 1: Create DataFrame with BASE features
        df = pd.DataFrame([input_data])
        
        # STEP 2: Extract numeric values for feature engineering
        pendapatan = max(input_data.get('Pendapatan_Bulanan', 5000000), 1)
        pengeluaran = max(input_data.get('Pengeluaran_Bulanan', 3000000), 1)
        pinjaman = max(input_data.get('Jumlah_Pinjaman', 10000000), 1)
        tenor = max(input_data.get('Lama_Tenor_Bulan', 12), 1)
        umur = max(input_data.get('Umur', 30), 18)
        lama_kerja = max(input_data.get('Lama_Bekerja_Tahun', 1), 0)
        skor = max(min(input_data.get('Skor_Kredit', 50), 100), 0)
        tanggungan = max(input_data.get('Jumlah_Tanggungan', 0), 0)
        
        # STEP 3: Create engineered features (SAME ORDER AS TRAINING!)
        angsuran = pinjaman / tenor
        pendapatan_bersih = pendapatan - pengeluaran
        
        df['Angsuran_Bulanan'] = angsuran
        df['DTI_Ratio'] = min(angsuran / pendapatan, 2)
        df['Pendapatan_Bersih'] = pendapatan_bersih
        df['Rasio_Pengeluaran'] = min(pengeluaran / pendapatan, 2)
        df['Kemampuan_Bayar'] = pendapatan_bersih - angsuran
        df['Loan_Income_Ratio'] = min(pinjaman / (pendapatan * 12), 10)
        df['Payment_to_Income'] = angsuran / pendapatan
        df['Disposable_Income_Ratio'] = pendapatan_bersih / pendapatan
        df['Saving_Rate'] = (pendapatan - pengeluaran) / pendapatan
        df['Expense_Coverage'] = pendapatan / pengeluaran
        
        df['Credit_Quality'] = skor / 100
        df['Credit_Risk'] = (100 - skor) / 100
        df['Credit_Score_Squared'] = (skor / 100) ** 2
        df['Credit_Score_Cubed'] = (skor / 100) ** 3
        df['Adjusted_Credit_Score'] = (skor / 100) * (1 - min(df['DTI_Ratio'].iloc[0], 1))
        df['Credit_Excellent'] = int(skor >= 80)
        df['Credit_Good'] = int((skor >= 60) and (skor < 80))
        df['Credit_Poor'] = int(skor < 40)
        
        df['Work_Stability'] = min(lama_kerja / max(umur - 17, 1), 1)
        df['Age_Risk'] = min(abs(umur - 35) / 35, 2)
        df['Age_Maturity'] = min((umur - 25) / 40, 1)
        df['Career_Progress'] = lama_kerja / umur
        df['Prime_Age'] = int((umur >= 30) and (umur <= 50))
        df['Stable_Job'] = int(lama_kerja >= 3)
        
        df['Per_Capita_Income'] = pendapatan / (tanggungan + 1)
        df['Burden_Ratio'] = tanggungan / max(umur / 10, 1)
        df['Family_Load'] = tanggungan * df['Rasio_Pengeluaran'].iloc[0]
        df['No_Dependents'] = int(tanggungan == 0)
        df['High_Dependents'] = int(tanggungan >= 3)
        
        df['Tenor_Risk'] = tenor / 60
        df['Monthly_Burden'] = angsuran / pendapatan * 100
        df['Loan_Size_Ratio'] = pinjaman / 50_000_000
        df['Short_Tenor'] = int(tenor <= 12)
        df['Long_Tenor'] = int(tenor >= 36)
        df['Small_Loan'] = int(pinjaman < 10_000_000)
        df['Large_Loan'] = int(pinjaman > 30_000_000)
        
        df['DTI_x_Credit'] = df['DTI_Ratio'].iloc[0] * df['Credit_Risk'].iloc[0]
        df['DTI_x_Credit_Quality'] = df['DTI_Ratio'].iloc[0] * (1 - df['Credit_Quality'].iloc[0])
        df['Income_x_Stability'] = (pendapatan / 10_000_000) * df['Work_Stability'].iloc[0]
        df['Income_x_Credit'] = (pendapatan / 10_000_000) * df['Credit_Quality'].iloc[0]
        df['Loan_x_Tenor'] = (pinjaman / 10_000_000) * (tenor / 60)
        df['Age_x_Credit'] = (umur / 100) * df['Credit_Quality'].iloc[0]
        df['Credit_x_DTI'] = df['Credit_Quality'].iloc[0] * (1 - min(df['DTI_Ratio'].iloc[0], 1))
        df['Saving_x_Credit'] = df['Saving_Rate'].iloc[0] * df['Credit_Quality'].iloc[0]
        df['Stability_x_Credit'] = df['Work_Stability'].iloc[0] * df['Credit_Quality'].iloc[0]
        
        df['DTI_Credit_Stability'] = df['DTI_Ratio'].iloc[0] * df['Credit_Risk'].iloc[0] * (1 - df['Work_Stability'].iloc[0])
        df['Income_Credit_Age'] = (pendapatan / 10_000_000) * df['Credit_Quality'].iloc[0] * df['Age_Maturity'].iloc[0]
        
        df['DTI_Squared'] = df['DTI_Ratio'].iloc[0] ** 2
        df['DTI_Cubed'] = df['DTI_Ratio'].iloc[0] ** 3
        df['Income_Log'] = np.log1p(pendapatan / 1_000_000)
        df['Income_Sqrt'] = np.sqrt(pendapatan / 1_000_000)
        df['Loan_Log'] = np.log1p(pinjaman / 1_000_000)
        df['Loan_Sqrt'] = np.sqrt(pinjaman / 1_000_000)
        df['Age_Squared'] = (umur / 100) ** 2
        df['Tenor_Log'] = np.log1p(tenor)
        
        df['Financial_Health_Score'] = (
            df['Credit_Quality'].iloc[0] * 0.30 +
            (1 - min(df['DTI_Ratio'].iloc[0], 1)) * 0.25 +
            max(min(df['Saving_Rate'].iloc[0], 1), 0) * 0.20 +
            df['Work_Stability'].iloc[0] * 0.15 +
            df['Age_Maturity'].iloc[0] * 0.10
        )
        
        df['Risk_Score'] = (
            df['Credit_Risk'].iloc[0] * 0.30 +
            min(df['DTI_Ratio'].iloc[0], 1) * 0.25 +
            df['Age_Risk'].iloc[0] * 0.15 +
            df['Tenor_Risk'].iloc[0] * 0.15 +
            (tanggungan / 10) * 0.15
        )
        
        df['Approval_Score'] = (
            df['Credit_Quality'].iloc[0] * 0.35 +
            (1 - min(df['DTI_Ratio'].iloc[0], 1)) * 0.30 +
            df['Work_Stability'].iloc[0] * 0.20 +
            (1 - df['Tenor_Risk'].iloc[0]) * 0.15
        )
        
        df['DTI_Acceptable'] = int(df['DTI_Ratio'].iloc[0] <= 0.35)
        df['DTI_Critical'] = int(df['DTI_Ratio'].iloc[0] > 0.50)
        df['Positive_Savings'] = int(df['Saving_Rate'].iloc[0] > 0)
        df['Deficit'] = int(df['Saving_Rate'].iloc[0] < 0)
        df['High_Income'] = int(pendapatan >= 10_000_000)
        df['Low_Income'] = int(pendapatan < 3_000_000)
        
        # STEP 4: Encode categorical features AFTER creating numeric features
        for col in self.le_dict.keys():
            if col in df.columns:
                try:
                    val = str(df[col].iloc[0])
                    if val in self.le_dict[col].classes_:
                        df[col] = self.le_dict[col].transform([val])[0]
                    else:
                        df[col] = 0
                except:
                    df[col] = 0
            else:
                df[col] = 0
        
        # STEP 5: Ensure all features match feature_names order
        for col in self.feature_names:
            if col not in df.columns:
                df[col] = 0
        
        df = df[self.feature_names]
        
        # STEP 6: Scale ONLY the features that were scaled during training
        df_to_scale = df[self.scaled_features].copy()
        df_scaled_values = self.scaler.transform(df_to_scale.values)
        df_scaled = df.copy()
        df_scaled[self.scaled_features] = df_scaled_values
        
        # STEP 7: Predict
        X_input = np.nan_to_num(df_scaled.values, nan=0.0)
        pred = self.model.predict(X_input)[0]
        pred_proba = self.model.predict_proba(X_input)[0]
        
        kelayakan = self.inv_target_mapping[pred]
        confidence = pred_proba[pred] * 100
        
        # Risk analysis
        dti = angsuran / pendapatan
        risiko_dti = 'Rendah' if dti <= 0.25 else 'Sedang' if dti <= 0.35 else 'Tinggi'
        risiko_skor = 'Rendah' if skor >= 70 else 'Sedang' if skor >= 50 else 'Tinggi'
        risiko_jaminan = 'Rendah' if input_data.get('Jaminan') == 'Ada' else 'Tinggi'
        
        fh_score = (
            (skor / 100) * 0.30 +
            (1 - min(dti, 1)) * 0.25 +
            max(min((pendapatan - pengeluaran) / pendapatan, 1), 0) * 0.20 +
            min(lama_kerja / max(umur - 17, 1), 1) * 0.15 +
            min((umur - 25) / 40, 1) * 0.10
        )
        
        risk_score = (
            ((100 - skor) / 100) * 0.30 +
            min(dti, 1) * 0.25 +
            min(abs(umur - 35) / 35, 2) * 0.15 +
            (tenor / 60) * 0.15 +
            (tanggungan / 10) * 0.15
        )
        
        return {
            'Kelayakan': kelayakan,
            'Confidence': f"{confidence:.1f}%",
            'Angsuran': f"Rp {angsuran:,.0f}",
            'DTI': f"{dti*100:.1f}%",
            'Kemampuan_Bayar': f"Rp {pendapatan - pengeluaran - angsuran:,.0f}",
            'Risiko_DTI': risiko_dti,
            'Risiko_Skor': risiko_skor,
            'Risiko_Jaminan': risiko_jaminan,
            'Financial_Health': f"{fh_score*100:.1f}%",
            'Risk_Level': f"{risk_score*100:.1f}%",
            'Rekomendasi': '✅ DISETUJUI' if kelayakan == 'Layak' else '⚠️ REVIEW' if kelayakan == 'Dipertimbangkan' else '❌ DITOLAK'
        }


def main():
    """Main"""
    print("\n" + "="*80)
    print(" CREDIT SCORING SYSTEM - COMPLETELY FIXED VERSION")
    print(" Target: 95%+ Test Accuracy | Gap <5% | No Memory Issues")
    print("="*80)
    
    model = CreditScoringModelUltra(random_state=42)
    model_path = 'credit_model_ultra.pkl'
    
    if os.path.exists(model_path):
        print(f"\n✓ Model found: {model_path}")
        choice = input("Load existing model? (y/n): ").lower()
        
        if choice == 'y':
            model.load_model(model_path)
        else:
            X, y = model.load_and_preprocess_data()
            model.train_model(X, y, target_accuracy=0.95)
            model.save_model(model_path)
    else:
        print(f"\n⚠ Model not found. Training...")
        X, y = model.load_and_preprocess_data()
        model.train_model(X, y, target_accuracy=0.95)
        model.save_model(model_path)
    
    # Prediction loop
    while True:
        print("\n" + "="*80)
        print(" ANALISIS KELAYAKAN")
        print("="*80)
        
        try:
            data = model.get_user_input()
            result = model.predict_kelayakan(data)
            
            print(f"\n" + "="*80)
            print(" HASIL ANALISIS")
            print("="*80)
            
            print(f"\n  Status: {result['Kelayakan']} ({result['Confidence']})")
            print(f"  {result['Rekomendasi']}")
            
            print(f"\n  Angsuran Bulanan: {result['Angsuran']}")
            print(f"  DTI Ratio: {result['DTI']}")
            print(f"  Kemampuan Bayar: {result['Kemampuan_Bayar']}")
            
            print(f"\n  Financial Health Score: {result['Financial_Health']}")
            print(f"  Risk Level: {result['Risk_Level']}")
            
            print(f"\n  Risiko DTI: {result['Risiko_DTI']}")
            print(f"  Risiko Skor Kredit: {result['Risiko_Skor']}")
            print(f"  Risiko Jaminan: {result['Risiko_Jaminan']}")
            
        except KeyboardInterrupt:
            print("\n\nProgram selesai!")
            break
        except Exception as e:
            print(f"\n❌ Error: {e}")
            import traceback
            traceback.print_exc()
        
        cont = input("\n  Analisis lagi? (y/n): ").lower()
        if cont != 'y':
            print("\nTerima kasih telah menggunakan sistem ini!")
            break
if __name__ == "__main__":
    main()