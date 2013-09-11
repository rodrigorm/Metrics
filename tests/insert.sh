#!/usr/bin/env bash

mysql -h127.0.0.1 -uroot -proot benchmark -e 'INSERT INTO data (metric_id, created, value) VALUES (1, NOW(), 5 + (RAND() * 15));'
# mysql -uroot -proot benchmark -e 'INSERT INTO orders (created, value) VALUES (NOW(), 5 + (RAND() * 15));'
# mysql -uroot -proot benchmark -e 'INSERT INTO orders (created, value) VALUES (NOW(), 5 + (RAND() * 15));'
# mysql -uroot -proot benchmark -e 'INSERT INTO orders (created, value) VALUES (NOW(), 5 + (RAND() * 15));'
# mysql -uroot -proot benchmark -e 'INSERT INTO orders (created, value) VALUES (NOW(), 5 + (RAND() * 15));'
# mysql -uroot -proot benchmark -e 'INSERT INTO orders (created, value) VALUES (NOW(), 5 + (RAND() * 15));'
# mysql -uroot -proot benchmark -e 'INSERT INTO orders (created, value) VALUES (NOW(), 5 + (RAND() * 15));'
# mysql -uroot -proot benchmark -e 'INSERT INTO orders (created, value) VALUES (NOW(), 5 + (RAND() * 15));'
# mysql -uroot -proot benchmark -e 'INSERT INTO orders (created, value) VALUES (NOW(), 5 + (RAND() * 15));'
# mysql -uroot -proot benchmark -e 'INSERT INTO orders (created, value) VALUES (NOW(), 5 + (RAND() * 15));'
# mysql -uroot -proot benchmark -e 'SELECT COUNT(*) FROM orders;'
# mysql -uroot -proot benchmark -e 'SELECT \
#     (SELECT COUNT(*) FROM orders) + \
#     (SELECT COUNT(*) FROM orders_5min) + \
#     (SELECT COUNT(*) FROM orders_60min) as total;'

