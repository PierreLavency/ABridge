#!/bin/bash
# declare STRING variable
Phase="dev"

while getopts ":p:" opt; do
  case $opt in
    p)
	  Phase=$OPTARG
      echo "-p was triggered, Parameter: $OPTARG" >&2
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
	echo "pchpcbf"
	phpcbf --standard=PSR2 Src
	echo "phpcs"
	phpcs -p --standard=PSR2 --report=summary  Src
	echo "pdepend"
	pdepend --summary-xml=tmp/summary.xml --jdepend-chart=tmp/jdepend.svg --overview-pyramid=tmp/pyramid.svg Src	
	echo "phpunit"
	phpunit --coverage-html tmp/Testcoverage
exit
fi

echo -e "full check \n"
echo "pchpcbf"
phpcbf --standard=PSR2 Src
phpcbf --standard=PSR2 Tests
echo "pdepend"
pdepend --summary-xml=tmp/summary.xml --jdepend-chart=tmp/jdepend.svg --overview-pyramid=tmp/pyramid.svg Src
echo "phpcs"
phpcs -p --standard=PSR2 --report=summary  Src
echo "phpmd"
echo " Mod"
phpmd Src/Mod/ html unusedcode,codesize,naming,design,cleancode,controversial --reportfile tmp/phpMD/Mod.html
echo " Hdl"
phpmd Src/Hdl/ html unusedcode,codesize,naming,design,cleancode,controversial --reportfile tmp/phpMD/Hdl.html
echo " Usr"
phpmd Src/Usr/ html unusedcode,codesize,naming,design,cleancode,controversial --reportfile tmp/phpMD/Usr.html
echo " Log"
phpmd Src/Log/ html unusedcode,codesize,naming,design,cleancode,controversial --reportfile tmp/phpMD/Log.html
echo " View"
phpmd Src/View/ html unusedcode,codesize,naming,design,cleancode,controversial --reportfile tmp/phpMD/View.html
echo ""
echo "phpunit"
phpunit --coverage-html tmp/Testcoverage
echo "performance Profiling"
cp ~/ABridge/Tests/perf.ini ~/ABridge/Tests/perfTmp.ini
cp ~/ABridge/Tests/perfProfiling.ini ~/ABridge/Tests/perf.ini
php -d xdebug.profiler_enable=1  -d xdebug.profiler_output_dir=./tmp tests/Controler_Perf.php
echo "performance Logging"
cp ~/ABridge/Tests/perfLog.ini ~/ABridge/Tests/perf.ini
php tests/Controler_Perf.php
cp ~/ABridge/Tests/perfTmp.ini ~/ABridge/Tests/perf.ini