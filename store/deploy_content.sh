#!/bin/bash
rm -rf pub/static/frontend/ekr/lashbrook/* var/view_preprocessed/*
php bin/magento setup:static-content:deploy
bin/magento cache:flush
