#!/bin/sh

read_var(){
  echo $(grep -v '^#' .env | grep -e "$1" | sed -e 's/.*=//')
}

U=$1

path=`pwd`

COMPOSE_PROJECT_NAME=$(read_var "COMPOSE_PROJECT_NAME")

echo "[program:$COMPOSE_PROJECT_NAME]
process_name=%(program_name)s_%(process_num)02d
command=/bin/bash -c 'cd ${path} && /usr/local/bin/docker-compose exec -T app sh -c \"php artisan queue:work --queue=high,default,low --tries=3 --sleep=3 --force\"'
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
numprocs=4
user=$U
stdout_logfile=/var/log/worker.log
stderr_logfile=/var/log/worker.log
stopwaitsecs=3600
stdout_logfile_maxbytes=5MB" > /etc/supervisor/conf.d/$COMPOSE_PROJECT_NAME.conf

supervisorctl reread
supervisorctl update
supervisorctl status

echo "Worker added: /etc/supervisor/conf.d/$COMPOSE_PROJECT_NAME.conf"
