from datetime import datetime

from airflow import DAG
from airflow.models import Variable
from airflow.providers.ssh.operators.ssh import SSHOperator

docker_host_user = Variable.get("ssh_docker_host_user")
docker_host_user_password = Variable.get("ssh_docker_host_user_password")
docker_host_url = Variable.get("ssh_docker_host_url")
docker_host_port = Variable.get("ssh_docker_host_port")

dag = DAG(
    "Respaldar_Documentos_Electronicos",
    default_args={"retries": 1},
    tags=["Garotas Bonitas", "Hacienda"],
    start_date=datetime(2023, 6, 7),
    catchup=False,
    schedule_interval="0 2 * * *"
)

sincronizar_archivos = SSHOperator(ssh_conn_id='gabo_app_ssh',
    task_id='sincronizar_archivos',
    conn_timeout=1200,
    cmd_timeout=1200,
    command="sshpass -p '" + docker_host_user_password + "' rsync -a -e 'ssh -p " + docker_host_port + " -o StrictHostKeyChecking=no' '{{var.value.ruta_fuente_documentos_electronicos}}'  " + docker_host_user + "@" + docker_host_url + ":'{{var.value.ruta_respaldo_documentos_electronicos}}'",
    dag=dag)

sincronizar_archivos