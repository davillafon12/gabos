from datetime import datetime

from airflow import DAG
from airflow.models import Variable
from airflow.contrib.hooks.ssh_hook import SSHHook
from airflow.operators.python_operator import PythonOperator
from airflow.providers.ssh.operators.ssh import SSHOperator

backup_name = "{{ ds }}_database_backup_file"

dag = DAG(
    "Respaldar_Base_De_Datos",
    default_args={"retries": 1},
    tags=["Garotas Bonitas"],
    start_date=datetime(2023, 6, 7),
    catchup=False,
    schedule_interval="0 0 * * *"
)

crear_respaldo = SSHOperator(ssh_conn_id='gabo_ssh',
    task_id='crear_respaldo',
    conn_timeout=1200,
    cmd_timeout=1200,
    command='mysqldump -u {{var.value.respaldo_usuario}} {{var.value.respaldo_base_de_datos}} > /tmp/' + backup_name + '.sql',
    dag=dag)

comprimir_respaldo = SSHOperator(ssh_conn_id='gabo_ssh',
    task_id='comprimir_respaldo',
    conn_timeout=300,
    cmd_timeout=300,
    command='tar -czvf ~/respaldos/' + backup_name + '.tar.gz /tmp/' + backup_name + '.sql',
    dag=dag)

eliminar_respaldo = SSHOperator(ssh_conn_id='gabo_ssh',
    task_id='eliminar_respaldo',
    conn_timeout=60,
    cmd_timeout=60,
    command='rm /tmp/' + backup_name + '.sql',
    dag=dag)
    
crear_respaldo >> comprimir_respaldo >> eliminar_respaldo