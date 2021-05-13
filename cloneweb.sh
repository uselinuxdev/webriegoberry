#!/bin/bash
#Variables
export riegoweb=/var/www/html
MYDATETIME=`date +%Y-%m-%d`" "${HORA}

#Proceso
######rm -rf $riegoweb/riegosolar_bck
cp -pr $riegoweb/riegosolar $riegoweb/riegosolar_bck
cd $riegoweb/riegosolar
git checkout .
git pull >> $riegoweb/logclonweb.log
cd $riegoweb
cp $riegoweb/riegosolar_bck/imagenes/* $riegoweb/riegosolar/imagenes
cp $riegoweb/riegosolar/cloneweb.sh riegoweb/cloneweb.sh
# Log de actalización.
echo "Web actualizada:${MYDATETIME}." >> $riegoweb/logclonweb.log

