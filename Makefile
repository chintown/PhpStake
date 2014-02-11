# ex:ts=8:sw=8:noexpandtab
#.PHONY: clean

SRCTOP := $(shell pwd)
#@sed -i '' "s/MODE',[0-1]/MODE',1/" "$(SRCTOP)/config/dev.php";
#@sed -i '' "s/MODE',[0-1]/MODE',0/" "$(SRCTOP)/config/dev.php";
#@sed -i '' "s/ENV','.*'/ENV','local'/" "$(SRCTOP)/config/dev.php";
#@sed -i '' "s/ENV','.*'/ENV','remote'/" "$(SRCTOP)/config/dev.php";

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
	@$(SRCTOP)/tool/gen_js.sh;

css:
	@$(SRCTOP)/tool/gen_css.sh;

bootstrap:
	lessc -x $(SRCTOP)/script/less/bootstrap/bootstrap.less > $(SRCTOP)/htdoc/css/bootstrap.css

update:
	@git pull origin master

stub:
	$(SRCTOP)/tool/gen_entry.sh ${name};

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

