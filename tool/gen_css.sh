#!/bin/bash

set -e
set -u

if [[ $# -ne 2 ]]; then
    echo "USAGE: $0 DIR_PARENT DIR_CHILD";
    exit 1;
fi

DIR_PARENT=`echo $1`
DIR_CHILD=`echo $2`

parent_tars=`find $DIR_PARENT/script/less/ -maxdepth 1 -name '*.less'`;
child_tars=`find $DIR_CHILD/script/less/ -maxdepth 1 -name '*.less'`;
tars="$parent_tars $child_tars";
tars="$tars $DIR_PARENT/script/less/bootstrap/bootstrap.less";
# tars="$tars $DIR_PARENT/script/less/bootstrap/responsive.less";
LESS_INCLUDE="$DIR_CHILD/script/less/:$DIR_PARENT/script/less/";

[ ! -d "$DIR_CHILD/htdoc/css" ] && mkdir "$DIR_CHILD/htdoc/css";

for fn in $tars; do
    # script/less/bootstrap/wells.less

    dname_above_less=${fn%%/less/*} # -> ...(/less/... removed)
    name_below_less=${fn#*less/} # -> bootstrap/wells.less
    name_below_less=${name_below_less%.*} # -> bootstrap/wells
    tar="$dname_above_less/less/$name_below_less.less";

    bname=`basename $fn` # wells.less
    fname=${bname%.*} # wells
    des="$DIR_CHILD/htdoc/css/$fname.css";

    echo "$tar"
    echo "> $des";

    [ ! -e "$tar" ] && exit 1;

    lessc --include-path="$LESS_INCLUDE" -x "$tar" > "$des";
done
echo # XXX this line critical
