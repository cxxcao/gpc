<?php
$home = dirname(__FILE__) . "/../";
$lib = $home ."lib/";

require_once($lib . 'database.php');
require_once($lib . 'functions.php');
require_once($home . '/globals.php');

   function GenericDelete($table, $arrName, $idName)
   {
      $pidArr = _checkIsSet($arrName);

      if($pidArr)
      {
         $num_del = 0;
         db_query(_BEGIN);
         for($i = 0; $i < count($pidArr); $i++)
         {
            $pid = $pidArr[$i];
            if($pid != "on")
            {
               $query1 = "DELETE FROM `$table` WHERE (`$idName` = '$pid')";
//               echo "$query1<br>";
               $res1 = db_query($query1);
               if($res1)
               {
                  $num_del++;
               }
               else
               {
                  db_query(_ROLLBACK);
                  $_SESSION['msg'] = 'AN ERROR OCCURRED WHILE TRYING TO DELETE, PLEASE TRY AGAIN';
                  return false;
               }
            }
         }

         if($num_del > 0)
         {
            db_query(_COMMIT);
            $_SESSION['msg'] = 'ITEMS SUCCESSFULLY DELETED';
            return true;
         }
         else
         {
            db_query(_ROLLBACK);
            $_SESSION['msg'] = 'AN ERROR OCCURRED WHILE TRYING TO DELETE, PLEASE TRY AGAIN';
            return false;
         }

      }
      else
      {
         $_SESSION['msg'] = 'PLEASE SELECT APPLICABLE ITEMS TO DELETE';
         return false;
      }
   }

   function GenericLoadRecord($query, $fieldArr)
   {
      $res = db_query($query);
      $numRows = db_numrows($res);

      $valueArr = array();
      if($numRows == 1)
      {
//         $_SESSION['msg'] = '<span class="'._SUCCESS_COLOR.'">'._DB_LOAD_SUCCESS.'</span>';

         for($i = 0; $i < count($fieldArr); $i++)
         {
            $fieldName = $fieldArr[$i];
            $valueArr[$fieldName] = db_result($res, 0, $fieldName);
         }
      }
      return $valueArr;
   }

   function GenericSaveUpdate($table, $fieldArr, $valueArr, $type, $idName, $id)
   {
//      if(count($fieldArr) != count($valueArr))
//      {
//         $_SESSION['msg'] = '<font color="'._FAILED_COLOR.'">ERROR - FIELDS AND VALUES DIFFER.</font>';
//         return false;
//      }
//echo "TYPE: $type<BR>";
      $query = "";
      if($type == _SAVE)
      {
         $query = "INSERT INTO `$table` (";
         $valueQ = "";
         //fieldnames first
         for($i = 0; $i < count($fieldArr); $i++)
         {
            $fieldName = $fieldArr[$i];
            $fieldValue = $valueArr[$fieldName];

            if(!get_magic_quotes_gpc())
               $fieldValue = mysqli_real_escape_string(db_connect(), $fieldValue);

            $query .= "`$fieldName`";
            $valueQ .= "\"$fieldValue\"";

            if($i+1 < count($fieldArr))
            {
               $query .= ", ";
               $valueQ .= ", ";
            }
         }
         $query .= ") VALUES (";
         $query .= $valueQ . ")";

      }
      else if($type == _UPDATE)
      {
         $query = "UPDATE `$table` SET ";
         for($i = 0; $i < count($fieldArr); $i++)
         {
            $fieldName = $fieldArr[$i];
            $fieldValue = $valueArr[$fieldName];

            if(!get_magic_quotes_gpc())
               $fieldValue = mysqli_real_escape_string(db_connect(), $fieldValue);

            $query .= "`$fieldName` = \"$fieldValue\"";

            if($i+1 < count($fieldArr))
            {
               $query .= ", ";
            }
         }
         $query .= " where $idName = $id";
      }

    //echo "query: $query<br>";
//    return false;
      $res = db_query($query);
      if($type != _UPDATE)
          $insertid = mysqli_insert_id(db_connect());
       else
         $insertid = true;
      if(!$res)
         return false;
      else
         return $insertid;
   }

   function GenericCheckAndSync($req_fields, $req)
   {
      $required = true;
      $return_fields = array();
      foreach($req_fields as $req_field)
      {
         $$req_field = $_REQUEST[$req_field];

         if($$req_field != '')
            $return_fields[$req_field] = $$req_field;

         //check if all fields req
         if($req)
         {
            if (in_array($req_field, $req_fields) && $$req_field == '')
            {
               //exit('Please, go back and complete all the fields.');
               $required = false;
               //break;
            }
         }
      }
      if(!$required)
      {
         $_SESSION['msg'] = ' MISSING FIELDS - PLEASE COMPLETE ALL REQUIRED FIELDS';
//         return count($return_fields);
      }
//      else
      {
         return $return_fields;
      }
   }


?>
