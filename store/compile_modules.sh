#!/bin/bash
php bin/magento setup:upgrade
php bin/magento setup:di:compile
bin/magento cache:flush
chown apache:users var/cache var/di var/generation
