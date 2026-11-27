# ML price estimator (Python / Flask)

## Setup (Windows PowerShell)
```
cd ml-service
python -m venv venv
venv\Scripts\activate
pip install -r requirements.txt
python app.py
```
The service runs at http://127.0.0.1:5000

## Test
Open http://127.0.0.1:5000/health in the browser, then test the prediction in a second terminal:
```
curl -X POST http://127.0.0.1:5000/predict -H "Content-Type: application/json" -d "{\"bedrooms\":3,\"bathrooms\":2,\"floor_area\":90,\"land_area\":100}"
```
Expected: about 6,100,000 for these inputs.

## Notes
- The model is a scikit-learn LinearRegression with the inputs Bedrooms, Bathrooms, Floor Area and Land Area.
- It predicts log(price), so app.py converts the result back to pesos with exp().
- If scikit-learn warns about a version mismatch when loading, install the same scikit-learn version that was used to train and save the model.
