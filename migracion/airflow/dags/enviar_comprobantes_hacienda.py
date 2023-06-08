from datetime import datetime

from airflow import DAG
from airflow.providers.http.operators.http import SimpleHttpOperator

dag = DAG(
    "Enviar_Comprobantes_Hacienda",
    default_args={"retries": 1},
    tags=["Garotas Bonitas", "Hacienda"],
    start_date=datetime(2023, 6, 7),
    catchup=False,
    schedule_interval="*/10 * * * *"
)

enviar_comprobantes = SimpleHttpOperator(
    task_id="Enviar_Comprobantes_Hacienda",
    endpoint="/external/enviarComprobantesAHacienda",
    http_conn_id="gabo_endpoint",
    dag=dag,
)

enviar_comprobantes

