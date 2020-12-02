<?php

$host = "postgres://vrhjkcjrsbpeco:4e9a438ec6f0a3eed52795cd6fee2fa9e4514a0cf99e895c3cd8a7b6db786040@ec2-174-129-255-72.compute-1.amazonaws.com:5432/de28g604k4ne2m";
$username = "vrhjkcjrsbpeco";
$password = "4e9a438ec6f0a3eed52795cd6fee2fa9e4514a0cf99e895c3cd8a7b6db786040";
$dbname = "de28g604k4ne2m";

$dbh = new PDO('pgsql:host=$host;port=5432;dbname=$dbname;user=$username;password=$password');

?>
