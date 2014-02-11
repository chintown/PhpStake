#!/bin/bash

tars=`find script/less/ -maxdepth 1 -name '*.less'`;
tars="$tars script/less/bootstrap/bootstrap.less";
tars="$tars script/less/bootstrap/responsive.less";

mkdir "htdoc/css"

for fn in $tars; do
    # script/less/bootstrap/wells.less

    #dname=`dirname $fn`
    bname=`basename $fn` # wells.less
    fname=${bname%.*} # wells
    pre=${fn#*less/} # bootstrap/wells.less
    pre=${pre%.*} # bootstrap/wells
    pre_tar="script/less/$pre";
    pre_des="htdoc/css/$fname";

    echo $pre_tar.less
    echo '> '$pre_des.css;
    lessc -x $pre_tar.less > $pre_des.css
done
