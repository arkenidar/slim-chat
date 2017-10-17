# get sources
```shell
git clone https://github.com/arkenidar/slim-chat
cd slim-chat/
```
# install software with PHP's Composer (package manager)
```shell
composer install
```
# run a local server (uses PHP's built-in web-server)
```shell
./server.sh
```
# use the application from the local server
http://0.0.0.0:8080

# setup the db
http://0.0.0.0:8080/util/db_setup

# to fix the "driver not found" error message (when using "sqlite" db type):
```shell
sudo apt install php-sqlite3 && sudo service apache2 restart
```

# to install the LAMP STACK on Ubuntu (for using Apache, in place of PHP's built-in server):
https://sites.google.com/site/dariocangialosi/linuxapachemysqlphp-lamp-on-ubuntu
