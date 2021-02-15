<?php
   error_reporting(E_ALL);
   ini_set("display_errors", 1);
session_start();
$home = dirname(__FILE__);
$lib = $home ."/lib/";

echo "home: $home<BR>";
echo "lib: $lib<BR>";

$dbArr = array("chemmart");
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

      $dbuser='root';
      $dbpasswd='12345the';

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
         $j = 1;
         while(!feof($handle))
         {
            $contents = fgets($handle);
            //echo "$contents<BR><BR>";
            //Business Name	Hospital	BU	stype	COST CENTRE	ENTITY	CLINIC NAME	ADDRESS	Suburb	STATE	POST CODE	PHONE	FAX	OFFICE MANAGER	EMAIL
            
            $lineArr = explode(",", $contents);
            $bu = "";
            $hospital = "N";
            $businessName = "";
            $fax = "";
            $entity = "";
            
            $branch_id = trim($lineArr[0]);            
            $sname = trim($lineArr[1]);
            $address = trim($lineArr[2]);
            $suburb = strtoupper(trim($lineArr[3]));
            $state = strtoupper(trim($lineArr[4]));
            $postcode = trim($lineArr[5]);
            $phone = trim($lineArr[6]);
            $symbion = trim($lineArr[7]);
            $manager = trim($lineArr[8]);
            $email = trim($lineArr[9]);
            $stype = "Pharmacy";
            


            $query = "INSERT INTO `location` (`branch_id`, `business_name`, `entity`, `business_unit`, `hospital`, `sname`, 
            `address`, `suburb`, `state`, `postcode`, `country`, `stype`, `phone`, `fax`, `email`, `status`) VALUES 
            		('$branch_id', '$businessName', '$entity', '$bu', '$hospital', '$sname', 
            		'$address', '$suburb', '$state', '$postcode', 'AU', '$stype', '$phone', '$fax', '$email', 'ACTIVE')";
            
         $res = $mysqli->query($query);

echo "$query<BR>";
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
