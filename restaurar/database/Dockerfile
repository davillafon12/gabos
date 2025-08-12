FROM debian:12

# docker build -t gabo-db . -f Dockerfile-db
# docker run -dit -p 3306:3306 gabo-db

RUN apt update
RUN apt -y install openssh-server sshpass

COPY my.cnf /etc/my.cnf
RUN apt install -y mariadb-server

COPY 50-server.cnf /etc/mysql/mariadb.conf.d/50-server.cnf

COPY entry.sh /usr/entry.sh
RUN chmod 755 /usr/entry.sh

COPY db-init.sh /usr/db-init.sh
RUN chmod 755 /usr/db-init.sh

COPY respaldo-gabo.sql /tmp/respaldo-gabo.sql
COPY respaldo-procedimientos.sql /tmp/respaldo-procedimientos.sql
COPY respaldo-airflow.sql /tmp/respaldo-airflow.sql
COPY respaldo-jasperreports.sql /tmp/respaldo-jasperreports.sql

EXPOSE 3306
EXPOSE 22
ENTRYPOINT ["bash", "/usr/entry.sh"]