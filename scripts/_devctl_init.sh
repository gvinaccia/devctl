#!/bin/sh

echo -n "verifica file di configurazione..."

if [ ! -f /etc/nginx/conf.d/dev.conf ]
then
  CONTENT="include ${HOME}/dev/conf/sites-enabled/*.conf;"
  echo $CONTENT | sudo tee /etc/nginx/conf.d/dev.conf > /dev/null
  echo "CREATO"
else
  echo "OK"
fi

echo -n "verifica presenza cartella configurazioni locali..."

if [ ! -d "$HOME/dev/conf" ] 
then
  mkdir -p $HOME/dev/conf
fi

if [ ! -d "$HOME/dev/conf/sites-enabled" ] 
then
  mkdir -p $HOME/dev/conf/sites-enabled
  echo "CREATA"
else
  echo "OK"
fi


