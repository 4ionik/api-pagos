<?php

define('DB_NAME', 'app_pagos'); // DATABASE
define('DB_USER', 'app'); // ROOT DEFAULT MYSQL
define('DB_PASSWORD', 'deliveryapp2020$');  // PASSOWORD
define('DB_HOST', 'localhost'); // LOCAL IF YOU USE LOCAL.

$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

?>