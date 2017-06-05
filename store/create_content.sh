#!/bin/bash
rm -rf pub/static/frontend/ekr/lashbrook/* var/view_preprocessed/*
php bin/magento setup:static-content:deploy
chown apache:users var/cache var/di var/generation
bin/magento cache:flush
