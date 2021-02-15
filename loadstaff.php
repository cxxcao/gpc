<?php
   error_reporting(E_ALL);
   ini_set("display_errors", 1);
session_start();
$home = dirname(__FILE__);
$lib = $home ."/lib/";

echo "home: $home<BR>";
echo "lib: $lib<BR>";


require_once($lib . 'database.php');
require_once($lib . 'functions.php');
require_once($lib . 'dbfunctions.php');
require_once($home . '/globals.php');


$action = _checkIsSet("action");

if($action == "submit")
{
   if ($_FILES["file"]["error"] > 0)
   {
      $_SESSION['msg'] = '<font color="'._FAILED_COLOR.'">ERROR UPLOADING FILE, PLEASE TRY AGAIN</font>';
   }
   else
   {
      $target_path = "$home/uploads/";
      $target_path = $target_path . basename($_FILES['file']['name']);
     $filename = $_FILES['file']['tmp_name'];

      echo "FILENAME: $filename [$target_path]<BR>";
   }

   echo "<table><tr><td>STAFF ID</td><td>Username</td><td>Surname</td><td>Firstname</td><td>JUR</td><td>emp_type</td><td>LocationID</td><tr>";
   if(($handle = fopen($filename,"r")) == NULL)
   {
      echo "FAILED TO OPEN FILE<BR>";
   }
   else
   {
      $contents = fgets($handle);
//      echo "$contents<BR>";
      $j = 1;
      while(!feof($handle))
      {
         $contents = fgets($handle);
         //echo "$contents<BR><BR>";
         $lineArr = explode(",", $contents);
/*         
ID #	
First Name	
Surname	
Gender	
Business Unit	
Entity	
Department	
Department Description	
Admin / Tech	
# of Days	Uniform Coordinator
Entitled to Uniform Y/N	
ISMED

UNIFORM TYPE:
1 = admin
2 = tech
3 = drjones
*/
         
         $eid = trim($lineArr[0]);
         //$firstname = trim($lineArr[1]);
         //$surname = trim($lineArr[2]);
         //$gender = trim($lineArr[3]);
         
         $gender = "1";
         $firstname = "";
         $surname = "";
         
         $eid = trim($lineArr[7]);     
         $firstname = trim($lineArr[8]); 
         $email = trim($lineArr[9]);
         $lid  = trim($lineArr[10]);

         $crange = 1;
         
        // $fullname = "$firstname $surname";

         $jurisdiction = "CHEMMART";
         $allowance =0;
         $access_level = 2;
         $uniform_type = 1;
         $daysworked = 5;
         
       	$password = md5(ucwords(strtolower("Chemmart")));
         $realm = "CHEMMART";
         
$query = "INSERT INTO `login` (`location_id`, `user_name`, `firstname`, `lastname`, `password`, `access_level`, `email`, `jurisdiction`, `allowance`, `allowance2`, `realm`, `role_id`, `crange`, `job_classification`, `status`, `isAUS`, `daysworked`, `start_date`)
		 VALUES ('$lid', '$eid', '$firstname', '$surname', '$password', '2', '$email', '$jurisdiction', '0', '0', 'CHEMMART', '$uniform_type', '$crange', '', 'ACTIVE', 'Y', '$daysworked', '2016-05-23')";

         echo "$query<BR>";
         $res = db_query($query);
         $staffid = mysql_insert_id();

         echo "<tr><td>$staffid</td><td>$eid</td><td>$email</td><td>$gender</td><td>$surname</td><td>$firstname</td><td>$jurisdiction</td><td>$eid</td><td>$lid</td><tr>";
      }
   }
   echo "</table>";
//   echo "J: $j<BR>";
//      else
//      {
//         $i = 0;
//         $_SESSION['msg'] = "";
//         while (!feof($handle))
//         {
//            $contents = fgets($handle, 1024);

}

?>

<html>
<body>

<form enctype="multipart/form-data" name="list" method="post">
<br>
File:<input type="file" size="50" name="file"><br>

<input type="submit" name="action" value="submit">

</form>


</body>
</html>
