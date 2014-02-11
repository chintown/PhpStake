#!/bin/bash

controllers=`python tool/gen_js_util.py controllers_need_customized_js`;

for controller in $controllers; do
    echo '===== ===== ===== '$controller' ===== ===== ====='
    echo

    echo '----- ----- -----'
    echo '[user scripts]';
    user_scripts=`python tool/gen_js_util.py user_js_file_names $controller`;
    #echo "$user_scripts";

    # 1. compress individuals
    # 2. transform to min file name
    min_users='';
    for user in $user_scripts; do
        pre="htdoc/"${user%.*};                                 # output
        echo "$pre.js > $pre.min.js";
        java -jar tool/yuicompressor-2.4.7.jar $pre.js -o $pre.min.js; # actual command
        ret=$?;
        [ "x$ret" != "x0" ] && exit 1;
        min_users="$min_users $pre.min.js";
    done
    echo

    # concatenate min users
    echo '[user scripts pack]';
    if [[ $min_users != '' ]]; then
        tmp="/tmp/$controller.pack.js";
        output="htdoc/js/$controller.pack.min.js";              # output
        echo $min_users
        echo '> '$tmp;
        cat $min_users > $tmp; # actual command
        echo '> '$output;
        cat $tmp > $output; # actual command
    fi
    echo


    echo '----- ----- -----'
    echo '[lib scripts pack] prepare merged min file for 3rd js (min.js should be ready before merge), http://refresh-sf.com/yui/';
    lib_scripts=`python tool/gen_js_util.py lib_js_file_names $controller`;
    #echo "$lib_scripts";

    # (prepare min js manually to make sure correctness)
    # transform to min file name
    min_libs='';
    for lib in $lib_scripts; do
        pre='htdoc/'${lib%.*};                                  # output
        echo "$pre";
        min_libs="$min_libs $pre.min.js";
    done
    echo

    # concatenate min libs
    if [[ $min_libs != '' ]]; then
        output="htdoc/js/$controller.lib.min.js";
        echo $min_libs
        echo '> '$output;
        cat $min_libs > $output; # actual command
    fi
    echo
done

