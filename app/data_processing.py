import pandas as pd

def load_data():
    df = pd.read_csv("data/raw_data.csv")
    df.dropna(inplace=True)
    df["country"] = df["country"].str.upper()
    return df

def get_summary(df):
    return {
        "total_rows": len(df),
        "columns": list(df.columns),
    }

def get_country_data(df, country_code):
    country_df = df[df["country"] == country_code]
    if country_df.empty:
        return None
    return {
        "country": country_code,
        "rows": country_df.to_dict(orient="records")
    }

def get_top_n(df, metric, n):
    if metric not in df.columns:
        return {"error": f"Métrica '{metric}' no encontrada"}
    top = df.groupby("country")[metric].sum().nlargest(n).reset_index()
    return top.to_dict(orient="records")
