from datetime import datetime

from airflow import DAG
from airflow.models import Variable
from airflow.providers.ssh.operators.ssh import SSHOperator

backup_name = "{{ ds }}_database_backup_file"
backup_directory = Variable.get("ruta_respaldos_bd")
backup_expiration_in_days = Variable.get("dias_para_eliminar_respaldo")

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
    conn_timeout=1200,
    cmd_timeout=1200,
    command='tar -czvf /tmp/' + backup_name + '.tar.gz /tmp/' + backup_name + '.sql',
    dag=dag)

mover_respaldo = SSHOperator(ssh_conn_id='gabo_ssh',
    task_id='mover_respaldo',
    conn_timeout=60,
    cmd_timeout=60,
    command='mv /tmp/' + backup_name + '.tar.gz ' + backup_directory,
    dag=dag)

eliminar_respaldo_sin_comprimir = SSHOperator(ssh_conn_id='gabo_ssh',
    task_id='eliminar_respaldo_sin_comprimir',
    conn_timeout=60,
    cmd_timeout=60,
    command='rm /tmp/' + backup_name + '.sql',
    dag=dag)

eliminar_respaldos_viejos = SSHOperator(ssh_conn_id='gabo_ssh',
    task_id='eliminar_respaldos_viejos',
    conn_timeout=60,
    cmd_timeout=60,
    command="find " + backup_directory + " -type f -mtime +" + backup_expiration_in_days + " -name '*.gz' -print0 | xargs -r0 rm --",
    dag=dag)
    
crear_respaldo >> comprimir_respaldo >> [eliminar_respaldo_sin_comprimir, mover_respaldo] >> eliminar_respaldos_viejos