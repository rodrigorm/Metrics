#!/usr/bin/env bash

mysql -h127.0.0.1 -uroot -proot benchmark -e 'INSERT INTO data_60min (metric_id, created, value) SELECT SQL_NO_CACHE metric_id, created, value FROM data_5min;'
mysql -h127.0.0.1 -uroot -proot benchmark -e 'TRUNCATE data_5min;'
# mysql -uroot -proot benchmark -e 'SELECT COUNT(*) FROM data_60min;'

