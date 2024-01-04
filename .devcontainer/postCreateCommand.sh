#!/usr/bin/env bash

sudo chown -R $USER:$USER /var/www/html
sudo chown -R $USER:$USER /home/vscode

git clone --depth 1 --branch master https://github.com/zsh-users/zsh-autosuggestions ${ZSH_CUSTOM:-~/.oh-my-zsh/custom}/plugins/zsh-autosuggestions
git clone --depth 1 --branch master https://github.com/zsh-users/zsh-syntax-highlighting.git ${ZSH_CUSTOM:-~/.oh-my-zsh/custom}/plugins/zsh-syntax-highlighting
sed -i 's/plugins=(git)/plugins=(git zsh-autosuggestions zsh-syntax-highlighting)/' ~/.zshrc

sudo sed /etc/php/8.2/fpm/pool.d/www.conf -i -e "s/user = www-data/user = $USER/g"
sudo sed /etc/php/8.2/fpm/pool.d/www.conf -i -e "s/group = www-data/group = $USER/g"
sudo sed /etc/php/8.2/fpm/pool.d/www.conf -i -e "s/listen.owner = www-data/listen.owner = $USER/g"
sudo sed /etc/php/8.2/fpm/pool.d/www.conf -i -e "s/listen.group = www-data/listen.group = $USER/g"
sudo sed /etc/php/8.2/fpm/php.ini -i -e "s/upload_max_filesize = 2M/upload_max_filesize = 100M/g"
sudo sed /etc/php/8.2/fpm/php.ini -i -e "s/post_max_size = 8M/post_max_size = 100M/g"
sudo sed /etc/nginx/nginx.conf -i -e "s/user www-data/user $USER/g" 

sudo service nginx start
sudo service php8.2-fpm start

echo "Done...."