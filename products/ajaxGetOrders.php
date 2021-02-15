<?php
session_start();
   error_reporting(E_ALL);
   ini_set("display_errors", 1);
$home = dirname(__FILE__) . "/../";
$lib = $home ."/lib/";

require_once($home . '/globals.php');
require_once($lib . 'functions.php');
require_once($lib . 'loginfunctions.php');
require_once($lib . 'htmlGenerator.php');
   /* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
    * Easy set variables
    */

   /* Array of database columns which should be read and sent back to DataTables. Use a space where
    * you want to insert a non-database field (for example a counter or static image)
    */
   $aColumns = array('order_id','user_id','emp_id','name','address','suburb','state','postcode','country','contact','order_time','status','courier','connote','lastupdated','comments','email','sname','approvaltime','status_no','numboxes','despatchdate','despatchstatus','sample','cardname','cardnumber','expiry','cardtype','payable','paid','paymentopt','paymentopt2','numpays','amountperpay','agree','receipt','signer','delivereddate');

   /* Indexed column (used for fast and accurate table cardinality) */
   $sIndexColumn = "order_id";

   /* DB table to use */
   $sTable = "ajax";

   /* Database connection information */
   $gaSql['user']       = "root";
   $gaSql['password']   = "12345the";
   $gaSql['db']         = "reece2";
   $gaSql['server']     = "localhost";

   /* REMOVE THIS LINE (it just includes my SQL connection user/pass) */


   /* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
    * If you just want to use the basic configuration for DataTables with PHP server-side, there is
    * no need to edit below this line
    */

   /*
    * Local functions
    */
   function fatal_error ( $sErrorMessage = '' )
   {
      header( $_SERVER['SERVER_PROTOCOL'] .' 500 Internal Server Error' );
      die( $sErrorMessage );
   }


   /*
    * MySQL connection
    */
   if ( ! $gaSql['link'] = mysql_pconnect( $gaSql['server'], $gaSql['user'], $gaSql['password']  ) )
   {
      fatal_error( 'Could not open connection to server' );
   }

   if ( ! mysql_select_db( $gaSql['db'], $gaSql['link'] ) )
   {
      fatal_error( 'Could not select database ' );
   }


   /*
    * Paging
    */
   $sLimit = "";
   if ( isset( $_GET['iDisplayStart'] ) && $_GET['iDisplayLength'] != '-1' )
   {
      $sLimit = "LIMIT ".intval( $_GET['iDisplayStart'] ).", ".
         intval( $_GET['iDisplayLength'] );
   }


   /*
    * Ordering
    */
   $sOrder = "";
   if ( isset( $_GET['iSortCol_0'] ) )
   {
      $sOrder = "ORDER BY  ";
      for ( $i=0 ; $i<intval( $_GET['iSortingCols'] ) ; $i++ )
      {
         if ( $_GET[ 'bSortable_'.intval($_GET['iSortCol_'.$i]) ] == "true" )
         {
            $sOrder .= "`".$aColumns[ intval( $_GET['iSortCol_'.$i] ) ]."` ".
               ($_GET['sSortDir_'.$i]==='asc' ? 'asc' : 'desc') .", ";
         }
      }

      $sOrder = substr_replace( $sOrder, "", -2 );
      if ( $sOrder == "ORDER BY" )
      {
         $sOrder = "";
      }
   }


   /*
    * Filtering
    * NOTE this does not match the built-in DataTables filtering which does it
    * word by word on any field. It's possible to do here, but concerned about efficiency
    * on very large tables, and MySQL's regex functionality is very limited
    */
   $sWhere = "";
   if ( isset($_GET['sSearch']) && $_GET['sSearch'] != "" )
   {
      $sWhere = "WHERE (";
      for ( $i=0 ; $i<count($aColumns) ; $i++ )
      {
         $sWhere .= "`".$aColumns[$i]."` LIKE '%".mysql_real_escape_string( $_GET['sSearch'] )."%' OR ";
      }
      $sWhere = substr_replace( $sWhere, "", -3 );
      $sWhere .= ')';
   }

   /* Individual column filtering */
   for ( $i=0 ; $i<count($aColumns) ; $i++ )
   {
      if ( isset($_GET['bSearchable_'.$i]) && $_GET['bSearchable_'.$i] == "true" && $_GET['sSearch_'.$i] != '' )
      {
         if ( $sWhere == "" )
         {
            $sWhere = "WHERE ";
         }
         else
         {
            $sWhere .= " AND ";
         }
         $sWhere .= "`".$aColumns[$i]."` LIKE '%".mysql_real_escape_string($_GET['sSearch_'.$i])."%' ";
      }
   }


   /*
    * SQL queries
    * Get data to display
    */
                            $todate = _checkIsSet("dateto");
                        $fromdate = _checkIsSet("datefrom");

                        //if specified date set, load from session
                        if(!$todate)
                        {
                           $todate = date('Y-m-t');
                        }

                        if(!$fromdate)
                        {
                              $fromyear = date('Y');
                              $frommonth = date('m');
                              if($frommonth == 1) //jan set to previous year and month
                              {
                                 $fromyear = $fromyear - 1;
                                 $frommonth = 12;
                              }
                              else
                                 $frommonth = date('m') - 1;

                              if(strlen($frommonth) < 2)
                                 $frommonth = "0$frommonth";

                              $fromdate = "$fromyear-$frommonth-01";
                        }

                                $user_id_val = _checkIsSet("user_id_val");
                            $fullname = _checkIsSet("fullname");

                              $branchLocationId = $_SESSION[_LOCATION_ID];
                              if(minAccessLevel(_ADMIN_LEVEL))
                                 $query = "select * from orders o where o.order_id != '' and  date(order_time) between '$fromdate' and '$todate'";
                              else if(minAccessLevel(_BRANCH_LEVEL))
                                 $query = "select * from orders o, login l where o.order_id != '' and o.sname like '$branchLocationId%'  and l.location_id =  $branchLocationId and  date(order_time) between '$fromdate' and '$todate' group by order_id";
                              else
                                 $query = "select * from orders o where o.user_id = " . $_SESSION[_USER_ID] . " and date(order_time) between '$fromdate' and '$todate'";

                              if($user_id_val)
                                 $query .= " and o.user_id = $user_id_val";

   $sQuery = "
      SELECT SQL_CALC_FOUND_ROWS `".str_replace(" , ", " ", implode("`, `", $aColumns))."`
      FROM   $sTable
      $sWhere
      $sOrder
      $sLimit
      ";
   $rResult = mysql_query( $sQuery, $gaSql['link'] ) or fatal_error( 'MySQL Error: ' . mysql_errno() );

   /* Data set length after filtering */
   $sQuery = "
      SELECT FOUND_ROWS()
   ";
   $rResultFilterTotal = mysql_query( $sQuery, $gaSql['link'] ) or fatal_error( 'MySQL Error: ' . mysql_errno() );
   $aResultFilterTotal = mysql_fetch_array($rResultFilterTotal);
   $iFilteredTotal = $aResultFilterTotal[0];

   /* Total data set length */
   $sQuery = "
      SELECT COUNT(`".$sIndexColumn."`)
      FROM   $sTable
   ";
   $rResultTotal = mysql_query( $sQuery, $gaSql['link'] ) or fatal_error( 'MySQL Error: ' . mysql_errno() );
   $aResultTotal = mysql_fetch_array($rResultTotal);
   $iTotal = $aResultTotal[0];


   /*
    * Output
    */
   $output = array(
      "sEcho" => intval($_GET['sEcho']),
      "iTotalRecords" => $iTotal,
      "iTotalDisplayRecords" => $iFilteredTotal,
      "aaData" => array()
   );

   while ( $aRow = mysql_fetch_array( $rResult ) )
   {
      $row = array();
      for ( $i=0 ; $i<count($aColumns) ; $i++ )
      {
         if ( $aColumns[$i] == "version" )
         {
            /* Special output formatting for 'version' column */
            $row[] = ($aRow[ $aColumns[$i] ]=="0") ? '-' : $aRow[ $aColumns[$i] ];
         }
         else if ( $aColumns[$i] != ' ' )
         {
            /* General output */
            $row[] = $aRow[ $aColumns[$i] ];
         }
      }
      $output['aaData'][] = $row;
   }

   echo json_encode( $output );
?>
