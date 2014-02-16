#! /usr/bin/env python
# coding: utf-8
import sys
import os
import re

template_dir = 'template/'
# see template/portfolio.footer.php as an example
p_user = re.compile(".*toggle_min_script\(.*?'(/js/.*?)'\).*")
p_lib = re.compile('.*type="text\/javascript"\ src=".*?(js\/vendor/.*?)".*')


def get_controllers():
    controllers = []
    files = os.listdir(template_dir)
    for file in files:
        if file.endswith('.footer.php') and not file.startswith('jquery.') and not file.startswith('project.'):
            controller = file.split('.')[0]
            controllers.append(controller)
    return controllers


def get_users(controller):
    users = []
    fn = os.path.join(template_dir, controller + '.footer.php')
    f = open(fn, 'r')
    content = f.readlines()
    f.close()

    for line in content:
        m = p_user.match(line)
        if m is not None:
            g = m.groups()
            js_file = g[0]  # e.g. /static/js/portfolio.js
            if not js_file.endswith('___STUB___.js'):
                users.append(js_file)
    users = map(lambda user: user.lstrip('/'), users)
    return users


def get_lib(controller):
    libs = []
    fn = os.path.join(template_dir, controller + '.footer.php')
    f = open(fn, 'r')
    content = f.readlines()
    f.close()

    for line in content:
        m = p_lib.match(line)
        if m is not None:
            g = m.groups()
            js_file = g[0]  # e.g. lib/MageQ/MageQ.js | /static/js/portfolio.lib.min.js
            if 'min' not in js_file:
                libs.append(js_file)
    libs = map(lambda lib: lib.lstrip('/'), libs)
    return libs

if __name__ == "__main__":
    if len(sys.argv) < 3:
        '''
        USAGE:
            gen_js_util.py controllers_need_customized_js target_filepath
            -> portfolio dashboard index (space separated)
            gen_js_util.py user_js_file_names <controller_name> target_filepath
            -> /static/js/std.js /static/js/Manage.js /static/js/Flipper.js ...
            gen_js_util.py lib_js_file_names <controller_name> target_filepath
            -> lib/MXHR/DUI.js lib/MXHR/Stream.js lib/jqueryjson/jquery.json.js ...
        '''
    else:
        action = sys.argv[1]
        target_filepath = sys.argv[-1]
        #print action

        template_dir = target_filepath + '/' + template_dir
        controllers = get_controllers()
        if action == 'controllers_need_customized_js':
            result = controllers
        elif action == 'user_js_file_names':
            controller = sys.argv[2]
            result = get_users(controller)
        elif action == 'lib_js_file_names':
            controller = sys.argv[2]
            result = get_lib(controller)

        result = ' '.join(result)
        print result
