<?php
$home = dirname(__FILE__) . "/../";
$lib = $home . "/lib";

require_once($home . '/globals.php');
require_once($lib . '/functions.php');
require_once($lib . '/htmlGenerator.php');
require_once($lib . '/phpmailer/class.phpmailer.php');

class location
{
   var $location_id;
   var $branch_id;
   var $sname;
   var $address;
   var $suburb;
   var $state;
   var $postcode;
   var $stype;
   var $phone;
   var $fax;
   var $email;
   var $action;
   var $country;
   var $business_name;
   var $business_unit;
   var $hospital;
   var $entity;
   var $status;
   var $jurisdiction;

   function __construct()
   {
      $this->location_id = "";
      $this->branch_id = "";
      $this->sname = "";
      $this->address = "";
      $this->suburb = "";
      $this->state = "";
      $this->postcode = "";
      $this->stype = "";
      $this->phone = "";
      $this->fax = "";
      $this->email = "";
      $this->country = "";
      $this->business_name = "";
      $this->business_unit = "";
      $this->hospital = "N";
      $this->entity = "";
      $this->status = "";
      $this->jurisdiction = "";
      $this->action = "SAVE";
   }

   function save()
   {
      db_query(_BEGIN);
      //query_val = locationid
      $req_addressfields = array("branch_id", "sname", "address", "suburb", "state", "postcode", "stype", "phone", "fax", "email", "country",
      		"business_name","business_unit","hospital","entity", "status");
      
      $return_addressfields = GenericCheckAndSync($req_addressfields, false);

      $this->SetValues($return_addressfields);

      $return_size = count($return_addressfields);
      $required_size = count($req_addressfields);

      $branch_id = $this->branch_id;
      $sname = $this->sname;
      $address = $this->address;
      $suburb = $this->suburb;
      $state = $this->state;
      $postcode = $this->postcode;
      $stype = $this->stype;
      $phone = $this->phone;
      $fax = $this->fax;
      $email = $this->email;
      $country = $this->country;
      $business_name = $this->business_name;
      $business_unit = $this->business_unit;
      $hospital = $this->hospital;
      $entity = $this->entity;
      $status = $this->status;

      if($this->action == "SAVE")
         $query = "INSERT INTO location (`branch_id`, `sname`, `address`, `suburb`, `state`, `postcode`, `stype`, `phone`, `fax`, `email`, `country`, `business_name`,`business_unit`,`hospital`,`entity`, `status`) VALUES ('$branch_id', '$sname', '$address', '$suburb', '$state', '$postcode', '$stype', '$phone', '$fax', '$email', '$country', '$business_name','$business_unit','$hospital','$entity', '$status')";
      else
      {
         $lid = $this->location_id;
         $query = "UPDATE location SET 
          `business_name`= '$business_name',`business_unit`= '$business_unit',`hospital`= '$hospital',`entity`= '$entity', `status`= '$status',
         `branch_id` = '$branch_id', `sname` = '$sname', `address` = '$address', `suburb` = '$suburb', `state` = '$state', `postcode` = '$postcode', `stype` = '$stype', `phone` = '$phone', `fax` = '$fax', `email` = '$email', `country` = '$country' WHERE (`location_id` = $lid)";
      }
//echo "$query<BR>";
      $res = db_query($query);
      if($res)
      {
         db_query(_COMMIT);
         return true;
      }
      else
      {
         db_query(_ROLLBACK);
         return false;
      }
   }

   function delete()
   {
      $idArr = _checkIsSet("itemArr");
      $num = count($idArr);
      $numdel = 0;

      for($i = 0; $i < $num; $i++)
      {
         $curId = $idArr[$i];
         $query = "delete from location where location_id = $curId";
         if(db_query($query))
            $numdel++;
      }
      if($numdel > 0)
         return true;
      else
         return false;

   }

   function LoadLocationId($id)
   {
      $query = "select * from location where location_id = '$id'";
//      echo "$query<BR>";
      $res = db_query($query);
      $num = db_numrows($res);
      if($num > 0)
      {
         $this->location_id = db_result($res, 0, "location_id");
         $this->branch_id = db_result($res, 0, "branch_id");
         $this->sname = db_result($res, 0, "sname");
         $this->address = db_result($res, 0, "address");
         $this->suburb = db_result($res, 0, "suburb");
         $this->state = db_result($res, 0, "state");
         $this->postcode = db_result($res, 0, "postcode");
         $this->stype = db_result($res, 0, "stype");
         $this->phone = db_result($res, 0, "phone");
         $this->fax = db_result($res, 0, "fax");
         $this->email = db_result($res, 0, "email");
         $this->country = db_result($res, 0, "country");
         $this->business_name = db_result($res, 0, "business_name");
         $this->business_unit = db_result($res, 0, "business_unit");
         $this->hospital = db_result($res, 0, "hospital");
         $this->entity = db_result($res, 0, "entity");
         $this->status = db_result($res, 0, "status");
         
         
         $this->jurisdiction = "GPC";
         $this->action = "UPDATE";
      }
      else
         return false;
   }


   function SetValues($fields)
   {
      //$this->location_id = $fields{"location_id"};
      $this->sname = $fields{"sname"};
      $this->branch_id = $fields{"branch_id"};//costcentre
      $this->address = $fields{"address"};
      $this->suburb = $fields{"suburb"};
      $this->state = $fields{"state"};
      $this->postcode = $fields{"postcode"};
      $this->stype = $fields{"stype"};
      $this->phone = $fields{"phone"};
      $this->fax = $fields{"fax"};
      $this->email = $fields{"email"};
      $this->country = $fields{"country"};
      $this->business_name = $fields{"business_name"};
      $this->business_unit = $fields{"business_unit"};
      $this->hospital = $fields{"hospital"};
      $this->entity = $fields{"entity"};
      $this->status = $fields{"status"};

   }

}
?>
