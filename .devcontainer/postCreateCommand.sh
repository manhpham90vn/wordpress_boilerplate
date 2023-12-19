#!/usr/bin/env bash

sudo usermod -a -G www-data root
sudo chmod g+w -R /var/www/html

echo "Done...."