<?php
$dsn = 'mysql:host=mysql;dbname=userdb;charset=utf8mb4';
$pdo = new PDO($dsn, 'user', 'password');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
