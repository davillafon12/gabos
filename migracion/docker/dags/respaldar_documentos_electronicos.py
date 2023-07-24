from datetime import datetime

from airflow import DAG
from airflow.models import Variable
from airflow.providers.ssh.operators.ssh import SSHOperator

dag = DAG(
    "Respaldar_Documentos_Electronicos",
    default_args={"retries": 1},
    tags=["Garotas Bonitas", "Hacienda"],
    start_date=datetime(2023, 6, 7),
    catchup=False,
    schedule_interval="0 2 * * *"
)

sincronizar_archivos = SSHOperator(ssh_conn_id='gabo_ssh',
    task_id='sincronizar_archivos',
    conn_timeout=1200,
    cmd_timeout=1200,
    command='rsync -a {{var.value.ruta_fuente_documentos_electronicos}} {{var.value.ruta_respaldo_documentos_electronicos}}',
    dag=dag)

sincronizar_archivos