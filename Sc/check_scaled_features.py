import pickle

with open('credit_model_ultra.pkl', 'rb') as f:
    model_data = pickle.load(f)

scaler = model_data['scaler']
feature_names = model_data['feature_names']

print(f"Total features in model: {len(feature_names)}")
print(f"Scaler expects: {scaler.n_features_in_} features")
print(f"\nDifference: {len(feature_names)} - {scaler.n_features_in_} = {len(feature_names) - scaler.n_features_in_} columns NOT scaled")

# Try to get feature names that scaler was trained on
if hasattr(scaler, 'feature_names_in_'):
    print(f"\nScaler was fitted with these {len(scaler.feature_names_in_)} features:")
    for i, fname in enumerate(scaler.feature_names_in_, 1):
        print(f"{i:3}. {fname}")
    
    # Find which features are NOT scaled
    scaled_features = set(scaler.feature_names_in_)
    all_features = set(feature_names)
    not_scaled = all_features - scaled_features
    
    print(f"\n\n{len(not_scaled)} Features NOT scaled (categorical):")
    for fname in sorted(not_scaled):
        idx = feature_names.index(fname) + 1
        print(f"{idx:3}. {fname}")
else:
    print("\nScaler does not have feature_names_in_ attribute")
    print("Attempting to identify categorical columns from le_dict...")
    
    le_dict = model_data['le_dict']
    print(f"\nCategorical columns (from LabelEncoders): {len(le_dict)}")
    for col in le_dict.keys():
        if col in feature_names:
            idx = feature_names.index(col) + 1
            print(f"{idx:3}. {col}")