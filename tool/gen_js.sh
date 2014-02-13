#!/bin/bash

set -e
set -u

if [[ $# -ne 2 ]]; then
    echo "USAGE: $0 DIR_PARENT DIR_CHILD";
    exit 1;
fi

DIR_PARENT=`echo $1`
DIR_CHILD=`echo $2`

TOOL_ROOT="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
controllers=`python $TOOL_ROOT/gen_js_util.py controllers_need_customized_js $DIR_CHILD`;
echo "$controllers";

for controller in $controllers; do
    echo '===== ===== ===== '$controller' ===== ===== ====='
    echo

    echo '----- ----- -----'
    echo '[user scripts]';
    user_scripts=`python $TOOL_ROOT/gen_js_util.py user_js_file_names $controller $DIR_CHILD`;
    # echo "$user_scripts";

    # 1. compress individuals
    # 2. transform to min file name
    min_users='';
    for user in $user_scripts; do
        pre="htdoc/"${user%.*};                                 # output
        from_file="$DIR_CHILD/$pre.js"
        ([ -e $from_file ] && from_file="$from_file") || from_file="$DIR_PARENT/$pre.js";
                                          # fallback to parent project directory
        to_file="$DIR_CHILD/$pre.min.js"

        echo "$from_file"
        echo "  > $to_file";
        java -jar $TOOL_ROOT/yuicompressor-2.4.7.jar $from_file -o $to_file; # actual command
        ret=$?;
        [ "x$ret" != "x0" ] && exit 1;
        min_users="$min_users $to_file";
    done
    echo

    # concatenate min users
    echo '[user scripts pack]';
    if [[ $min_users != '' ]]; then
        tmp="/tmp/$controller.pack.js";
        output="$DIR_CHILD/htdoc/js/$controller.pack.min.js";              # output
        echo $min_users
        echo '> '$tmp;
        cat $min_users > $tmp; # actual command
        echo '> '$output;
        cat $tmp > $output; # actual command
    fi
    echo


    echo '----- ----- -----'
    echo '[lib scripts pack] prepare merged min file for 3rd js (*.min.js should be ready before merge), http://refresh-sf.com/yui/';
    lib_scripts=`python $TOOL_ROOT/gen_js_util.py lib_js_file_names $controller $DIR_CHILD`;
    #echo "$lib_scripts";

    # (prepare min js manually to make sure correctness)
    # transform to min file name
    min_libs='';
    for lib in $lib_scripts; do
        pre="$DIR_PARENT/htdoc/"${lib%.*};                        # output
        echo "$pre";
        min_libs="$min_libs $pre.min.js";
    done
    echo

    # concatenate min libs
    if [[ $min_libs != '' ]]; then
        output="$DIR_CHILD/htdoc/js/$controller.lib.min.js";
        echo "$min_libs"
        echo "> $output";
        cat $min_libs > $output; # actual command
    fi
    echo
done

