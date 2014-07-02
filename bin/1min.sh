#!/usr/bin/env bash

# Put this file to run every minute on cronjobs

mysql -h127.0.0.1 -uroot -proot benchmark -e 'INSERT INTO data_5min (metric_id, created, value) SELECT SQL_NO_CACHE metric_id, created, value FROM data;'
mysql -h127.0.0.1 -uroot -proot benchmark -e 'TRUNCATE TABLE data;'
# mysql -uroot -proot benchmark -e 'SELECT COUNT(*) FROM data_5min;'

