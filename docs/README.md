# Processed Data API — Documentation

This project exposes a small, preprocessed dataset through a FastAPI REST API. Use this documentation to discover all public endpoints, data models, and internal functions.

- Base URL (local): `http://127.0.0.1:8000`
- Interactive docs (Swagger UI): `http://127.0.0.1:8000/docs`
- OpenAPI schema: `http://127.0.0.1:8000/openapi.json`

## Quickstart

1) Install dependencies
```bash
pip install -r requirements.txt
```

2) Run the API
```bash
uvicorn main:app --reload
```

3) Browse docs at `http://127.0.0.1:8000/docs`

## Dataset

- Source file: `data/raw_data.csv`
- Columns: `country`, `population`, `gdp`, `life_expectancy`, `year`

## Documentation Contents

- Endpoints reference: see `docs/endpoints.md`
- Data models (Pydantic): see `docs/models.md`
- Internal Python functions: see `docs/functions.md`

## Export OpenAPI

Generate a local copy of the OpenAPI spec:
```bash
curl -s http://127.0.0.1:8000/openapi.json -o openapi.json
```

## Support

If you encounter issues running locally, ensure your Python version is 3.10+ and that `uvicorn` and `pandas` are installed from `requirements.txt`.