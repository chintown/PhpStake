# ex:ts=8:sw=8:noexpandtab
#.PHONY: clean

ROOT_CHILD := $(shell pwd)
ROOT_PARENT := $(shell grep __PARENT_ROOT__ core/main.inc.php |  cut -d ' ' -f6 | tr -d "'")

dev:
	@perl -pi -e "s/MODE',[0-1]/MODE',1/" "$(ROOT_CHILD)/config/dev.php";
	@echo "DEV MODE";

prod:
	@perl -pi -e "s/MODE',[0-1]/MODE',0/" "$(ROOT_CHILD)/config/dev.php";
	@echo "PROD MODE";

local:
	@perl -pi -e "s/ENV','.*'/ENV','local'/" "$(ROOT_CHILD)/config/dev.php";
	@echo "Local ENV";

remote:
	@perl -pi -e "s/ENV','.*'/ENV','remote'/" "$(ROOT_CHILD)/config/dev.php";
	@echo "Remote ENV";

script: js css

js:
	@$(ROOT_PARENT)/tool/gen_js.sh $(ROOT_PARENT) $(ROOT_CHILD);

css:
	@$(ROOT_PARENT)/tool/gen_css.sh $(ROOT_PARENT) $(ROOT_CHILD);

bootstrap:
	lessc -x $(ROOT_CHILD)/script/less/bootstrap/bootstrap.less > $(ROOT_CHILD)/htdoc/css/bootstrap.css

update:
	@git pull origin master

stub:
	$(ROOT_PARENT)/tool/gen_entry.sh ${name} $(ROOT_PARENT) $(ROOT_CHILD);
	@perl -pi -e "s/___STUB___/${name}/" "$(ROOT_CHILD)/entry/${name}.php";
	@perl -pi -e "s/___STUB___/${name}/" "$(ROOT_CHILD)/template/${name}.footer.php";

stub_purge:
	$(ROOT_PARENT)/tool/clean_entry.sh ${name} $(ROOT_PARENT) $(ROOT_CHILD);

deploy: prod remote update script

log_mac:
	tail -f /var/log/apache2/error_log | sed "s/\\\n/\\n/g"

clean_log:
	#@sudo bash -c '> error_log '

test_db:
	@cd tool; php -f test_mysql.php;

fix_tool_permission:
	@chmod +x $(ROOT_CHILD)/tool/*.sh
	@chmod +x $(ROOT_CHILD)/tool/*.py

#http://ejohn.org/blog/keeping-passwords-in-source-control/
PW_FILE=config/pw.php
encrypt_pw:
	openssl cast5-cbc -e -in ${PW_FILE} -out ${PW_FILE}.cast5

decrypt_pw:
	openssl cast5-cbc -d -in ${PW_FILE}.cast5 -out ${PW_FILE}
	chmod 644 ${PW_FILE}

fork:
	@echo "backup...";
	-@mv $(ROOT_CHILD)/../${name} $(shell mktemp -d -t phpstake)
	@echo "forking project structure...";
	mkdir -p $(ROOT_CHILD)/../${name}/core
	cp $(ROOT_CHILD)/../PhpStake/core/main.inc.php $(ROOT_CHILD)/../${name}/core/main.inc.php
	mkdir -p $(ROOT_CHILD)/../${name}/config
	cp -r $(ROOT_CHILD)/../PhpStake/config $(ROOT_CHILD)/../${name}/
	mkdir -p $(ROOT_CHILD)/../${name}/controller
	mkdir -p $(ROOT_CHILD)/../${name}/entry
	mkdir -p $(ROOT_CHILD)/../${name}/i18n
	mkdir -p $(ROOT_CHILD)/../${name}/lib
	mkdir -p $(ROOT_CHILD)/../${name}/script/less
	mkdir -p $(ROOT_CHILD)/../${name}/template
	mkdir -p $(ROOT_CHILD)/../${name}/tool
	mkdir -p $(ROOT_CHILD)/../${name}/htdoc
	cp $(ROOT_CHILD)/../PhpStake/htdoc/crossdomain.xml $(ROOT_CHILD)/../${name}/htdoc/crossdomain.xml
	cp $(ROOT_CHILD)/../PhpStake/htdoc/robots.txt $(ROOT_CHILD)/../${name}/htdoc/robots.txt
	cp $(ROOT_CHILD)/../PhpStake/htdoc/humans.txt $(ROOT_CHILD)/../${name}/htdoc/humans.txt
	mkdir -p $(ROOT_CHILD)/../${name}/htdoc/js
	cp $(ROOT_CHILD)/../PhpStake/htdoc/js/common.project.js $(ROOT_CHILD)/../${name}/htdoc/js/common.project.js
	mkdir -p $(ROOT_CHILD)/../${name}/htdoc/css
	mkdir -p $(ROOT_CHILD)/../${name}/htdoc/font
	mkdir -p $(ROOT_CHILD)/../${name}/htdoc/icon
	mkdir -p $(ROOT_CHILD)/../${name}/htdoc/img
	mkdir -p $(ROOT_CHILD)/../${name}/htdoc/error
	mkdir -p $(ROOT_CHILD)/../${name}/htdoc/xml
	cp $(ROOT_CHILD)/../PhpStake/Makefile $(ROOT_CHILD)/../${name}/Makefile
	@echo "configuring..."
	@perl -pi -e "s/___SITE___/${name}/" "$(ROOT_CHILD)/../${name}/config/prerequisite.php";
	@perl -pi -e "s|/[*][*]/ //__PARENT_PROJECT__|/** //__PARENT_PROJECT__|" "$(ROOT_CHILD)/../${name}/core/main.inc.php";
	@perl -pi -e "s|/[*][*] //__CHILD_PROJECT__|/**/ //__CHILD_PROJECT__|" "$(ROOT_CHILD)/../${name}/core/main.inc.php";
