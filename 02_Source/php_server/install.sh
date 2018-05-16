#!/bin/bash
# GLOBAL VARIABLES
INS_PATH=/var/www/html/
APP_NAME=DTND

function clean_dir {
	rm -rf $INS_PATH/$APP_NAME > /dev/null
}

function copy_dir {
	mkdir $INS_PATH/$APP_NAME
	cp -rf * $INS_PATH/$APP_NAME
	chown -R www-data:www-data $INS_PATH/$APP_NAME
	chmod 777 images
}

function check_dir {
	if [ -f $INS_PATH/$APP_NAME/install.sh ]; then
		return 1;
	fi
	return 0;
}

# MAIN
echo Deploying $APP_NAME into $INS_PATH...
echo -n [-] Cleaning old $APP_NAME directory
clean_dir
echo -e '\t\t\t'[OK]
echo -n [-] Copying $APP_NAME to $INS_PATH
copy_dir
echo -e '\t\t'[OK]
echo -n [-] Checking deploy is successfully
if [ check_dir ]; then
	echo -e '\t\t'[OK]
else
	echo -e '\t\t'[FAIL]
fi
echo
echo '---------------'
echo DONE
