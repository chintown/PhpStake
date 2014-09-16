# ex:ts=8:sw=8:noexpandtab
#.PHONY: clean

ROOT_CHILD := $(shell pwd)
ROOT_PARENT := $(shell grep __PARENT_ROOT__ core/main.inc.php |  cut -d ' ' -f6 | tr -d "'")

%: # for all not matched target
	@: # do nothing silently

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

css_parent:
		@$(shell pwd)/tool/gen_css.sh $(shell pwd) $(shell pwd);

bootstrap:
	lessc -x $(ROOT_PARENT)/script/less/bootstrap/bootstrap.less > $(ROOT_PARENT)/htdoc/css/bootstrap.css;
	lessc -x $(ROOT_PARENT)/script/less/bootstrap.precompile/bootstrap.less > $(ROOT_PARENT)/htdoc/css/bootstrap.precompile.css;
	lessc -x $(ROOT_PARENT)/script/less/bootstrap.precompile/bootstrap-responsive.less > $(ROOT_PARENT)/htdoc/css/bootstrap-responsive.precompile.css;

update:
	@git pull origin master

stub:
	$(ROOT_PARENT)/tool/gen_entry.sh ${name} $(ROOT_PARENT) $(ROOT_CHILD);
	@perl -pi -e "s/___STUB___/${name}/" "$(ROOT_CHILD)/entry/${name}.php";
	@perl -pi -e "s/___STUB___/${name}/" "$(ROOT_CHILD)/template/${name}.footer.php";

stub_purge:
	$(ROOT_PARENT)/tool/clean_entry.sh ${name} $(ROOT_PARENT) $(ROOT_CHILD);

flo:
	supervisor -- $(ROOT_PARENT)/tool/flo.js '$(ROOT_CHILD)'

deploy: prod remote update script

#http://stackoverflow.com/questions/6273608/how-to-pass-argument-to-makefile-from-command-line
#make query_parse get Diary where='={"pet":{"__type":"Pointer","className":"Pet","objectId":"W7G2xRgmrG"}}' count==1
KEY_APP := $(shell grep PARSE_APP_ID $(ROOT_CHILD)/config/dev.php | sed -E "s/define\('PARSE_APP_ID', '(.*)'\);/\1/" | tr -d ' ')
KEY_REST := $(shell grep PARSE_REST_ID $(ROOT_CHILD)/config/dev.php | sed -E "s/define\('PARSE_REST_ID', '(.*)'\);/\1/" | tr -d ' ')
query_parse:
	@debug=${debug} app=$(KEY_APP) rest=$(KEY_REST) where='${where}' skip='${skip}' limit='${limit}' order='${order}' count='${count}' include='${include}' $(ROOT_PARENT)/tool/query_parse.sh $(filter-out $@,$(MAKECMDGOALS))

map:
	touch $(ROOT_CHILD)/htdoc/sitemap.gz $(ROOT_CHILD)/htdoc/sitemap.xml;
	@rm $(ROOT_CHILD)/htdoc/sitemap.gz $(ROOT_CHILD)/htdoc/sitemap.xml;
	@wget -O- '$(WEB_ROOT)/sitemap.php' > $(ROOT_CHILD)/htdoc/sitemap.xml;
	@gzip -c $(ROOT_CHILD)/htdoc/sitemap.xml > $(ROOT_CHILD)/htdoc/sitemap.gz

log_mac:
	tail -f /var/log/apache2/error_log | sed "s/\\\n/\\n/g"

clean_log:
	#@sudo bash -c '> error_log '

test_db:
	php -f $(ROOT_PARENT)/tool/test_mysql.php $(ROOT_CHILD);

fix_tool_permission:
	@chmod +x $(ROOT_CHILD)/tool/*.sh
	@chmod +x $(ROOT_CHILD)/tool/*.py

fix_production_inc_path:
	@perl -pi -e "s/Users/home/" "$(ROOT_CHILD)/core/main.inc.php";

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
	cp $(ROOT_CHILD)/../PhpStake/template/common.project.footer.php $(ROOT_CHILD)/../${name}/template/common.project.footer.php
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
	cp $(ROOT_CHILD)/../PhpStake/.gitignore $(ROOT_CHILD)/../${name}/.gitignore
	@echo "configuring..."
	@perl -pi -e "s/___SITE___/${name}/" "$(ROOT_CHILD)/../${name}/config/prerequisite.php";
	@perl -pi -e "s|/[*][*]/ //__PARENT_PROJECT__|/** //__PARENT_PROJECT__|" "$(ROOT_CHILD)/../${name}/core/main.inc.php";
	@perl -pi -e "s|/[*][*] //__CHILD_PROJECT__|/**/ //__CHILD_PROJECT__|" "$(ROOT_CHILD)/../${name}/core/main.inc.php";


# refs: https://algorithms.rdio.com/post/make/

rsync_js:
	rsync -rave "ssh -i ${pem}" $(ROOT_CHILD)/htdoc/js/ ${host}:${path}/htdoc/js/
rsync_css:
	rsync -rave "ssh -i ${pem}" $(ROOT_CHILD)/htdoc/css/ ${host}:${path}/htdoc/css/