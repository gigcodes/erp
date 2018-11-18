<?php

require_once __DIR__.'logger.php';

simple_log('statutory start');

file_get_contents('http://erp.sololuxury.co.in/recurringTask');

simple_log('statutory stop');