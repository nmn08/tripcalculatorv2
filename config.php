<?php
/*
* Change the value of $password if you have set a password on the root userid
* Change NULL to port number to use DBMS other than the default using port 3306
*
*/
$host = 'durvbryvdw2sjcm5.cbetxkdyhwsb.us-east-1.rds.amazonaws.com';
$user = 't6o2ke3a9bzrrua6';
$password = 'je1xmssd9ya1gm1y'; //To be completed if you have set a password to root
$database = 'tfyzjhuuhqb83uk9'; //To be completed to connect to a database. The database must exist.
$port = 3306; //Default must be NULL to use default port
$mysqli = new mysqli($host, $user, $password, $database, $port);

if ($mysqli->connect_error) {
    die('Connect Error (' . $mysqli->connect_errno . ') '
        . $mysqli->connect_error);
}
?>
