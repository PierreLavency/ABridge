#!/bin/bash
# declare STRING variable

Phase="build"
HOMEDIR="/C/Users/pierr/ABridge"
SRCDIR=$HOMEDIR/Src
BUILDIR=$HOMEDIR/Bld
BUILDTMP=$BUILDIR/Tmp
RELDIR="/C/xampp/htdocs"
GETDIR="//DESKTOP-ES4KJA9/Users/Public/Documents"

#GETDIR="/C/Users/pierr/ABridge/tmp/testdir"


while getopts ":p:" opt; do
  case $opt in
    p)
	  Phase=$OPTARG
#      echo "-p was triggered, Parameter: $OPTARG" >&2
      ;;
    \?)
      echo "Invalid option: -$OPTARG" >&2
      exit 1
      ;;
    :)
      echo "Option -$OPTARG requires an argument." >&2
      exit 1
      ;;
  esac
done

if [ $Phase = "dev" ] 
then
	echo -e "releasing dev \n"
	cp $SRCDIR/ABridge_dev.php  $RELDIR/ABridge.php
	cp $SRCDIR/ABridge_dev.php  $RELDIR/ABridgeAPI.php
	cp $SRCDIR/ABridge_init.php $RELDIR/ABridge_init.php
	exit
fi

if [ $Phase = "rel" ] 
then
	echo -e "releasing prod \n"
	cp $BUILDIR/*.*  $RELDIR
	exit
fi

if [ $Phase = "get" ] 
then
	echo -e "releasing from "$GETDIR"\n"
	cp $GETDIR/ABridge.php      $RELDIR/ABridge.php
	cp $GETDIR/ABridge.phar     $RELDIR/ABridge.phar
	cp $GETDIR/ABridge_init.php $RELDIR/ABridge_init.php
	cp $GETDIR/ABridgeAPI.php   $RELDIR/ABridgeAPI.php
	cp $GETDIR/config.ini       $RELDIR/config.ini
	exit
fi

if [ $Phase = "put" ] 
then
	echo -e "copping to "$GETDIR"\n"
	cp $BUILDIR/*.*  $GETDIR
	exit
fi

echo -e "building release \n"

rm $BUILDIR/*.*
rm -R $BUILDTMP/Src
rm -R $BUILDTMP/DataStore
rm -R $BUILDTMP/App
rm -R $BUILDTMP/vendor

cp -R $SRCDIR $BUILDTMP
cp $SRCDIR/config.ini       $BUILDIR/config.ini
cp $SRCDIR/ABridge_prod.php $BUILDIR/ABridge.php
cp $SRCDIR/ABridge_prod.php $BUILDIR/ABridgeAPI.php
cp $SRCDIR/ABridge_init.php $BUILDIR/ABridge_init.php

rm $BUILDTMP/Src/config.ini
rm $BUILDTMP/Src/ABridge_dev.php
rm $BUILDTMP/Src/ABridge_test.php
rm $BUILDTMP/Src/ABridge_prod.php
rm $BUILDTMP/Src/ABridge_init.php

cp -R ~/ABridge/Datastore $BUILDTMP
cp -R ~/ABridge/App $BUILDTMP

mkdir $BUILDTMP/vendor
cp $HOMEDIR/vendor/autoload.php $BUILDTMP/vendor/autoload.php
cp -R $HOMEDIR/vendor/composer $BUILDTMP/vendor/composer
cp -R $HOMEDIR/vendor/symfony  $BUILDTMP/vendor/symfony
cp -R $HOMEDIR/vendor/myclabs  $BUILDTMP/vendor/myclabs

php ~/ABridge/Src/BuildPhar.php




