#!/bin/bash
#Variables
export riegoweb=/var/www/html
MYDATETIME=`date +%Y-%m-%d`" "${HORA}

#Proceso
cp -p $riegoweb/riegosolar/imagenes/* $riegoweb/riegosolar_bck/imagenes
cd $riegoweb
git config --global core.editor vi
git config --global user.name “uselinuxdev”
git config --global user.email eusebio.antonio.castro@gmail.com
##########rm -rf riegosolar
git clone https://github.com/uselinuxdev/webriegoberry.git riegosolar >> $riegoweb/logclonweb.log
cd $riegoweb
cp $riegoweb/riegosolar_bck/imagenes/* $riegoweb/riegosolar/imagenes
cp $riegoweb/riegosolar/resetgit.sh $riegoweb/resetgit.sh
# Log de actalización.
echo "Web actualizada:${MYDATETIME}." >> $riegoweb/logclonweb.log


