<?php 
//done connection
$servername = 'localhost';
$username   = 'root';
$pass       = '';
$dbname     = 'to_do_list_api';

$conn = new PDO("mysql:host=$servername; dbname=$dbname", $username, $pass);
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

//echo "connection done";


?>