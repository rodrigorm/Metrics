#!/usr/bin/env bash

mysqladmin -h127.0.0.1 extended-status | grep 'Qcache'
mysqladmin -h127.0.0.1 extended-status | grep 'Com_select '
mysqladmin -h127.0.0.1 extended-status | grep 'Com_insert '
mysqladmin -h127.0.0.1 extended-status | grep 'Com_delete '
mysqladmin -h127.0.0.1 extended-status | grep 'Com_update '
mysqladmin -h127.0.0.1 extended-status | grep 'Com_replace '

Qcache_hits=$(mysqladmin -h127.0.0.1 extended-status | grep 'Qcache_hits ' | awk '{ print $4 }')
Qcache_inserts=$(mysqladmin -h127.0.0.1 extended-status | grep 'Qcache_inserts ' | awk '{ print $4 }')
Com_select=$(mysqladmin -h127.0.0.1 extended-status | grep 'Com_select ' | awk '{ print $4 }')
Com_insert=$(mysqladmin -h127.0.0.1 extended-status | grep 'Com_insert ' | awk '{ print $4 }')
Com_delete=$(mysqladmin -h127.0.0.1 extended-status | grep 'Com_delete ' | awk '{ print $4 }')
Com_update=$(mysqladmin -h127.0.0.1 extended-status | grep 'Com_update ' | awk '{ print $4 }')
Com_replace=$(mysqladmin -h127.0.0.1 extended-status | grep 'Com_replace ' | awk '{ print $4 }')

echo
echo 'Query Cache Efficiency'
echo '----------------------'
echo 'Qcache_hits / (Com_select + Qcache_hits) = ' $(echo "($Qcache_hits / ($Com_select + $Qcache_hits)) * 100" | bc -l)
echo 'Qcache_hits / Qcache_inserts = ' $(echo "($Qcache_hits / $Qcache_inserts) * 100" | bc -l 2>/dev/null)
echo '(Com_insert + Com_delete + Com_update + Com_replace) / Qcache_hits = ' $(echo "(($Com_insert + $Com_delete + $Com_update + $Com_replace) / $Qcache_hits) * 100" | bc -l)

