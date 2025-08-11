# Endpoints

Base URL (local): `http://127.0.0.1:8000`

- Interactive docs: `http://127.0.0.1:8000/docs`
- OpenAPI JSON: `http://127.0.0.1:8000/openapi.json`

---

## GET /summary
Returns basic metadata about the loaded dataset.

- Response model: `SummaryResponse`
- Status codes: `200 OK`

Example request
```bash
curl -s http://127.0.0.1:8000/summary | jq .
```

Example response
```json
{
  "total_rows": 8,
  "columns": ["country", "population", "gdp", "life_expectancy", "year"]
}
```

Python example
```python
import requests
resp = requests.get("http://127.0.0.1:8000/summary")
resp.raise_for_status()
print(resp.json())
```

---

## GET /country/{country_code}
Returns all rows for a specific country code (case-insensitive in the API; internally converted to uppercase).

- Path params:
  - `country_code` (string): e.g., `USA`, `CAN`, `MEX`, `BRA`
- Response model: `CountryDataResponse`
- Status codes: `200 OK`, `404 Not Found` when the country does not exist

Example request
```bash
curl -s http://127.0.0.1:8000/country/CAN | jq .
```

Example response
```json
{
  "country": "CAN",
  "rows": [
    {
      "country": "CAN",
      "population": 38005238,
      "gdp": 2050990,
      "life_expectancy": 81.7,
      "year": 2022
    },
    {
      "country": "CAN",
      "population": 38246108,
      "gdp": 2153400,
      "life_expectancy": 82.0,
      "year": 2023
    }
  ]
}
```

Python example
```python
import requests
resp = requests.get("http://127.0.0.1:8000/country/CAN")
if resp.status_code == 404:
    print("Country not found")
else:
    print(resp.json())
```

---

## GET /top/{metric}
Returns the top N countries ranked by the sum of a numeric metric.

- Path params:
  - `metric` (string): one of the dataset columns. Recommended: `population`, `gdp`, `life_expectancy`.
- Query params:
  - `n` (int, default: 10): number of countries to return
- Response: list of objects with `country` and the aggregated `metric` value
- Status codes: `200 OK` (note: if metric is invalid, a JSON error is returned with key `error`)

Example request
```bash
curl -s "http://127.0.0.1:8000/top/gdp?n=3" | jq .
```

Example response
```json
[
  { "country": "USA", "gdp": 43545538 },
  { "country": "CAN", "gdp": 4204390 },
  { "country": "BRA", "gdp": 2964832 }
]
```

Invalid metric example
```bash
curl -s "http://127.0.0.1:8000/top/unknown_metric?n=3" | jq .
```
returns
```json
{ "error": "Métrica 'unknown_metric' no encontrada" }
```

Python example
```python
import requests
params = {"n": 3}
resp = requests.get("http://127.0.0.1:8000/top/gdp", params=params)
print(resp.json())
```

Notes
- Aggregation used: sum per `country`, then top N by descending order.
- Dataset columns: `country`, `population`, `gdp`, `life_expectancy`, `year`.