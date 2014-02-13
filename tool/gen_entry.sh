#!/bin/bash

if [ "x$1" == "x" ]; then
    echo "USAGE: $0 entry dir_from dir_to";
    exit 1;
fi

STUB='stub';
entry=`echo $1`
dir_from=`echo $2`
dir_to=`echo $3`
files=`find $dir_from -name *$STUB*`;
echo $dir_from "xxx";
echo $dir_to "xxx";
echo "generating ..."
for fn in $files; do
    path=`dirname $fn`;
    base=`basename $fn`;

    new_path=${path/$dir_from/$dir_to}
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