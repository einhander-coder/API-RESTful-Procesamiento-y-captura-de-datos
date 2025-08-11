# Data Models

## SummaryResponse
Pydantic model returned by `GET /summary`.

Fields
- `total_rows` (int): Number of rows in the dataset after cleaning.
- `columns` (List[str]): Column names present in the dataset.

JSON example
```json
{
  "total_rows": 8,
  "columns": ["country", "population", "gdp", "life_expectancy", "year"]
}
```

## CountryDataResponse
Pydantic model returned by `GET /country/{country_code}`.

Fields
- `country` (str): Uppercased country code.
- `rows` (List[Dict[str, Any]]): Raw rows for the country, as parsed from the dataset.

JSON example
```json
{
  "country": "CAN",
  "rows": [
    {"country": "CAN", "population": 38005238, "gdp": 2050990, "life_expectancy": 81.7, "year": 2022},
    {"country": "CAN", "population": 38246108, "gdp": 2153400, "life_expectancy": 82.0, "year": 2023}
  ]
}
```

Notes
- Types are enforced by Pydantic at serialization time for the responses where models are configured in the route decorators.