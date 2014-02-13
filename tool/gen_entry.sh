#!/bin/bash

set -e
set -u

if [[ $# -ne 3 ]]; then
    echo "USAGE: $0 entry DIR_PARENT DIR_CHILD";
    exit 1;
fi

STUB='stub';
entry=`echo $1`
DIR_PARENT=`echo $2`
DIR_CHILD=`echo $3`
files=`find $DIR_PARENT -name *$STUB*`;

echo "generating ..."
for fn in $files; do
    path=`dirname $fn`;
    base=`basename $fn`;

    new_path=${path/$DIR_PARENT/$DIR_CHILD}
    new_base=${base/$STUB/$entry};
    new_fn="$new_path/$new_base";

    echo $fn
    echo '  ->' $new_fn;
    cp $fn $new_fn;
done

echo "linking ..."
cd htdoc
rm "$entry.php" 2> /dev/null
ln -s "../entry/$entry.php";
echo 'done'