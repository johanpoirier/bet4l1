#!/bin/bash

/etc/init.d/mysql start
mysql -uroot -proot -e "CREATE DATABASE bets;"
mysql -uroot -proot bets < /tmp/mysql/schema.sql
