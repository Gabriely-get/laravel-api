#!/bin/bash

curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

composer --version
