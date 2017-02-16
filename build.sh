#!/bin/bash
# declare STRING variable
rm ~/ABridge/Bld/*.*
rm -R ~/ABridge/Bld/Tmp/Src
rm -R ~/ABridge/Bld/Tmp/DataStore
rm -R ~/ABridge/Bld/Tmp/App
cp -R ~/ABridge/Src ~/ABridge/Bld/Tmp
cp ~/ABridge/Bld/Tmp/Src/config.ini ~/ABridge/Bld
cp ~/ABridge/Bld/Tmp/Src/ABridge_dev.php ~/ABridge/Bld
cp ~/ABridge/Bld/Tmp/Src/ABridge_prod.php ~/ABridge/Bld
rm ~/ABridge/Bld/Tmp/Src/config.ini
rm ~/ABridge/Bld/Tmp/Src/ABridge_dev.php
rm ~/ABridge/Bld/Tmp/Src/ABridge_prod.php
cp -R ~/ABridge/Datastore ~/ABridge/Bld/Tmp
cp -R ~/ABridge/App ~/ABridge/Bld/Tmp
php ~/ABridge/Src/BuildPhar.php
cp ~/ABridge/Bld/*.*  /C/xampp/htdocs
rm /C/xampp/htdocs/ABridge.php
cp /C/xampp/htdocs/ABridge_prod.php /C/xampp/htdocs/ABridge.php