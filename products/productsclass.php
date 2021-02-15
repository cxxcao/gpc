<?php
$home = dirname(__FILE__) . "/../";
$lib = $home . "/lib/";

require_once($home . '/globals.php');
require_once($lib . '/functions.php');
require_once($lib . 'loginfunctions.php');
require_once($lib . 'loginfunctions.php');
require_once($lib . '/htmlGenerator.php');
require_once($lib . '/phpmailer/class.phpmailer.php');

class category
{
   var $cat_id;
   var $name;
   var $img;
   var $action;

   function __construct()
   {
      $this->cat_id = "";
      $this->name = "";
      $this->img = "";
      $this->action = _SAVE;
   }

   function Update()
   {
      if(!minAccessLevel(_ADMIN_LEVEL))
         exit('You are not authorized to view this page.');
      //check and sync addy info
      $req_addressfields = array(_NAME, _IMGFILE);
      $target_path = "../data/";
      $numfiles = 0;
      $basefilename = basename( $_FILES[_IMGFILE]['name']);

      $return_addressfields = GenericCheckAndSync($req_addressfields, false);

      if($basefilename) //new img
      {
         $curimg = $this->img;
         $imgdir ="../img/products/";
         if($this->ProcessImage(_IMGFILE, $imgdir, 150, 100))
         {
            db_query(_BEGIN);
            $name = $this->name;
            $imgname = $this->img;
            $cat_id = $this->cat_id;

            $query = "UPDATE category SET name = '$name', img = '$imgname' WHERE (cat_id = $cat_id)";
            $res = db_query($query);
            if($res)
            {
               //delete old img
               if(file_exists($imgdir . $curimg))
                  unlink($imgdir . $curimg);
               db_query(_COMMIT);
               $_SESSION['msg'] =  "Save Successful";
               return true;
            }
            else
            {
               db_query(_ROLLBACK);
               $_SESSION['msg'] =  "Save Failed";
               return false;
            }
         }
      }
      else //just update the category name
      {
         $query = "UPDATE category SET name = '$name' WHERE (cat_id = $cat_id)";
         $res = db_query($query);
         if($res)
         {
            db_query(_COMMIT);
            $_SESSION['msg'] =  "Save Successful";
            return true;
         }
         else
         {
            db_query(_ROLLBACK);
            $_SESSION['msg'] =  "Save Failed";
            return false;
         }
      }
   }

   function Save()
   {
      if(!minAccessLevel(_ADMIN_LEVEL))
         exit('You are not authorized to view this page.');
      $action = _checkIsSet(_ACTION);
      //check and sync addy info
      $req_addressfields = array(_NAME, _IMGFILE);

      $target_path = "../data/";
      $numfiles = 0;
      $basefilename = basename( $_FILES[_IMGFILE]['name']);

      $return_addressfields = GenericCheckAndSync($req_addressfields, false);

      $return_addressfields{_IMGFILE} = $basefilename;
      $this->SetValues($return_addressfields);

      $return_size = count($return_addressfields);
      $required_size = count($req_addressfields);

      if($return_size != $required_size)
      {
         $_SESSION['msg'] =  "Please note all fields are required.";
         return false;
      }
      else
      {
         //process img
         $imgdir ="../img/products/";
         if($this->ProcessImage(_IMGFILE, $imgdir, 185, 150))
         {
            db_query(_BEGIN);
            $name = $this->name;
            $imgname = $this->img;
            $query = "INSERT INTO category (name, img ) VALUES  ( '$name', '$imgname' )";
            $res = db_query($query);
            if($res)
            {
               db_query(_COMMIT);
               $_SESSION['msg'] =  "Save Successful";
               return true;
            }
            else
            {
               db_query(_ROLLBACK);
               $_SESSION['msg'] =  "Save Failed";
               return false;
            }
         }
         else
            return false;
      }
   }

   function ProcessImage($imgFileId, $imgdir, $width, $height)
   {
         //check image
         list($error, $photo_message, $extension) = CheckUploadedImage(_IMGFILE);
         if ($error == 1)
         {
            $_SESSION['msg'] =  $photo_message;
            return false;
         }
         //move the image
         $imgname = uniqid() . "." . $extension;

         list($error, $photo_message) = moveImage(_IMGFILE, $imgdir, $imgname);

         if ($error == 0)
         {
            if(resizeImage($imgdir . $imgname, $extension, $width, $height) === true)
            {
               //all is good, set imgname return true;
               $this->img = $imgname;
               return true;
            }
         }
         else
         {
            $_SESSION['msg'] =  $photo_message;
            return false;
         }
   }

   function LoadCategory()
   {
      $cat_id = _checkIsSet('cat_id');
      $query = "select * from category where cat_id = $cat_id";
      $res = db_query($query);
      $num = db_numrows($res);

      if($num > 0)
      {
         $name = db_result($res, 0, _NAME);
         $img = db_result($res, 0, 'img');
         $this->cat_id = $cat_id;
         $this->name = $name;
         $this->img = $img;
         $this->action = _UPDATE;
      }
   }

   function Delete()
   {
      $cat_id = _checkIsSet('cat_id');
      //get the img id;
      $this->LoadCategory();

      $query = "delete from category where cat_id = $cat_id";
      $res = db_query($query);

      if($res)
      {
         $img = $this->img;
         $imgdir ="../img/products/";
         if(file_exists($imgdir . $img))
            unlink($imgdir . $img);
         $_SESSION['msg'] =  "Category Deleted";
         return true;
      }
      else
      {
         $_SESSION['msg'] =  "Delete failed";
         return false;
      }
   }

   function ListCategories()
   {
      if(!minAccessLevel(_ADMIN_LEVEL))
         exit('You are not authorized to view this page.');
      $query = "select * from category";
      $res = db_query($query);
      $num = db_numrows($res);

      echo "
            <table cellspacing='0' cellpadding='0' id='orders'>
               <tr>
                  <td class='img'>CATEGORY NAME</td>
                  <td class='img'>IMAGE</td>
                  <td class='img'><img src='img/edit-page-grey.gif' width='14' height='14' /></td>
                  <td class='img'><img src='img/delete-page-red' width='14' height='14' /></td>
               </tr>";

      if($num > 0)
      {
         for($i = 0; $i < $num; $i++)
         {
            $cat_id = db_result($res, $i, 'cat_id');
            $name = db_result($res, $i, _NAME);
            $img = db_result($res, $i, 'img');
            $imgdir ="../img/products/";

            $link = "add-category.php?action=EDIT&cat_id=$cat_id";
            $editlink = '<a href="'.$link.'">Edit</a>';
            $del = "list-category.php?action=DELETE&cat_id=$cat_id";
            $dellink = '<a href="'.$del.'">Delete</a>';
            echo "
               <tr>
                  <td class='catDesc'>$name</td>
                  <td class='status'><img src='$imgdir/$img'></td>
                  <td class=\"status\">$editlink</td>
                  <td class='status'>$dellink</td>
               </tr>";


         }
      }

      echo "</table>";
   }

   function SetValues($fields)
   {
      $this->name = $fields{_NAME};
      $this->img = $fields{_IMGFILE};
   }

}

class subcategory
{
   var $subcat_id;
   var $cat_id;
   var $name;
   var $action;

   function __construct()
   {
      $this->subcat_id = "";
      $this->cat_id = "";
      $this->name = "";
      $this->action = _SAVE;
   }

   function LoadSubCategory()
   {
      if(!minAccessLevel(_ADMIN_LEVEL))
         exit('You are not authorized to view this page.');
      $subcat_id = _checkIsSet('subcat_id');
      $query = "select * from subcategory where subcat_id = $subcat_id";
      $res = db_query($query);
      $num = db_numrows($res);

      if($num > 0)
      {
         $name = db_result($res, 0, _NAME);
         $cat_id = db_result($res, 0, 'cat_id');

         $this->cat_id = $cat_id;
         $this->name = $name;
         $this->subcat_id = $subcat_id;
         $this->action = _UPDATE;
      }
   }

   function Delete()
   {
      $subcat_id = _checkIsSet('subcat_id');
      //get the img id;
      $this->LoadSubCategory();

      $query = "delete from subcategory where subcat_id = $subcat_id";
      $res = db_query($query);

      if($res)
      {
         $_SESSION['msg'] =  "Category Deleted";
         return true;
      }
      else
      {
         $_SESSION['msg'] =  "Delete failed";
         return false;
      }
   }

   function ListSubCategories()
   {
      if(!minAccessLevel(_ADMIN_LEVEL))
         exit('You are not authorized to view this page.');
      $query = "select * from subcategory order by cat_id";
      $res = db_query($query);
      $num = db_numrows($res);

      echo "
            <table cellspacing='0' cellpadding='0' id='orders'>
               <tr>
                  <td class='img'>CATEGORY</td>
                  <td class='img'>SUBCATEGORY</td>
                  <td class='img'><img src='img/edit-page-grey.gif' width='14' height='14' /></td>
                  <td class='img'><img src='img/delete-page-red' width='14' height='14' /></td>
               </tr>";

      if($num > 0)
      {
         for($i = 0; $i < $num; $i++)
         {
            $cat_id = db_result($res, $i, 'cat_id');
            $subcat_id = db_result($res, $i, 'subcat_id');
            $name = db_result($res, $i, _NAME);
            $cat_name = $this->GetCatName($cat_id);

            $link = "add-subcategory.php?action=EDIT&subcat_id=$subcat_id";
            $editlink = '<a href="'.$link.'">Edit</a>';
            $del = "list-subcategory.php?action=DELETE&subcat_id=$subcat_id";
            $dellink = '<a href="'.$del.'">Delete</a>';
            echo "
               <tr>
                  <td class='catDesc'>$cat_name</td>
                  <td class='catDesc'>$name</td>
                  <td class=\"status\">$editlink</td>
                  <td class='status'>$dellink</td>
               </tr>";


         }
      }

      echo "</table>";
   }

   function GetCatName($cat_id)
   {
      $query = "select * from category where cat_id = $cat_id";
      $res = db_query($query);
      $num = db_numrows($res);
      if($num > 0)
         return db_result($res, 0, _NAME);
      else
         return "Category Not Found";
   }

   function Save()
   {
      if(!minAccessLevel(_ADMIN_LEVEL))
         exit('You are not authorized to view this page.');
      //check and sync addy info
      $req_addressfields = array(_NAME, "cat_name");

      $return_addressfields = GenericCheckAndSync($req_addressfields, true);

      $this->SetValues($return_addressfields);

      $return_size = count($return_addressfields);
      $required_size = count($req_addressfields);

      if($return_size != $required_size)
      {
         $_SESSION['msg'] =  "Please note all fields are required.";
         return false;
      }
      else
      {
         db_query(_BEGIN);
         $name = $this->name;
         $cat_id = $this->cat_id;
         $subcat_id = $this->subcat_id;
         if($this->action == _SAVE)
            $query = "INSERT INTO subcategory (cat_id, name ) VALUES  ( $cat_id, '$name' )";
         else
            $query = "UPDATE subcategory SET cat_id = $cat_id, name = '$name' WHERE (subcat_id = $subcat_id)";

         $res = db_query($query);
         if($res)
         {
            db_query(_COMMIT);
            $_SESSION['msg'] =  "Save Successful";
            return true;
         }
         else
         {
            db_query(_ROLLBACK);
            $_SESSION['msg'] =  "Save Failed";
            return false;
         }
      }
   }

   function SetValues($fields)
   {
      $this->cat_id = $fields{"cat_name"};
      $this->name = $fields{_NAME};
      if($this->action != _SAVE)
      {

      }
   }



}

class product
{
   var $prod_id;
   var $category; //mens or womens
   var $cat_id;
   var $item_number;
   var $myob_code;
   var $description;
   var $fabric;
   var $colour;
   var $measure;
   var $qty;
   var $price;
   var $action;

   function __construct()
   {
      $this->prod_id = "";
      $this->category = "";
      $this->cat_id = "";
      $this->item_number = "";
      $this->myob_code = "";
      $this->description = "";
      $this->fabric = "";
      $this->colour = "";
      $this->measure = "";
      $this->qty= "";
      $this->price = "";
      $this->action = _SAVE;
   }


   function Delete()
   {
      $prod_id = _checkIsSet('prod_id');
      //get the img id;
      $this->LoadProduct();

      $query = "delete from products where prod_id = $prod_id";
      $res = db_query($query);

      if($res)
      {
         $_SESSION['msg'] =  "Product Deleted";
         return true;
      }
      else
      {
         $_SESSION['msg'] =  "Delete failed";
         return false;
      }
   }

   function LoadProductId($prod_id, $isAUS)
   {
      $query = "select * from products where prod_id = $prod_id";
      $res = db_query($query);
      $num = db_numrows($res);
      if($num > 0)
      {
         $this->prod_id = $prod_id;
         $this->category = db_result($res, 0, _CATEGORY);
         $this->cat_id = db_result($res, 0, _CAT_ID);
         $this->item_number = db_result($res, 0, _ITEM_NUMBER);
         $this->myob_code = db_result($res, 0, _MYOB_CODE);
         $this->description = db_result($res, 0, _DESCRIPTION);
         $this->myob_code = db_result($res, 0, _MYOB_CODE);
         $this->colour = db_result($res, 0, _COLOUR);
         $this->measure = db_result($res, 0, _MEASURE);
         $this->qty = db_result($res, 0, _QTY);
         if($isAUS == "N")
         {
            $this->price = db_result($res, 0, "price_nz");
         }
         else //default to AUS pricing
         {
            $this->price = db_result($res, 0, _PRICE);
            if(_SHOW_PRICE_WITH_GST == "Y")
               $this->price = $this->price *1.1;//gst
         }

         return true;
      }
      return false;
   }

   function LoadProduct()
   {
      $prod_id = _checkIsSet('prod_id');
      return $this->LoadProductId($prod_id, "");
   }

   function SetValues($fields)
   {
      $this->prod_id =  $fields{_PROD_ID};
      $this->category = $fields{_CATEGORY};
      $this->cat_id = $fields{_CAT_ID};
      $this->item_number = $fields{_ITEM_NUMBER};
      $this->myob_code = $fields{_MYOB_CODE};
      $this->description = $fields{_DESCRIPTION};
      $this->fabric = $fields{_FABRIC};
      $this->colour = $fields{_COLOUR};
      $this->measure = $fields{_MEASURE};
      $this->qty= $fields{_QTY};
      $this->price = $fields{_PRICE};
   }

   function ListProducts()
   {
      if(!minAccessLevel(_ADMIN_LEVEL))
         exit('You are not authorized to view this page.');

      $query = "select * from products";
      $res = db_query($query);
      $num = db_numrows($res);

      echo "
            <table cellspacing='0' cellpadding='0' id='orders'>
               <tr>
                  <td class='img'>Product Name</td>
                  <td class='img'>Description</td>
                  <td class='img'>Minimum Qty</td>
                  <td class='img'>Minimum Price</td>
                  <td class='img'>Carton Qty</td>
                  <td class='img'>Carton Price</td>
                  <td class='img'>Show on Home Page</td>
                  <td class='img'>Available</td>
                  <td class='img'><img src='img/edit-page-grey.gif' width='14' height='14' /></td>
                  <td class='img'><img src='img/delete-page-red' width='14' height='14' /></td>
               </tr>";

      if($num > 0)
      {
         for($i = 0; $i < $num; $i++)
         {
            $prod_id = db_result($res, $i, 'prod_id');
            $subcat_id = db_result($res, $i, _SUBCAT_ID);
            $name = db_result($res, $i, _NAME);
            $description = db_result($res, $i, _DESCRIPTION);
            $retail_price = db_result($res, $i, _RETAIL_PRICE);
            $wholesale_price = db_result($res, $i, _WHOLESALE_PRICE);
            $minQty = db_result($res, $i, "min");
            $maxQty = db_result($res, $i, "max");
            $available = db_result($res, $i, "available");
            $display = db_result($res, $i, "display");

            $displayName = "YES";
            if(!$display)
               $displayName = "NO";

            $availableName = "YES";
            if(!$available)
               $availableName = "OUT OF STOCK";


            $img = db_result($res, $i, 'img');
            $imgdir ="../img/products/";

            $link = "add-product.php?action=EDIT&prod_id=$prod_id";
            $editlink = '<a href="'.$link.'">Edit</a>';
            $del = "list-product.php?action=DELETE&prod_id=$prod_id";
            $dellink = '<a href="'.$del.'" onclick="return confirm(\'Are you sure you want to delete this item\');">Delete</a>';

            $display = "list-product.php?action=DISPLAY&prod_id=$prod_id";
            $displaylink = "<a href='$display'>$displayName</a>";

            $disp = "list-product.php?action=DISPLAY&prod_id=$prod_id";
            $displaylink = "<a href='$disp'>$displayName</a>";

            $avail = "list-product.php?action=OUTOFSTOCK&prod_id=$prod_id";
            $availlink = "<a href='$avail'>$availableName</a>";

            echo "
               <tr>
                  <td class='catDesc'>$name</td>
                  <td class='status'>$description</td>
                  <td class='status'>$minQty</td>
                  <td class='status'>$retail_price</td>
                  <td class='status'>$maxQty</td>
                  <td class='status'>$wholesale_price</td>
                  <td class=\"status\">$displaylink</td>
                  <td class=\"status\">$availlink</td>
                  <td class=\"status\">$editlink</td>
                  <td class='status'>$dellink</td>
               </tr>";


         }
      }

      echo "</table>";
   }

   function Save()
   {

   }

   function ProcessImage($imgFileId, $imgdir, $imgno, $width, $height)
   {
         //check image
         list($error, $photo_message, $extension) = CheckUploadedImage($imgFileId);
         if ($error == 1)
         {
            $_SESSION['msg'] =  $photo_message;
            return false;
         }
         //move the image
         $uuid = uniqid();
         $imgname = $uuid . "." . $extension;
         $thumbname = $uuid;

         list($error, $photo_message) = moveImage($imgFileId, $imgdir, $imgname);
         resizeImage( $imgdir .$imgname, $extension, 380, 380);

         if ($error == 0)
         {
            if(createThumb($imgdir . $thumbname, $extension, $width, $height)  === false)
            {
               $_SESSION['msg'] =  "Error creating thumbnail";
               return false;
            }
            else
            {
               if($imgno == 1)
                  $this->image1 = $imgname;
               else if($imgno == 2)
                  $this->image2 = $imgname;
               else if($imgno == 3)
                  $this->image3 = $imgname;
               else if($imgno == 4)
                  $this->image4 = $imgname;


               return true;
            }


         }
         else
         {
            $_SESSION['msg'] =  $photo_message;
            return false;
         }
   }

}


?>
