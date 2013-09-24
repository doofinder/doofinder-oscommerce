#!/bin/bash
if [ $# -lt 1 ]
then
  echo "Usage: $0 version"
  exit 1
fi

mkdir -p doofinder
cp -r catalog doofinder
# cp logo.* doofinder
# cp *.tpl doofinder
# cp -r css doofinder
cp README.md doofinder
zip -r doofinder-osc-v$1.zip doofinder
cp doofinder-osc-v$1.zip doofinder-osc-latest.zip
rm -Rf doofinder
