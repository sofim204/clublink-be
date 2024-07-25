ll
sudo su
sudo apt-get dist-upgrade
sudo apt install cifs-utils
sudo systemctl disable gcs
sudo systemctl stop gcs
sudo nano /etc/fstab 
ll
sudo nano /etc/php/7.2/fpm/php-fpm.conf 
sudo nano /etc/php/7.2/fpm/php.ini 
sudo nano /etc/php/7.2/fpm/pool.d/www.conf 
sudo /etc/init.d/php7.2-fpm restart
curl localhost:9000
ifconfig
sudo nano /etc/php/7.2/fpm/pool.d/www.conf 
sudo reboot
cd /var/www/html/
ll
cd media/
ll
cd ..
ll
sudo nano /etc/fstab 
sudo reboot
sudo systemctl var-www-html-media.mount
sudo systemctl status var-www-html-media.mount
ls /lib/modules/$(uname -r)/kernel/fs/nls/nls_utf8.ko
sudo apt install linux-modules-extra-$(uname -r)-generic
sudoapt install linux-generic
sudo apt install linux-generic
ls /lib/modules/$(uname -r)/kernel/fs/nls/nls_utf8.ko
sudo reboot
sudo systemctl status var-www-html-media.mount
sudo nano /etc/fstab 
sudo mount -a
man mount.cifs 
sudo nano /etc/fstab 
man mount.cifs 
sudo mount -a
sudo poweroff
curl localhost
sudo nano /etc/nginx/sites-enabled/default 
sudo /etc/init.d/nginx restart
sudo /etc/init.d/nginx reload
curl localhost
sudo nano /etc/php/7.2/fpm/pool.d/www.conf 
sudo nano /etc/nginx/sites-enabled/default 
sudo /etc/init.d/php7.2-fpm restart
sudo /etc/init.d/nginx restart
sudo /etc/init.d/nginx reload
curl localhost
curl localhost/a
curl localhost/a/admin
poweroff
sudo poweroff
sudo apt-get install php7.2-curl php7.2-zip php7.2-gd
sudo apt-get install php7.2-xml php7.2-json 
sudo apt-get install php7.2-curl 
sudo nano /etc/ssh/sshd_config 
sudo adduser gits_sellon
usermod -aG sudo gits_sellon
sudo usermod -aG sudo gits_sellon
sudo /etc/init.d/ssh restart
ll
rm -rf sellon-df828-db4a21c9dbc2.json 
ll
wget https://github.com/prometheus/node_exporter/releases/download/v1.0.0-rc.0/node_exporter-1.0.0-rc.0.linux-amd64.tar.gz
tar -xzf node_exporter-1.0.0-rc.0.linux-amd64.tar.gz 
ll
cd node_exporter-1.0.0-rc.0.linux-amd64/
ll
./node_exporter 
sudo cp node_exporter /usr/sbin/node_exporter
node_exporter 
sudo nano /etc/systemd/system/node_exporter.service
sudo systemctl daemon-reload 
sudo systemctl enable node_exporter.service 
sudo systemctl start node_exporter.service 
sudo systemctl status node_exporter.service 
sudo nano /etc/sysconfig/node_exporter
sudo useradd --no-create-home --shell /bin/false node_exporter
sudo chown node_exporter:node_exporter /usr/local/bin/node_exporter
sudo nano /etc/default/node_exporter
sudo nano /etc/systemd/system/node_exporter.service
sudo systemctl daemon-reload 
sudo systemctl enable node_exporter.service 
sudo systemctl start node_exporter.service 
sudo systemctl status node_exporter.service 
sudo su
ls
rm -rf node_exporter-1.0.0-rc.0.linux-amd64.tar.gz 
ll
ls
cd /var/www/html/
cd app/
ls
cd ..
exit
ll
rm star.sellon.net.key chain.sellon.net.crt 
ll
cd /var/www/html/
ll
rm s3demo_sellon-demo.sql 
ll
sudo rm s3demo_sellon-demo.sql 
ll
ls
cd sql/
ll
scp gits_sellon@10.148.0.30:~/ ./
scp -R gits_sellon@10.148.0.30:~/ ./
scp -r gits_sellon@10.148.0.30:~/ ./
ll
cd gits_sellon/update/
ll
sudo cp app/ /var/www/html/
sudo cp -r app/ /var/www/html/
ll
sudo cp -r media/ /var/www/html/media/
ll
cd ..
rm -rf gits_sellon/
cd /var/www/html/media/
ll
cd media/
ll
sudo mv icon/ ../
ll
cd ..
ll
cd 
poweroff
sudo poweroff
scp -r gits_sellon@10.148.0.30:~/update ./
ll
cd update/
ll
which php
ll
sudo cp -r app/ /var/www/html/app/
cd
cd /var/www/html/app/
ll
cd controller/api_mobile/
ll
cat produk.php 
cd ..
sudo poweroff
scp -r gits_sellon@10.148.0.30:~/update ./
ll
sudo cp -r app/ /var/www/html/app/
cd update/
sudo cp -r app/ /var/www/html/app/
exit
sudo poweroff
scp -r gits_sellon@10.148.0.30:~/update ./
cd update/
sudo cp -r app/ /var/www/html/app/
cd ..
ls
rm -rf update/
poweroff
sudo poweroff
scp -r gits_sellon@10.148.0.30:~/update ./
cd update/
sudo cp -r app/ /var/www/html/app/
sudo poweroff
cd /var/www/html/
ll
sudo chmod 775 *
ll
sudo chmod -R 775 *
ll
sudo chmod -R 777 app/
sudo poweroff
poweroff
sudo poweroff
sudo nano /etc/php/7.2/fpm/php.ini 
sudo poweroff
sudo nano /etc/netdata/netdata.conf 
sudo poweroff
sudo su
scp -r gits_sellon@10.148.0.30:~/update ./
ll
cd update/
ll
cd ..
ll
mkdir new
cd new/
scp -r gits_sellon@10.148.0.30:~/update ./
ll
cd update/
ll
sudo cp -r ./ /var/www/html/
poweroff
sudo poweroff
sudo nano /etc/php/7.2/fpm/php.ini 
sudo tail -f /var/log/php7.2-fpm.log 
sudo tail -f /var/log/nginx/error.log
sudo nano /etc/php/7.2/fpm/php.ini 
sudo apt-get install php7.2-redis
sudo tail -f /var/log/nginx/error.log
sudo apt-get install php-redis 
sudo /etc/init.d/php7.2-fpm restart
sudo /etc/init.d/php7.2-fpm reload
sudo tail -f /var/log/nginx/error.log
sudo poweroff
