<?php
//
// SourceForge: Breaking Down the Barriers to Open Source Development
// Copyright 1999-2000 (c) The SourceForge Crew
// http://sourceforge.net
//
// $Id: database.php,v 1.6 2000/04/11 14:17:13 cvs Exp $
//
// /etc/local.inc includes the machine specific database connect info
//
$sys_dbhost='localhost';
$sys_dbuser='root';
$sys_dbpasswd='12345the';
$sys_dbname='gpc';
// error_reporting(1);
// $sys_dbhost='localhost';
// $sys_dbuser='desi2010';
// $sys_dbpasswd='pura+upa';
// $sys_dbname='sprnpiul_gpc';

//uat
// $sys_dbhost='localhost';
// $sys_dbuser='reeceuat';
// $sys_dbpasswd='design2017';
// $sys_dbname='uatdesig_gpc';


function mysql_bit($bit) {
   echo "bit: [".ord($bit)."]<BR>";
if(ord($bit) == 1)
return 1;
else
return 0;
}

/*
function db_connect() {
	global $sys_dbhost,$sys_dbuser,$sys_dbpasswd;
	
	$conn = mysqli_connect($sys_dbhost,$sys_dbuser,$sys_dbpasswd);
    $errNo = mysqli_errno($conn);
    	
	//$conn = new mysqli("$sys_dbhost", "$sys_dbuser", "$sys_dbpasswd", "$sys_dbname") or die($mysqli->connect_error);
	if ($mysqli->connect_errno) {
		echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
	}
	
	if (!$conn) {
		echo $mysqli->connect_error;
	}
	return $conn;
}
*/

function db_connect() {

    // Define connection as a static variable, to avoid connecting more than once 
    static $connection;
    global $sys_dbhost,$sys_dbuser,$sys_dbpasswd,$sys_dbname;

    // Try and connect to the database, if a connection has not been established yet
    if(!isset($connection)) {
         // Load configuration as an array. Use the actual location of your configuration file
        $connection = mysqli_connect($sys_dbhost,$sys_dbuser,$sys_dbpasswd,$sys_dbname);
    }

    // If connection was not successful, handle the error
    if($connection === false) {
        // Handle error - notify administrator, log to a file, show an error screen, etc.
        return mysqli_connect_error(); 
    }
    return $connection;
}

function db_query($query) {
    // Connect to the database
    $connection = db_connect();
    // Query the database
//     echo "QUERY: $query<BR>\n";
    $result = mysqli_query($connection,$query);
    $errNo = mysqli_errno($connection);
    $msg = mysqli_error($connection);
// echo "\n\n $msg<BR>\n";
    return $result;
}


/*
function db_query($qstring,$print=0) {
	$connection = db_connect();

// Query the database
// echo "$qstring<BR>";
$result = mysqli_query($connection,$qstring);
return $result;
//	global $sys_dbname;
	//return @mysqli_query($qstring);
	//@mysql($sys_dbname,$qstring);
}
*/

function db_numrows($qhandle) {
	// return only if qhandle exists, otherwise 0
	if ($qhandle) {
		return @mysqli_num_rows($qhandle);
	} else {
		return 0;
	}
}

// function db_result($qhandle,$row,$field) {
// 	mysqli_
// 	return @mysqli_result($qhandle,$row,$field);
// }

// function db_result($result,$row,$field) { 
function db_result($res1,$row,$col){ 
    $numrows = mysqli_num_rows($res1); 
    if ($numrows && $row <= ($numrows-1) && $row >=0)
    {
        mysqli_data_seek($res1,$row);
        $resrow = mysqli_fetch_assoc($res1);
        $resrow = array_change_key_case ($resrow, CASE_LOWER);
        $col = strtolower($col);
        //echo "\n\n== RES: " . $resrow[$col] . " COL: $col<BR/>\n";
        if (isset($resrow[$col])){
            return $resrow[$col];
        }
    }
    return false;
}


function db_numfields($lhandle) {
	return @mysql_numfields($lhandle);
}

function db_fieldname($lhandle,$fnumber) {
           return @mysql_fieldname($lhandle,$fnumber);
}

function db_affected_rows($qhandle) {
	return @mysql_affected_rows();
}

function db_fetch_array($qhandle) {
	return mysqli_fetch_array($qhandle);
}

function db_insertid($qhandle) {
	return @mysqli_insert_id($qhandle);
}

function db_error() {
	return "\n\n<P><B>".@mysql_error()."</B><P>\n\n";
}

//connect to the db
//I usually call from pre.php
db_connect();

?>

