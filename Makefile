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
	$(PARENTTOP)/tool/gen_entry.sh ${name};
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
	cp -r $(SRCTOP)/../PhpStake $(SRCTOP)/../z${name}
	@perl -pi -e "s/___SITE___/${name}/" "$(SRCTOP)/../z${name}/config/prerequisite.php";
	@perl -pi -e "s|/[*][*]/ //__PARENT_PROJECT__|/** //__PARENT_PROJECT__|" "$(SRCTOP)/../z${name}/core/main.inc.php";
	@perl -pi -e "s|/[*][*] //__CHILD_PROJECT__|/**/ //__CHILD_PROJECT__|" "$(SRCTOP)/../z${name}/core/main.inc.php";
	mv "$(SRCTOP)/../z${name}/.git/" /tmp/$RANDOM.git;
	mv "$(SRCTOP)/../z${name}/.idea/" /tmp/$RANDOM.idea;
