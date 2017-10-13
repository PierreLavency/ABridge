#!/bin/bash
# declare STRING variable

Phase="dev"
BUILD="~/ABridge/Bld/"


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

cp ~/ABridge/* \\DESKTOP-ES4KJA9\Users\Default\Documents\ABridge\
cp -R ~/ABridge/Src \\DESKTOP-ES4KJA9\Users\Default\Documents\Src



