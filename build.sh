#!/bin/bash
# declare STRING variable

if [ $# -eq 0 ] 
then
	set "dev"
fi

if [ $1 = "dev" ] 
then
	echo -e "building dev \n"
	cp ~/ABridge/Src/ABridge_dev.php /C/xampp/htdocs/ABridge.php
	cp ~/ABridge/Src/ABridge_init.php /C/xampp/htdocs/ABridge_init.php
	exit
fi

echo -e "building release \n"

rm ~/ABridge/Bld/*.*
rm -R ~/ABridge/Bld/Tmp/Src
rm -R ~/ABridge/Bld/Tmp/DataStore
rm -R ~/ABridge/Bld/Tmp/App

cp -R ~/ABridge/Src ~/ABridge/Bld/Tmp
cp ~/ABridge/Bld/Tmp/Src/config.ini ~/ABridge/Bld
cp ~/ABridge/Bld/Tmp/Src/ABridge_prod.php ~/ABridge/Bld/ABridge.php
cp ~/ABridge/Bld/Tmp/Src/ABridge_init.php ~/ABridge/Bld/ABridge_init.php

rm ~/ABridge/Bld/Tmp/Src/config.ini
rm ~/ABridge/Bld/Tmp/Src/ABridge_dev.php
rm ~/ABridge/Bld/Tmp/Src/ABridge_test.php
rm ~/ABridge/Bld/Tmp/Src/ABridge_prod.php
rm ~/ABridge/Bld/Tmp/Src/ABridge_init.php

cp -R ~/ABridge/Datastore ~/ABridge/Bld/Tmp
cp -R ~/ABridge/App ~/ABridge/Bld/Tmp

php ~/ABridge/Src/BuildPhar.php
cp ~/ABridge/Bld/*.*  /C/xampp/htdocs

