from fastapi import FastAPI, HTTPException
from app.data_processing import load_data, get_summary, get_country_data, get_top_n
from app.models import CountryDataResponse, SummaryResponse

app = FastAPI(title="API de Datos Procesados")

df = load_data()  # Procesamiento inicial

@app.get("/summary", response_model=SummaryResponse)
def summary():
    return get_summary(df)

@app.get("/country/{country_code}", response_model=CountryDataResponse)
def country_data(country_code: str):
    data = get_country_data(df, country_code.upper())
    if data is None:
        raise HTTPException(status_code=404, detail="País no encontrado")
    return data

@app.get("/top/{metric}")
def top_n(metric: str, n: int = 10):
    return get_top_n(df, metric, n)
