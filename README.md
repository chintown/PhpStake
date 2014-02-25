PhpStake
========

a simple php framework

Apply PhpStake as a parent project
----------------------------

1. fix project path by
    
	```bash
	make fix_production_inc_path
	```
1. generate static files

	```bash
	make js css_parent
	```
1. link public directory to `htdoc`

1. fork a child project which will leverage the resouce of `PhpStake`

	```bash
	make fork name=<child_project_name>
	```
