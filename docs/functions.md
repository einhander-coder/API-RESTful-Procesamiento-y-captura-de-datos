# Internal Python Functions

Module: `app.data_processing`

## load_data() -> pandas.DataFrame
Loads and cleans the dataset from `data/raw_data.csv`.

Behavior
- Reads CSV with `pandas.read_csv`.
- Drops rows containing any missing values (`dropna(inplace=True)`).
- Uppercases the `country` column.
- Returns the cleaned `DataFrame`.

Example
```python
from app.data_processing import load_data

df = load_data()
print(df.head())
```

---

## get_summary(df: pandas.DataFrame) -> dict
Returns basic metadata for the given `DataFrame`.

Behavior
- Calculates `total_rows` as `len(df)`.
- Returns list of column names as `columns`.

Example
```python
from app.data_processing import load_data, get_summary

df = load_data()
print(get_summary(df))
# {"total_rows": 8, "columns": ["country", "population", "gdp", "life_expectancy", "year"]}
```

---

## get_country_data(df: pandas.DataFrame, country_code: str) -> dict | None
Returns all rows for a specific country code or `None` if not found.

Behavior
- Filters rows where `country` equals the provided `country_code` (must be uppercase).
- Returns `{"country": country_code, "rows": [...]}` or `None` if there are no rows.

Example
```python
from app.data_processing import load_data, get_country_data

df = load_data()
print(get_country_data(df, "CAN"))
```

---

## get_top_n(df: pandas.DataFrame, metric: str, n: int) -> list | dict
Returns top N countries by the sum of a numeric `metric`.

Behavior
- If `metric` not present in `df.columns`, returns `{ "error": "Métrica '<metric>' no encontrada" }`.
- Groups by `country`, sums the metric, sorts descending, returns top `n` as list of dicts.

Example
```python
from app.data_processing import load_data, get_top_n

df = load_data()
print(get_top_n(df, "gdp", 3))
# [
#   {"country": "USA", "gdp": 43545538},
#   {"country": "CAN", "gdp": 4204390},
#   {"country": "BRA", "gdp": 2964832}
# ]
```

Notes
- Ensure `metric` is numeric to produce meaningful aggregations.