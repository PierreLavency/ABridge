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
	phpcbf --standard=PSR2 --no-patch  Src
	echo "phpcs"
	phpcs --standard=PSR2 --report=summary  Src
	echo "phpunit"
	phpunit --coverage-html Testcoverage
exit
fi

echo -e "full check \n"
echo "pchpcbf"
phpcbf --standard=PSR2 --no-patch  Src
phpcbf --standard=PSR2 --no-patch  Tests
echo "pdepend"
pdepend --summary-xml=tmp/summary.xml --jdepend-chart=tmp/jdepend.svg --overview-pyramid=tmp/pyramid.svg Src
echo "phpcs"
phpcs --standard=PSR2 --report=summary  Src
echo "phpunit"
phpunit --coverage-html Testcoverage
echo "performance"
php -d xdebug.profiler_enable=1  -d xdebug.profiler_output_dir=./tmp tests/Controler_Perf.php
