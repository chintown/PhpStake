#!/bin/sh

set -e
set -u

#echo $#;
#echo $@;

action=`echo $1`;
resource=`echo $2`;
where=$([ "x$where" != "x" ] && echo "where=$where" || echo '');
skip=$([ "x$skip" != "x" ] && echo "skip=$skip" || echo '');
limit=$([ "x$limit" != "x" ] && echo "limit=$limit" || echo '');
order=$([ "x$order" != "x" ] && echo "order=$order" || echo '');
count=$([ "x$count" != "x" ] && echo "count=$count" || echo '');
include=$([ "x$include" != "x" ] && echo "include=$include" || echo '');

uri="https://api.parse.com/1/classes/$resource";
headers="X-Parse-Application-Id:$app X-Parse-REST-API-Key:$rest"

http $action $uri $headers $where $skip $limit $order $include $count
if [ "x$debug" != "x" ]; then
    echo http $action $uri $headers $where $skip $limit $order $include $count
fi