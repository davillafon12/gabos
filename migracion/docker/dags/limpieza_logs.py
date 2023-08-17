from datetime import datetime

from airflow import DAG
from airflow.models import Variable
from airflow.providers.ssh.operators.ssh import SSHOperator

dag = DAG(
    "Limpieza_De_Logs",
    default_args={"retries": 1},
    tags=["Garotas Bonitas", "Limpieza"],
    start_date=datetime(2023, 6, 7),
    catchup=False,
    schedule_interval="0 4 * * *"
)

backup_directory = Variable.get("ruta_respaldos_bd")
backup_expiration_in_days = Variable.get("dias_para_eliminar_respaldo")

eliminar_logs_gabo_viejos = SSHOperator(ssh_conn_id='gabo_ssh',
    task_id='eliminar_logs_gabo_viejos',
    conn_timeout=60,
    cmd_timeout=60,
    command="find {{var.value.ruta_logs_gabo_app}} -mtime +{{var.value.maximo_dias_logs_gabo_app}} -exec rm {} \;",
    dag=dag)

eliminar_logs_gabo_viejos