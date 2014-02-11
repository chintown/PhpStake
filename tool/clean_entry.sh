#!/bin/bash

if [ "x$1" == "x" ]; then
    echo "USAGE: $0 entry";
    exit 1;
fi

STUB='stub';
entry=`echo $1`
files=`find . -name *$STUB*`;
echo "cleaning ..."
for fn in $files; do
    path=`dirname $fn`;
    base=`basename $fn`;
    new_base=${base/$STUB/$entry};
    new_fn="$path/$new_base";
    echo $new_fn;
    rm $new_fn;
done

echo 'done'