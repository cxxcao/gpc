<?php
   error_reporting(E_ALL);
   ini_set("display_errors", 1);
session_start();
$home = dirname(__FILE__);
$lib = $home ."/lib/";

echo "home: $home<BR>";
echo "lib: $lib<BR>";

$dbArr = array("reece_live");
//$dbArr = array("reece2");
$dbUserArr = array("desi2010");
$dbPassArr = array("pura+upa");
$host = 'localhost';
$dbuser='desi2010';
$dbpasswd='pura+upa';
$action = $_REQUEST["action"];

if($action == "submit")
{
   for($i = 0; $i < count($dbArr); $i++)
   {
      $curDB = $dbArr[$i];
      $dbuser= $dbUserArr[$i];
      $dbpasswd= $dbPassArr[$i];

//      $dbuser='root';
//      $dbpasswd='12345the';

      $mysqli = new mysqli($host, $dbuser, $dbpasswd, $curDB);
      if ($mysqli->connect_errno) {
          echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
          exit;
      }
      echo $mysqli->host_info . "<BR>";

      //read file and insert!
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

      echo "<table><tr><td>location_id</td><td>storeno</td><td>branch name</td><td>address</td><td>suburb</td><td>state</td><td>postcode</td><td>country</td><td>stype</td><td>phone</td><tr>";
      if(($handle = fopen($filename,"r")) == NULL)
      {
         echo "FAILED TO OPEN FILE<BR>";
      }
      else
      {
         $query = "select * from location";
         $res = $mysqli->query($query);
         $num = $res->num_rows;

         if($num > 0)
         {
            for($i = 0; $i < $num; $i++)
            {
               $row = $res->fetch_assoc();
               $branch_id = trim($row["branch_id"]);
               $location_id = trim($row["location_id"]);
               $sname = trim($row["sname"]);
               $address = trim($row["address"]);
               $suburb = trim($row["suburb"]);
               $state = trim($row["state"]);
               $postcode = trim($row["postcode"]);
               $country = trim($row["country"]);
               $stype = trim($row["stype"]);
               $phone = trim($row["phone"]);
               $fax = trim($row["fax"]);
               $email = trim($row["email"]);
               $status = trim($row["status"]);

               $location_id2 = md5(strtoupper($branch_id) . strtoupper($address) . strtoupper($suburb). strtoupper($postcode));

               $updatequery = "update location set location_id='$location_id2' where location_id = $location_id";
               $mysqli->query($updatequery);
            }
         }
      }
   }
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
