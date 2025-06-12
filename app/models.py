from pydantic import BaseModel
from typing import List, Dict, Any

class SummaryResponse(BaseModel):
    total_rows: int
    columns: List[str]

class CountryDataResponse(BaseModel):
    country: str
    rows: List[Dict[str, Any]]
