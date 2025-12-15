import sys
import json
import pickle
import pandas as pd
import numpy as np

def load_model(model_path):
    """Memuat model dari file pickle."""
    try:
        with open(model_path, 'rb') as f:
            model_components = pickle.load(f)
        print(f"✅ Model loaded from {model_path}", file=sys.stderr)
        return model_components
    except FileNotFoundError:
        print(json.dumps({"error": f"Model file not found: {model_path}"}))
        sys.exit(1)
    except Exception as e:
        print(json.dumps({"error": f"Error loading model: {e}"}))
        sys.exit(1)

def recreate_engineered_features(df):
    """
    Membuat ulang fitur-fitur yang digunakan saat pelatihan model.
    PERSIS seperti yang ada di model (87 features).
    """
    # Kolom base yang diperlukan
    required_cols = [
        'Umur', 'Status_Pernikahan', 'Jumlah_Tanggungan', 'Pendidikan', 'Jenis_Pekerjaan',
        'Lama_Bekerja_Tahun', 'Pendapatan_Bulanan', 'Pengeluaran_Bulanan', 'Skor_Kredit',
        'Jumlah_Pinjaman', 'Lama_Tenor_Bulan', 'Riwayat_Tunggakan', 'Jaminan',
        'Tujuan_Pinjaman', 'Status_Tempat_Tinggal'
    ]

    for col in required_cols:
        if col not in df.columns:
            print(json.dumps({"error": f"Required column '{col}' is missing from input data."}))
            sys.exit(1)

    # Tambahkan kolom tambahan yang ada di model tapi mungkin tidak ada di input
    extra_cols = {
        'Tanggungan_Kredit_Lain': 0,
        'Tabungan_Aset': 0,
        'Nilai_Jaminan': 0,
        'Lama_Anggota_Tahun': 0,
        'Riwayat_Pinjam': 'Tidak',
        'Jumlah_Pinjaman_Diajukan': 0,
        'Durasi_Pinjaman_Bulan': 0,
        'Rasio_DebttoIncome': 0
    }
    
    for col, default_val in extra_cols.items():
        if col not in df.columns:
            df[col] = default_val

    # Konversi ke numerik jika perlu
    df['Pendapatan_Bulanan'] = pd.to_numeric(df['Pendapatan_Bulanan'], errors='coerce').fillna(0)
    df['Pengeluaran_Bulanan'] = pd.to_numeric(df['Pengeluaran_Bulanan'], errors='coerce').fillna(0)
    df['Jumlah_Pinjaman'] = pd.to_numeric(df['Jumlah_Pinjaman'], errors='coerce').fillna(0)
    df['Lama_Bekerja_Tahun'] = pd.to_numeric(df['Lama_Bekerja_Tahun'], errors='coerce').fillna(0)
    df['Skor_Kredit'] = pd.to_numeric(df['Skor_Kredit'], errors='coerce').fillna(50)
    df['Umur'] = pd.to_numeric(df['Umur'], errors='coerce').fillna(30)
    df['Jumlah_Tanggungan'] = pd.to_numeric(df['Jumlah_Tanggungan'], errors='coerce').fillna(0)
    df['Lama_Tenor_Bulan'] = pd.to_numeric(df['Lama_Tenor_Bulan'], errors='coerce').fillna(12)
    
    # Kolom tambahan numerik
    df['Tanggungan_Kredit_Lain'] = pd.to_numeric(df['Tanggungan_Kredit_Lain'], errors='coerce').fillna(0)
    df['Tabungan_Aset'] = pd.to_numeric(df['Tabungan_Aset'], errors='coerce').fillna(0)
    df['Nilai_Jaminan'] = pd.to_numeric(df['Nilai_Jaminan'], errors='coerce').fillna(0)
    df['Lama_Anggota_Tahun'] = pd.to_numeric(df['Lama_Anggota_Tahun'], errors='coerce').fillna(0)
    df['Jumlah_Pinjaman_Diajukan'] = pd.to_numeric(df['Jumlah_Pinjaman_Diajukan'], errors='coerce').fillna(0)
    df['Durasi_Pinjaman_Bulan'] = pd.to_numeric(df['Durasi_Pinjaman_Bulan'], errors='coerce').fillna(0)
    df['Rasio_DebttoIncome'] = pd.to_numeric(df['Rasio_DebttoIncome'], errors='coerce').fillna(0)

    # Pastikan tidak ada nilai NaN yang tersisa
    df.fillna(0, inplace=True)

    # Ambil nilai-nilai numerik untuk perhitungan fitur
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

    print("✅ Engineered features recreated.", file=sys.stderr)
    return df

def main():
    if len(sys.argv) != 3: 
        print(json.dumps({"error": "Usage: python predict_script.py <model_path.pkl> <input_json_file>"}))
        sys.exit(1)

    model_path = sys.argv[1]
    input_file = sys.argv[2]

    try:
        # 1. Load input data
        print(f"🔍 Loading input data from {input_file}...", file=sys.stderr)
        with open(input_file, 'r', encoding='utf-8-sig') as f:
            input_data = json.load(f)

        # 2. Load the trained model and its components
        print(f"🔍 Loading model from {model_path}...", file=sys.stderr)
        model_components = load_model(model_path)
        model = model_components['model']
        le_dict = model_components['le_dict']
        le_target = model_components['le_target']
        scaler = model_components['scaler']
        feature_names = model_components['feature_names']
        inv_target_mapping = model_components['inv_target_mapping']

        print(f"📊 Model expects {len(feature_names)} features", file=sys.stderr)

        # 3. Prepare DataFrame
        df = pd.DataFrame([input_data])

        # 4. Recreate features (ini akan menambahkan kolom tambahan yang diperlukan)
        df = recreate_engineered_features(df)

        # 5. Encode categorical features BEFORE scaling
        print("🔤 Encoding categorical features...", file=sys.stderr)
        for col, encoder in le_dict.items():
            if col in df.columns:
                try:
                    val = str(df[col].iloc[0])
                    if val in encoder.classes_:
                        df[col] = encoder.transform([val])[0]
                    else:
                        print(f"⚠️ Unknown category '{val}' for column '{col}', using default (index 0).", file=sys.stderr)
                        df[col] = 0
                except Exception as e:
                    print(f"⚠️ Error encoding column '{col}': {e}", file=sys.stderr)
                    df[col] = 0
            else:
                # Kolom kategorikal tidak ada di input, set ke default
                df[col] = 0

        # 6. Ensure all features are present in EXACT order
        print("📋 Ensuring feature alignment...", file=sys.stderr)
        for col in feature_names:
            if col not in df.columns:
                print(f"⚠️ Missing feature '{col}', setting to 0", file=sys.stderr)
                df[col] = 0
        
        # Reorder to match feature_names EXACTLY
        df = df[feature_names]
        print(f"✅ Features aligned: {len(df.columns)} columns", file=sys.stderr)

        # 7. Scale ONLY numerical features (scaler was fitted on 62 features, not all 87)
        print("⚖️ Scaling numerical features...", file=sys.stderr)
        
        # Get the feature names that scaler was trained on
        if hasattr(scaler, 'feature_names_in_'):
            scaled_feature_names = list(scaler.feature_names_in_)
            print(f"   Scaler expects {len(scaled_feature_names)} features", file=sys.stderr)
        else:
            # Fallback: scaler expects 62 numerical features
            # These are all non-categorical, non-binary features
            scaled_feature_names = [col for col in feature_names 
                                   if col not in le_dict.keys() 
                                   and not col.endswith(('_Excellent', '_Good', '_Poor', '_Age', '_Job', 
                                                         '_Dependents', '_Tenor', '_Loan', '_Acceptable', 
                                                         '_Critical', '_Savings', 'Deficit', '_Income'))]
        
        # Extract only the columns that need scaling
        df_to_scale = df[scaled_feature_names]
        
        # Scale those columns
        df_scaled_values = scaler.transform(df_to_scale.values)
        df_scaled_subset = pd.DataFrame(df_scaled_values, columns=scaled_feature_names)
        
        # Replace scaled columns in the original dataframe
        for col in scaled_feature_names:
            df[col] = df_scaled_subset[col].values
        
        # Now df has scaled numerical features + unscaled categorical/binary features
        df_scaled = df
        print(f"✅ Scaled {len(scaled_feature_names)} numerical features", file=sys.stderr)

        # 8. Prepare input for prediction
        X_input = np.nan_to_num(df_scaled.values, nan=0.0, posinf=0.0, neginf=0.0)

        # 9. Predict
        print("🤖 Running prediction...", file=sys.stderr)
        pred = model.predict(X_input)[0]
        pred_proba = model.predict_proba(X_input)[0]

        # 10. Map prediction back to label
        kelayakan = inv_target_mapping[pred]
        confidence = float(pred_proba[pred])

        result = {
            "success": True,
            "prediction": kelayakan,
            "confidence": confidence
        }

        print(json.dumps(result))

    except Exception as e:
        error_result = {
            "success": False,
            "error": f"Error during prediction: {str(e)}"
        }
        print(json.dumps(error_result))
        import traceback
        print(f"Full traceback:\n{traceback.format_exc()}", file=sys.stderr)


if __name__ == "__main__":
    main()