<?php

function generateStaticCombo($itemsArr, $default, $selectName, $required)
{
   $keys = array_keys($itemsArr);
   $size = count($keys);

   if($required)
      echo '<select id="'.$selectName.'" name="'.$selectName.'" class="validate[required]"><option value="">-select-</option>';
   else
      echo '<select id="'.$selectName.'" name="'.$selectName.'"><option value="">-select-</option>';
      for($i = 0; $i < $size; $i++)
      {
         $key = $keys[$i];
         $val = $itemsArr[$key];
         if(strtoupper($default) == strtoupper($key))
            echo '<option value="'.$key.'" selected>'.$val.'</option>';
         else
            echo '<option value="'.$key.'">'.$val.'</option>';
      }
   echo '</select>';
}

function generateComboDynamic($itemsArr, $default, $selectName, $reqOpt)
{
   $keys = array_keys($itemsArr);
   $size = count($keys);

   echo '<select id="'.$selectName.'" name="'.$selectName.'"  class="validate['.$reqOpt.']"><option value="">-select-</option>';
      for($i = 0; $i < $size; $i++)
      {
         $key = $keys[$i];
         $val = $itemsArr[$key];
         if($default == $key)
            echo '<option value="'.$key.'" selected>'.$val.'</option>';
         else
            echo '<option value="'.$key.'">'.$val.'</option>';
      }
   echo '</select>';
}

   function generateComboQuerySizes($name, $query, $default, $pid)
   {
      $result = db_query($query);
      $divname = $name . "_div";
      //$onChange = "showElement('".$divname."', '".$name."');calcOnHand($pid, $name);";
      print("<select name=\"$name\" id=\"$name\" class=\"prodsizeclass\">\n");
      print("<option value=\"\" selected>-size-\n");
      while($row = mysqli_fetch_array($result))
      {
         $row_val = $row[0];
         $id = $row[1];
//         $id = strtoupper($id);
         $row_val = strtoupper($row_val);
         $default = strtoupper($default);

         if($default != "" && $default == strtoupper($id))
         {
            print("<option value=\"$id\" selected>$row_val\n");
         }
         else
         {
            print("<option value=\"$id\">$row_val\n");
         }
      }
/*
      if($default == strtoupper(_SPECIAL))
         print("<option value=\"special\" selected>Special Make</option>");
      else
         print("<option value=\"special\">Special Make</option>");
*/
      print("</select>");
   }

   function generateComboQuery($name, $query, $default, $reqOpt)
   {
      $result = db_query($query);
      //$onchange = 'this.form.'.$textInputName.'.value=this.form.'.$name.'[this.selectedIndex].value;this.form.submit()';
      //print("<select id=\"$name\"  name=\"$name\">\n");

      if(!$reqOpt)
         $reqOpt = "optional";
      print('<select id="'.$name.'" name="'.$name.'"  class="validate['.$reqOpt.'] ProductClass">');
      print("<option value=\"\"></option>\n");
      while($row = mysqli_fetch_array($result))
      {
         $row_val = $row[0];
         $id = strtoupper(trim($row[1]));
         $row_val = strtoupper(trim($row_val));
         $default = strtoupper(trim($default));
         if($default != "" && ($default == $id || $default == $row_val))
         {

            print("<option value=\"$id\" selected>$row_val</option>");
         }
         else
         {
            print("<option value=\"$id\">$row_val</option>");
         }
      }
      print("</select>");
   }

   function generateComboQueryNA($name, $query, $default, $reqOpt)
   {
      $result = db_query($query);
      //$onchange = 'this.form.'.$textInputName.'.value=this.form.'.$name.'[this.selectedIndex].value;this.form.submit()';

      //print("<select id=\"$name\"  name=\"$name\">\n");
      //print("<option value=\"\"></option>\n");
      if(!$reqOpt)
         $reqOpt = "optional";
      print('<select id="'.$name.'" name="'.$name.'"  class="validate['.$reqOpt.'] naProductClass">');
      print("<option value=\"na\">No Replacement Required</option>\n");
      while($row = mysqli_fetch_array($result))
      {
         $row_val = $row[0];
         $id = strtoupper($row[1]);
         $row_val = strtoupper($row_val);
         $default = strtoupper($default);
         if($default != "" && $default == $id)
         {
            print("<option value=\"$id\" selected>$row_val</option>");
         }
         else
         {
            print("<option value=\"$id\">$row_val</option>");
         }
      }
      print("</select>");
   }

function generateComboShipping($name, $query, $default)
{
   $result = db_query($query);
   $onchange = 'this.form.submit()';

   print("<select name=\"$name\" onChange=\"$onchange\">\n");
   print("<option value=\"\"></option>\n");
   while($row = mysql_fetch_array($result))
   {
      $row_id = $row[0];
      $row_val = $row[3] . " - $" . $row[5];
      $row_val = ucwords(strtolower($row_val));
      $default = ucwords(strtolower($default));
      if($default != "" && $default == $row_id)
      {
         print("<option value=\"$row_id\" selected>$row_val</option>\n");
      }
      else
      {
         print("<option value=\"$row_id\">$row_val</option>\n");
      }
   }
   print("</select>");
}

function generateStaticComboID($itemsArr, $default, $selectName, $selectID, $required)
{
   $keys = array_keys($itemsArr);
   $size = count($keys);

//    if(substr($selectName, strlen($selectName)-2,2) == "[]")
//    	$selectName = substr($selectName, 0, strlen($selectName)-2);
   
   if($required)
      echo '<select id="'.$selectID.'" name="'.$selectName.'" class="validate[required] allocClass allocSel"><option value="">-select-</option>';
   else
      echo '<select id="'.$selectID.'" name="'.$selectName.'"><option value="">-select-</option>';
      for($i = 0; $i < $size; $i++)
      {
         $key = $keys[$i];
         $val = $itemsArr[$key];
         if(strtoupper($default) == strtoupper($key))
            echo '<option value="'.$key.'" selected>'.$val.'</option>';
         else
            echo '<option value="'.$key.'">'.$val.'</option>';
      }
   echo '</select>';
}

function generateStaticComboSimple($itemsArr, $default, $selectName, $required)
{
   $keys = array_keys($itemsArr);
   $size = count($keys);

//    if(substr($selectName, strlen($selectName)-2,2) == "[]")
//    	$selectName = substr($selectName, 0, strlen($selectName)-2);
   
   if($required)
      echo '<select id="'.$selectName.'" name="'.$selectName.'" class="validate[required] allocClass"><option value="">-select-</option>';
   else
      echo '<select id="'.$selectName.'" name="'.$selectName.'"><option value="">-select-</option>';
      for($i = 0; $i < $size; $i++)
      {
         $key = $keys[$i];
         $val = $itemsArr[$key];
         if(strtoupper($default) == strtoupper($key))
            echo '<option value="'.$key.'" selected>'.$val.'</option>';
         else
            echo '<option value="'.$key.'">'.$val.'</option>';
      }
   echo '</select>';
}

function numbersCombo($start, $end, $order, $default, $name)
{
   echo '<select name="'.$name.'"><option value=""></option>';
   if($order)
   {
      for($i = $start; $i <= $end; $i++)
      {
         if($i < 10)
            $num = "0".$i;
         else
            $num = $i;
         if($i == $default)
            echo "<option selected>$num</option>";
         else
            echo "<option>$num</option>";
      }
   }
   else
   {
      for($i = $end; $i >= $start; $i--)
      {
         if($i < 10)
            $num = "0".$i;
         else
            $num = $i;
         if($i == $default)
            echo "<option selected>$num</option>";
         else
            echo "<option>$num</option>";
      }
   }
   echo '</select>';
}


function htmlCountries($default, $name)
{
   $countries = array("Afghanistan","Albania","Algeria","American Samoa","Andorra","Angola","Anguilla","Antarctica","Antigua and Barbuda","Argentina","Armenia","Aruba","Australia","Austria","Azerbaidjan","Bahamas",
"Bahrain", "Bangladesh","Barbados","Belarus","Belgium","Belize","Benin","Bermuda","Bhutan","Bolivia","Bosnia-Herzegovina","Botswana","Bouvet Island","Brazil","Brunei Darussalam","Bulgaria",
"Burkina Faso", "Burundi","Cambodia","Cameroon","Canada","Cape Verde","Cayman Islands","Central African Republic","Chad","Chile","China","Christmas Island","Cocos Islands","Colombia","Comoros","Congo",
"Cook Islands","Costa Rica","C�te d'Ivoire","Croatia","Cuba","Cyprus","Czech Republic","Democratic Republic of the Congo","Denmark","Djibouti","Dominican Republic","East Timor","Ecuador","Egypt",
"El Salvador","Equatorial Guinea","Estonia","Ethiopia","Falkland Islands","Faroe Islands","Fiji","Finland","France","French Polynesia","French Southern Terr.","Gabon","Gambia","Georgia","Germany","Ghana",
"Gibraltar","Greece","Greenland","Grenada","Guadeloupe","Guam","Guatemala","Guinea","Guinea Bissau","Guyana","Guyana","Haiti","Honduras","Hong Kong","Hungary","Iceland","India","Indonesia","Iran","Iraq",
"Ireland","Israel","Italy","Jamaica","Japan","Jordan","Kazakhstan","Kenya","Kiribati","Korea North","Korea South","Kuwait","Kyrgyzstan","Laos","Latvia","Lebanon","Lesotho","Liberia","Libya","Liechtenstein",
"Lithuania","Luxembourg","Macau","Macedonia","Madagascar","Malawi","Malaysia","Maldives","Mali","Malta","Marshall Islands","Martinique","Mauritania","Mauritius","Mexico","Micronesia","Moldova","Monaco",
"Mongolia","Montserrat","Morocco","Mozambique","Myanmar","Namibia","Nauru","Nepal","Netherland Antilles","Netherlands","New Caledonia","New Zealand","Nicaragua","Niger","Nigeria","Niue","Norfolk Island","Northern Mariana Islands",
"Norway","Oman","Pakistan","Palau","Panama","Papua New Guinea","Paraguay","Peru","Philippines","Pitcairn","Poland","Portugal","Puerto Rico","Qatar","Reunion","Romania","Russia","Rwanda","Saint Lucia","Samoa",
"San Marino","Sao Tome and Principe","Saudi Arabia","Senegal","Serbia","Seychelles","Sierra Leone","Singapore","Slovakia","Slovenia","Solomon Islands","Somalia","South Africa","Spain","Sri Lanka","St. Helena",
"St. Pierre & Miquelon","St.Vincent & Grenadines","Sudan","Suriname","Svalbard & Jan Mayen Island","Swaziland","Sweden","Switzerland","Syria","Taiwan","Tajikistan","Tanzania","Thailand","Togo",
"Tokelau","Tonga","Trinidad & Tobago","Tunisia","Turkey","Turkmenistan","Turks & Caicos Islands",
"Tuvalu","Uganda","Ukraine","United Arab Emirates","United Kingdom","United States","Uruguay","Uzbekistan","Vanuatu","Vatican City State","Venezuela","Vietnam","Wallis and Futuna Islands","Western Sahara","Yemen","Zambia","Zimbabwe");

   $countriesCommon = array("Australia", "New Zealand", "United Kingdom", "United States", "France", "Germany", "Spain", "Italy","Canada", "China", "Hong Kong", "Viet Nam");
   $found = false;
   echo'
   <select name="'.$name.'">
      <optgroup label="">
         <option value="" selected="selected">Select Country</option>
      </optgroup>
      <optgroup label="common choices">';
      foreach ($countriesCommon as $c)
      {
         if($c == $default)
         {
            echo "<option selected>$c</option>";
            $found = true;
         }
         else
            echo "<option>$c</option>";
      }
   echo'
      </optgroup>
      <optgroup label="other countries">';

      foreach($countries as $c)
      {
         if(!$found && $c == $default)
         {
            echo "<option selected>$c</option>";
            $found = true;
         }
         else
            echo "<option>$c</option>";
      }
   echo'
      </optgroup>
   </select>';
}


?>
