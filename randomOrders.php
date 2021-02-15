<?php
//   error_reporting(E_ALL);
//   ini_set("display_errors", 1);
session_start();
$home = dirname(__FILE__);
$lib = $home ."/lib/";

echo "home: $home<BR>";
echo "lib: $lib<BR>";

$dbArr = array("reece");
$dbUserArr = array("desi2010");
$dbPassArr = array("pura+upa");
$host = 'localhost';
//$dbuser='desi2010';
//$dbpasswd='pura+upa';
$action = $_REQUEST["action"];

if($action == "submit")
{
   function randomDate($start_date, $end_date)
   {
       // Convert to timetamps
       $min = strtotime($start_date);
       $max = strtotime($end_date);

       // Generate random number using above bounds
       $val = rand($min, $max);

       // Convert back to desired date format
       return array(date('Y-m-d h:i:s', $val), $val);
   }

   for($i = 0; $i < count($dbArr); $i++)
   {
      $curDB = $dbArr[$i];
      $dbuser= $dbUserArr[$i];
      $dbpasswd= $dbPassArr[$i];

//      $dbuser='root';
//      $dbpasswd='12345the';

      $mysqli = new mysqli($host, $dbuser, $dbpasswd, $curDB);
      if ($mysqli->connect_errno)
      {
          echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
      }
      echo $mysqli->host_info . "<BR>";

      $query = "select * from products";
      $res = $mysqli->query($query);
      $rowsProd = $res->fetch_assoc();

      for($j = 0; $j < 1000; $j++)
      {
         //get random user
         $userArr = array("1", "123", "5", "6");
         $nameArr = array("Designs To You", "Test User", "Reece Reece", "Cuong Cao");
         $rand_keys = array_rand($userArr, 1);
         $userID =  $userArr[$rand_keys] ;
         $name = $nameArr[$rand_keys];

         $randLines = mt_rand(1,5);
         $orderDate = randomDate("2011-10-08", "2012-10-08");
         $orderDate = $orderDate[0];
         $orderDateUnix = $orderDate[1];
         $curDate = date('Y-m-d h:i:s');
         $curDate2days = strtotime($curDate, "-2 days");
         $curDate2weeks = strtotime($curDate, "-2 weeks");
         $curDate4weeks = strtotime($curDate, "-4 days");

         $days2  = date('Y-m-d h:i:s', strtotime("-2 days"));
         $weeks2  = date('Y-m-d h:i:s', strtotime("-2 weeks"));
         $weeks4 =  date('Y-m-d h:i:s', strtotime("-4 weeks"));

         $bool2days = false;
         $bool2weeks = false;
         $bool4weeks = false;
         $status = "APPROVED";

         if($orderDate < $days2 and $orderDate > $weeks2)
         {
            //echo "2days; $orderDate vs $days2 weeks: $weeks2<BR>";
            $status = "APPROVED";
            $lastupdated = $orderDate;
         }
         else if($orderDate < $weeks2 and $orderDate > $weeks4)
         {
             //echo "2weeks ; $orderDate vs $weeks2 weeks: $weeks4<BR>";
            $status = "PROCESSING";
            $updated = strtotime ( '+1 day' , strtotime ( $orderDate ) ) ;
            $lastupdated = date('Y-m-d h:i:s', $updated);
            echo "lastupdated: $lastupdated orderdate: $orderDate<BR>";
         }
         else if($orderDate < $weeks4)
         {
            //echo "older than 4weeks: $orderDate $weeks4<BR>";
            //random despatched date
            $days = mt_rand(3, 10);
            $updated = strtotime ( "+$days day" , strtotime ( $orderDate ) ) ;
            $lastupdated = date('Y-m-d h:i:s', $updated);
            echo "lastupdated: $lastupdated orderdate: $orderDate completed in: $days<BR>";
            $status = "DELIVERED";

         }

         $locationArr = array("3 Coombes Drive, Penrith, Nsw, 2750",
         "22 Mort Street, Braddon, Act, 2612",
         "87-91 Kirkham Road, Bowral, Nsw, 2576",
         "60 Memorial Avenue, Blackwall, Nsw, 2256",
         "188 Chalmers Street, Surry Hills, Nsw, 2010",
         "891-895 High St, Armadale, Vic, 3143",
         "8 Pilgrim Court, Ringwood, Vic, 3134",
         "2133 Princes Highway, Clayton, Vic, 3168",
         "20 Reliance Drive, Tuggerah, Nsw, 2259",
         "Cnr Beattie & Darling, St Balmain, Nsw, 2041",
         "38 Nepean Highway, Mentone, Vic, 3194",
         "8 Glen Kyle Drive, Maroochydore, Qld, 4558",
         "7 Northview Street, Mermaid Beach, Qld, 4218",
         "100 Norman Street, Woolloongabba, Qld, 4102",
         "149 Old Pacific Highway, Oxenford, Qld, 4210",
         "118 Mica Street, Carole Park, Qld, 4300",
         "42 Stuart Highway, Stuart Park, Nt, 0820",
         "4 Charles Street, Yeppoon, Qld, 4703",
         "Cnr Goodman Crt & Invermay Rd, Launceston, Tas, 7250",
         "17 Don Road, Devonport, Tas, 7310",
         "8 Elliott Street, Midvale, Wa, 6056",
         "53 Osullivans Beach Road, Lonsdale, Sa, 5160",
         "21 Main South Rd, Ohalloran Hill, Sa, 5158",
         "10A Paxton Street, Willaston, Sa, 5118",
         "8 Mervyn Street, Bunbury, Wa, 6230",
         "199-203 Stirling Street, Perth, Wa, 6000",
         "1 Caloundra Road, Clarkson, Wa, 6030",
         "911 Princes Highway, Pakenham, Vic, 3810",
         "82 Barrier St, Fyshwick, Act, 2609",
         "11 Packard Street, Joondalup, Wa, 6027");

          $randLoc = mt_rand(0,29);
          $location = explode(",", $locationArr[$randLoc]);
          $address = trim(strtoupper($location[0]));
          $suburb = trim(strtoupper($location[1]));
          $state = trim(strtoupper($location[2]));
          $postcode = trim(strtoupper($location[3]));

          echo "$street<BR>";
          echo "$suburb<BR>";
          echo "$state<BR>";
          echo "$postcode<BR>";
          $query = "insert into orders(user_id, name, address, suburb, state, postcode, order_time, status, lastupdated, sname, approvaltime, iswages)values($userID, '$name', '$address', '$suburb', '$state', '$postcode', '$orderDate', '$status', '$lastupdated', '$suburb', '$orderDate', 'N')";
          echo "$query<BR>";
          $mysqli->query($query);
          $insert_id = $mysqli->insert_id;

         for($k = 0; $k < $randLines; $k++)
         {

            //random products
            $randrow = mt_rand(0, 44);
            $rand_keys = array_rand($rowsProd, 1);
            $prod_id =  $rowsProd[$randrow]["prod_id"];
            $myob_code = $rowsProd[$randrow]["myob_code"];
            $price = $rowsProd[$randrow]["price"];

            //get random size
            $query = "select * from sizes where prod_id = $prod_id";
            $resSize = $mysqli->query($query);
            $rowsSize = $resSize->fetch_assoc();

            $numSizes = $resSize->num_rows;
            $randSizeIdx = mt_rand(0, $numSizes-1);
            $size = $rowsSize[$randSizeIdx]["size"];

            $randQty = mt_rand(1, 5);

            $query = "insert into lineitems(order_id, prod_id, myob_code, qty, size, price) value($insert_id, $prod_id, '$myob_code', $randQty, '$size', $price)";
            echo "$query<BR>";
            $mysqli->query($query);

   //         $userID =  $userArr[$rand_keys[1]] ;
            echo "date: $orderDate User: $userID pid: $prod_id $myob_code status; $status size: $size qty: $randQty<BR>";
         }
      }

            //insert into location
//            $query = "insert into location (sname, address, suburb, state, postcode, stype, phone, fax, email) values ('$storename', '$address1', '$suburb', '$state', '$postcode', '$brand', '$phone', '$fax', '$email')";
//            $res = $mysqli->query($query);
//            $location_id = "";
//            if($res)
//            {
//               //insert into login with location id
//               $location_id = $mysqli->insert_id;
//               $query = "insert into login (user_id, location_id, user_name, firstname, password, access_level, jurisdiction, realm) value ($storeno, $location_id, '$storeno', '$storename', '$password', '$access_level', '$jurisdiction', '$realm')";
//               $res = $mysqli->query($query);
//               echo "$query<BR>";
//            }



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
