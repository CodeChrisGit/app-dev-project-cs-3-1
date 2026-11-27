"""Price estimator service for Pinoy DreamHome Finder.

Loads the trained scikit-learn model (phil_price_model.pkl) and exposes:
  GET  /health
  POST /predict   {"bedrooms": 3, "bathrooms": 2, "floor_area": 90, "land_area": 100}

The model is a LinearRegression trained on log(price), so the prediction
is converted back to pesos with exp().
"""
import os
from pathlib import Path

import joblib
import numpy as np
import pandas as pd
from flask import Flask, jsonify, request

MODEL_PATH = Path(os.getenv("MODEL_PATH", Path(__file__).parent / "models" / "phil_price_model.pkl"))
model = joblib.load(MODEL_PATH)

# Column names exactly as used when the model was trained:
# ['Bedrooms', 'Bathrooms', 'Floor Area', 'Land Area']
FEATURES = list(model.feature_names_in_)

app = Flask(__name__)


def read_number(data, key, low, high, as_int=False):
    """Read one input, check it is a number inside the allowed range."""
    if key not in data:
        raise ValueError(f"'{key}' is required.")
    try:
        value = int(data[key]) if as_int else float(data[key])
    except (TypeError, ValueError):
        raise ValueError(f"'{key}' must be a number.")
    if not low <= value <= high:
        raise ValueError(f"'{key}' must be between {low} and {high}.")
    return value


@app.get("/health")
def health():
    return jsonify(status="ok", features=FEATURES)


@app.post("/predict")
def predict():
    data = request.get_json(silent=True) or {}
    try:
        bedrooms = read_number(data, "bedrooms", 1, 20, as_int=True)
        bathrooms = read_number(data, "bathrooms", 1, 20, as_int=True)
        floor_area = read_number(data, "floor_area", 10, 2000)
        land_area = read_number(data, "land_area", 0, 10000)
    except ValueError as error:
        return jsonify(message=str(error)), 422

    row = pd.DataFrame([[bedrooms, bathrooms, floor_area, land_area]], columns=FEATURES)
    log_price = float(model.predict(row)[0])
    price = float(np.exp(log_price))  # the model predicts log(price)

    return jsonify(estimated_price=round(price, -3))


if __name__ == "__main__":
    app.run(host="127.0.0.1", port=int(os.getenv("PORT", 5000)))
