from datetime import datetime

from airflow import DAG
from airflow.providers.http.operators.http import SimpleHttpOperator

dag = DAG(
    "Actualizar_Comprobantes_Hacienda",
    default_args={"retries": 1},
    tags=["Garotas Bonitas", "Hacienda"],
    start_date=datetime(2023, 6, 7),
    catchup=False,
    schedule_interval="*/10 9-19 * * 1-6"
)

actualizar_comprobantes = SimpleHttpOperator(
    task_id="Actualizar_Comprobantes_Hacienda",
    endpoint="/external/actualizarComprobantes",
    http_conn_id="gabo_app_endpoint",
    dag=dag,
)

actualizar_comprobantes