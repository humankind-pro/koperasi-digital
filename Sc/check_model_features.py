import pickle

with open('credit_model_ultra.pkl', 'rb') as f:
    model_data = pickle.load(f)

feature_names = model_data['feature_names']

print(f"Total features in model: {len(feature_names)}\n")
print("Feature names:")
for i, feature in enumerate(feature_names, 1):
    print(f"{i:3}. {feature}")

# Save to text file
with open('model_features.txt', 'w') as f:
    for feature in feature_names:
        f.write(feature + '\n')

print(f"\n✅ Feature names saved to: model_features.txt")