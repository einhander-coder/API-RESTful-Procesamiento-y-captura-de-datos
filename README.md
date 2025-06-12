# 📊 API de Datos Procesados con FastAPI

Este proyecto es una API RESTful construida con **FastAPI** que expone datos previamente procesados. Se utiliza un dataset simple de países con métricas como población, PIB y esperanza de vida.

---

## 🚀 Tecnologías utilizadas

- Python 3.10+
- FastAPI
- Pandas
- Uvicorn
- Pydantic
- VS Code + Docker (opcional)

---

## 📁 Estructura del Proyecto
api-datos/
│
├── app/
│ ├── data_processing.py # Carga y procesamiento de datos
│ └── models.py # Esquemas Pydantic
│
├── data/
│ └── raw_data.csv # Dataset de ejemplo
│
├── main.py # Entrypoint de la API
├── requirements.txt
└── .vscode/
└── launch.json # Configuración para correr con VS Code

---

## ⚙️ Instalación y Ejecución

### 1. Clona el repositorio

```bash
git clone https://github.com/tuusuario/api-datos.git
cd api-datos

2. Crea un entorno virtual
bash
Copiar
Editar
python -m venv venv
source venv/bin/activate  # En Windows: venv\Scripts\activate
3. Instala las dependencias
bash
Copiar
Editar
pip install -r requirements.txt
4. Ejecuta la API
bash
Copiar
Editar
uvicorn main:app --reload
La API estará disponible en:
👉 http://127.0.0.1:8000
👉 Documentación Swagger en: http://127.0.0.1:8000/docs

📌 Endpoints disponibles
🔹 GET /summary
Devuelve información general del dataset (columnas y cantidad de registros).

🔹 GET /country/{country_code}
Devuelve todos los registros para un país específico (ej: USA, MEX).

🔹 GET /top/{metric}?n=5
Devuelve el top N de países ordenados por la suma total de una métrica (gdp, population, etc.).

Ejemplo:

bash
Copiar
Editar
GET /top/gdp?n=3
📊 Dataset de ejemplo (data/raw_data.csv)
Incluye datos ficticios de población, PIB y esperanza de vida para 4 países durante los años 2022–2023.

🧪 Probar la API (opcional)
Si usas VS Code con la extensión REST Client, crea un archivo requests.http:

http
Copiar
Editar
### Ver resumen
GET http://127.0.0.1:8000/summary

### Ver datos de Canadá
GET http://127.0.0.1:8000/country/CAN

### Top países por PIB
GET http://127.0.0.1:8000/top/gdp?n=2
📦 Docker (opcional)
Para ejecutar el proyecto con Docker:

bash
Copiar
Editar
docker build -t api-datos .
docker run -p 8000:8000 api-datos
✨ Créditos
Proyecto desarrollado como ejemplo de portafolio para ingeniería de datos y backend.

3. Instala las dependencias
bash
Copiar
Editar
pip install -r requirements.txt
4. Ejecuta la API
bash
Copiar
Editar
uvicorn main:app --reload
La API estará disponible en:
👉 http://127.0.0.1:8000
👉 Documentación Swagger en: http://127.0.0.1:8000/docs

📌 Endpoints disponibles
🔹 GET /summary
Devuelve información general del dataset (columnas y cantidad de registros).

🔹 GET /country/{country_code}
Devuelve todos los registros para un país específico (ej: USA, MEX).

🔹 GET /top/{metric}?n=5
Devuelve el top N de países ordenados por la suma total de una métrica (gdp, population, etc.).

Ejemplo:

bash
Copiar
Editar
GET /top/gdp?n=3
📊 Dataset de ejemplo (data/raw_data.csv)
Incluye datos ficticios de población, PIB y esperanza de vida para 4 países durante los años 2022–2023.

🧪 Probar la API (opcional)
Si usas VS Code con la extensión REST Client, crea un archivo requests.http:

http
Copiar
Editar
### Ver resumen
GET http://127.0.0.1:8000/summary

### Ver datos de Canadá
GET http://127.0.0.1:8000/country/CAN

### Top países por PIB
GET http://127.0.0.1:8000/top/gdp?n=2
📦 Docker (opcional)
Para ejecutar el proyecto con Docker:

bash
Copiar
Editar
docker build -t api-datos .
docker run -p 8000:8000 api-datos
✨ Autor: Frantz Hinrichsen ©2025
Proyecto corto desarrollado para portafolio orientado a ingeniería de datos y backend.

