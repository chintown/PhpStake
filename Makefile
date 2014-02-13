# ex:ts=8:sw=8:noexpandtab
#.PHONY: clean

SRCTOP := $(shell pwd)
PARENTTOP := $(shell grep __PARENT_ROOT__ core/main.inc.php |  cut -d ' ' -f6 | tr -d "'")

dev:
	@perl -pi -e "s/MODE',[0-1]/MODE',1/" "$(SRCTOP)/config/dev.php";
	@echo "DEV MODE";

prod:
	@perl -pi -e "s/MODE',[0-1]/MODE',0/" "$(SRCTOP)/config/dev.php";
	@echo "PROD MODE";

local:
	@perl -pi -e "s/ENV','.*'/ENV','local'/" "$(SRCTOP)/config/dev.php";
	@echo "Local ENV";

remote:
	@perl -pi -e "s/ENV','.*'/ENV','remote'/" "$(SRCTOP)/config/dev.php";
	@echo "Remote ENV";

script: js css

js:
	@$(PARENTTOP)/tool/gen_js.sh;

css:
	@$(PARENTTOP)/tool/gen_css.sh;

bootstrap:
	lessc -x $(SRCTOP)/script/less/bootstrap/bootstrap.less > $(SRCTOP)/htdoc/css/bootstrap.css

update:
	@git pull origin master

stub:
	$(PARENTTOP)/tool/gen_entry.sh ${name} $(PARENTTOP) $(SRCTOP);
	@perl -pi -e "s/___STUB___/${name}/" "$(SRCTOP)/entry/${name}.php";
	@perl -pi -e "s/___STUB___/${name}/" "$(SRCTOP)/template/${name}.footer.php";

stub_purge:
	$(PARENTTOP)/tool/clean_entry.sh ${name};

deploy: prod remote update script

log_mac:
	tail -f /var/log/apache2/error_log | sed "s/\\\n/\\n/g"

clean_log:
	#@sudo bash -c '> error_log '

test_db:
	@cd tool; php -f test_mysql.php;

fix_tool_permission:
	@chmod +x $(SRCTOP)/tool/*.sh
	@chmod +x $(SRCTOP)/tool/*.py

#http://ejohn.org/blog/keeping-passwords-in-source-control/
PW_FILE=config/pw.php
encrypt_pw:
	openssl cast5-cbc -e -in ${PW_FILE} -out ${PW_FILE}.cast5

decrypt_pw:
	openssl cast5-cbc -d -in ${PW_FILE}.cast5 -out ${PW_FILE}
	chmod 644 ${PW_FILE}

fork:
	@echo "backup...";
	-@mv $(SRCTOP)/../${name} $(shell mktemp -d -t phpstake)
	@echo "forking project structure...";
	mkdir -p $(SRCTOP)/../${name}/core
	mkdir -p $(SRCTOP)/../${name}/config
	mkdir -p $(SRCTOP)/../${name}/controller
	mkdir -p $(SRCTOP)/../${name}/entry
	mkdir -p $(SRCTOP)/../${name}/i18n
	mkdir -p $(SRCTOP)/../${name}/lib
	mkdir -p $(SRCTOP)/../${name}/script/less
	mkdir -p $(SRCTOP)/../${name}/template
	mkdir -p $(SRCTOP)/../${name}/tool
	mkdir -p $(SRCTOP)/../${name}/htdoc/js
	mkdir -p $(SRCTOP)/../${name}/htdoc/css
	mkdir -p $(SRCTOP)/../${name}/htdoc/font
	mkdir -p $(SRCTOP)/../${name}/htdoc/icon
	mkdir -p $(SRCTOP)/../${name}/htdoc/img
	mkdir -p $(SRCTOP)/../${name}/htdoc/error
	mkdir -p $(SRCTOP)/../${name}/htdoc/xml
	cp -r $(SRCTOP)/../PhpStake/config $(SRCTOP)/../${name}/
	cp $(SRCTOP)/../PhpStake/htdoc/crossdomain.xml $(SRCTOP)/../${name}/htdoc/crossdomain.xml
	cp $(SRCTOP)/../PhpStake/htdoc/robots.txt $(SRCTOP)/../${name}/htdoc/robots.txt
	cp $(SRCTOP)/../PhpStake/htdoc/humans.txt $(SRCTOP)/../${name}/htdoc/humans.txt
	cp $(SRCTOP)/../PhpStake/core/main.inc.php $(SRCTOP)/../${name}/core/main.inc.php
	cp $(SRCTOP)/../PhpStake/Makefile $(SRCTOP)/../${name}/Makefile
	@echo "configuring..."
	@perl -pi -e "s/___SITE___/${name}/" "$(SRCTOP)/../${name}/config/prerequisite.php";
	@perl -pi -e "s|/[*][*]/ //__PARENT_PROJECT__|/** //__PARENT_PROJECT__|" "$(SRCTOP)/../${name}/core/main.inc.php";
	@perl -pi -e "s|/[*][*] //__CHILD_PROJECT__|/**/ //__CHILD_PROJECT__|" "$(SRCTOP)/../${name}/core/main.inc.php";
