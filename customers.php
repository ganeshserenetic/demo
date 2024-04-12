<?php

include_once dirname(__FILE__) . '/layout/start.inc.php';
 has_access(4);
$page_title = "Customers";
ini_set('memory_limit', '-1');
ini_set('max_execution_time', 30000);
$breadcrumbes[0]['title'] = '<i class="icon-home"></i>';
$breadcrumbes[0]['link'] = 'dashboard.php';

$type_title = 'Customers';

if(isset($_GET['viewall']) && $_GET['viewall'] == 'viewall'){
  redirect('customers.php?view=viewall');
}

$breadcrumbes[1]['title'] = $type_title;

$is_redirect_to_event_order="N";
if(isset($_GET['newregister'])){
  $is_redirect_to_event_order="Y";
  $_SESSION['cust_redirectbacktoeventorder']='Y';
}

$sch_params = array();
$SortBy = "c.created_at";
$SortDirection = "DESC";
$currSortDirection = "ASC";
$incr = '';

$has_querystring = false;
if (cx_isset($_GET["sort"]) != "") {
  $has_querystring = true;
  $SortBy = $_GET["sort"];
}

if (cx_isset($_GET["direction"]) != "") {
  $has_querystring = true;
  $currSortDirection = $_GET["direction"];
  if ($_GET["direction"] == "ASC") {
    $SortDirection = "DESC";
    $currSortDirectionStatus = "DESC";
    $SortDirectionStatus = 'DESC';
  } else {
    $SortDirection = "ASC";
    $currSortDirectionStatus = "ASC";
    $SortDirectionStatus = 'ASC';
  }
}

$is_ajaxed = cx_isset($_GET['is_ajaxed']);
$rep_id = cx_isset($_GET['rep_id']);
$fname = cx_isset($_GET["fname"]);
$lname = cx_isset($_GET["lname"]);
$email = cx_isset($_GET["email"]);
$phone = cx_isset($_GET["phone"]);
$premium = cx_isset($_GET["premium"]);
$shipping_phone = cx_isset($_GET["shipping_phone"]);
$billing_phone = cx_isset($_GET["billing_phone"]);
$s_member_status = cx_isset($_GET['s_member_status']);
$sponsor_name = cx_isset($_GET["sponsor_name"]);
$sponsor_id = cx_isset($_GET["sponsor_id"]);
$network_sponsor_id = cx_isset($_GET['network_sponsor_id']);
$user_name = cx_isset($_GET['user_name']);
$fromdate = cx_isset($_GET["fromdate"]);
$todate = cx_isset($_GET["todate"]);
$verify_status= cx_isset($_GET['verify_status']);
$custom_date = cx_isset($_GET['custom_date']);
$enroll_type = cx_isset($_GET['enroll_type']);
$address = cx_isset($_GET['address']);
$zip = cx_isset($_GET['zip']);
$city = cx_isset($_GET['city']);
$country = cx_isset($_GET["country"]);
$state = cx_isset($_GET['state']);
$tax_exempted = cx_isset($_GET['tax_exempted']);
$old_uforia_customer_id = cx_isset($_GET['old_uforia_customer_id']);

switch ($custom_date) {
  case "Today":
    $fromdate = date('m/d/Y');
    $todate = date('m/d/Y');
    break;
  case "Yesterday":
    $fromdate = date("m/d/Y", strtotime("-1 days"));
    $todate = date('m/d/Y', strtotime("-1 days"));
    break;
  case "Last7Days":
    $fromdate = date('m/d/Y', strtotime("-7 day"));
    $todate = date('m/d/Y', strtotime("-1 day"));
    break;
  case "ThisMonth":
    $fromdate = date('m/01/Y');
    $todate = date('m/d/Y');
    break;
  case "LastMonth":
    $fromdate = date('m/d/Y', strtotime(date('Y-m') . " -1 month"));
    $todate = date('m/d/Y', strtotime(date('Y-m') . " last day of -1 month"));
    break;
  case "ThisYear":
    $fromdate = date('01/01/Y');
    $todate = date('m/d/Y');
    break;
}
if ($premium != "") {
  $sch_params[':premium'] = makeSafe($premium);
  $incr .= " AND c.is_premium = :premium";
}
if ($rep_id != "") {
  $sch_params[':rep_id'] = makeSafe($rep_id);
  $incr.=" AND c.rep_id = :rep_id";
}
if ($old_uforia_customer_id != "") {
  $sch_params[':old_customer_id'] = makeSafe($old_uforia_customer_id);
  $incr.=" AND c.old_customer_id = :old_customer_id";
}
if ($fname != "") {
  $sch_params[':fname'] = "%" . makeSafe($fname) . "%";
  $incr.=" AND c.fname LIKE :fname";
}
if ($lname != "") {
  $sch_params[':lname'] = "%" . makeSafe($lname) . "%";
  $incr.=" AND c.lname LIKE :lname";
}
if ($email != "") {
  $sch_params[':email'] = "%" . makeSafe($email) . "%";
  $incr.=" AND c.email LIKE :email";
}
if ($phone != "") {
  $sch_params[':phone'] = "%" . makeSafe($phone) . "%";
  $incr.=" AND c.cell_phone LIKE :phone";
}
if ($shipping_phone != "") {
  $sch_params[':shipping_phone'] = "%" . makeSafe($shipping_phone) . "%";
  $incr .= " AND cp.phone LIKE :shipping_phone";
}
$billing_incr = '';
if ($billing_phone != "") {
  if($enroll_type == 'hemplevate'){
    $billing_incr = " LEFT JOIN customer_billing_profile cb on(cb.customer_id = c.id AND cb.payment_api_type = 'NMIhemp')";
  } else if($enroll_type != 'hemplevate'){
    $billing_incr = " LEFT JOIN customer_billing_profile cb on(cb.customer_id = c.id AND cb.payment_api_type != 'NMIhemp')";
  } else {
    $billing_incr = " LEFT JOIN customer_billing_profile cb on(cb.customer_id = c.id)";
  }
  $sch_params[':billing_phone'] = "%" . makeSafe($billing_phone) . "%";
  $incr .= " AND cb.phone LIKE :billing_phone";
}
if ($s_member_status != "") {
  $sch_params[':s_member_status'] = makeSafe($s_member_status);
  $incr.=" AND c.status = :s_member_status";
}
if ($verify_status == "Not Applicable") {
  $incr.=" AND v.status is null";
}elseif ($verify_status != "") {
  $sch_params[':verify_status'] = makeSafe($verify_status);
  $incr.=" AND v.status = :verify_status";
}
if ($sponsor_name != "") {
  $sch_params[':sponsor_name'] = "%" . makeSafe($sponsor_name) . "%";
  $incr.=" AND CONCAT(s.fname,' ',s.lname) LIKE :sponsor_name";
}
if ($sponsor_id != "") {
  $sch_params[':sponsor_id'] = makeSafe($sponsor_id);
  $incr.=" AND s.rep_id = :sponsor_id";
}

if($network_sponsor_id != ""){
  $network_sponsor_id = getname('customer', $network_sponsor_id, 'id', 'rep_id');
  $sch_params[':network_sponsor_rep_id'] = makeSafe($network_sponsor_id);
  $incr .= " AND c.org_sponsor_id = :network_sponsor_rep_id"; 
}

if ($user_name != '') {
  $sch_params[':user_name'] = makeSafe($user_name);
  $incr.=" AND c.user_name=:user_name";
}
if ($zip != '') {
  $sch_params[':zip'] = makeSafe($zip);
  $incr.=" AND c.zip=:zip";
}
if ($address != "") {
  $sch_params[':address'] = "%" . makeSafe($address) . "%";
  $incr.=" AND c.address LIKE :address";
}
if ($city != "") {
  $sch_params[':city'] = "%" . makeSafe($city) . "%";
  $incr.=" AND c.city LIKE :city";
}
if ($country != "") {
  $sch_params[':country'] = makeSafe($country);
  $incr .= " AND c.country_name = :country";
}

if($state != ""){
  $sch_params[':state'] = makeSafe($state);
  $incr .= " AND c.state = :state";
}

if($tax_exempted !=''){
  $sch_params[':tax_exempted'] = makeSafe($tax_exempted);
  $incr .= " AND cs.tax_exempted = :tax_exempted";
}

if ($fromdate != "") {
  $sch_params[':fcreated_at'] = date('Y-m-d', strtotime($fromdate));
  $incr.=" AND DATE(c.created_at) >= :fcreated_at";
}
if ($todate != "") {
  $sch_params[':tcreated_at'] = date('Y-m-d', strtotime($todate));
  $incr.=" AND DATE(c.created_at) <= :tcreated_at";
}

if (count($sch_params) > 0) {
  $has_querystring = true;
}
if (isset($_GET['pages']) && $_GET['pages'] > 0) {
  $has_querystring = true;
  $per_page = $_GET['pages'];
}
$query_string = $has_querystring ? (cx_isset($_GET['page']) !='' ? str_replace('page=' . $_GET['page'], "page=*VAR*", $_SERVER['QUERY_STRING']) : $_SERVER['QUERY_STRING'] . "&page=*VAR*") : 'page=*VAR*';

$options = array(
    'results_per_page' => $per_page,
    'url' => 'customers.php?' . $query_string,
    'db_handle' => $pdo->dbh,
    // 'named_params' => $sch_params
);

$page = cx_isset($_GET['page']) > 0 ? $_GET['page'] : 1;
$options = array_merge($pageinate_html, $options);

if ($is_ajaxed) {
  try {
    $sel_sql = "SELECT c.rep_id, c.id, c.type,c.is_unsubscribe, c.fname, c.lname, 
                c.email, c.status, c.cell_phone,c.user_name,c.sponsor_id,c.country_id,
                c.created_at,c.sub_type,s.fname as s_fname, s.lname as s_lname, 
                s.rep_id as sponsor_rep_id,s.type AS s_type, bt.unilevel_placed
      FROM customer c
      JOIN binary_tree bt ON bt.user_id = c.id
      LEFT JOIN customer s ON(s.id= c.sponsor_id)
      LEFT JOIN customer_shipping_profile cp ON cp.customer_id=c.id
      LEFT JOIN customer_settings cs ON(cs.customer_id = c.id)
     ". $billing_incr ."
    WHERE c.type='Customer' "  . $incr . " GROUP BY c.id  ORDER BY $SortBy $currSortDirection";
    // echo $sel_sql;
    // pre_print($options);
    $paginate = new pagination($page, $sel_sql, $options);
    if ($paginate->success == true) {
      $fetch_rows = $paginate->resultset->fetchAll();
      $total_rows = count($fetch_rows);
    }
  } catch (paginationException $e) {
    echo $e;
    exit();
  }
  include_once 'tmpl/customers.inc.php';
  exit;
}
  /* * ****************    Export Code Start ****************** */

  if (isset($_GET["export"]) && trim($_GET["export"]) != "") {
    
    if ($_GET['export'] == 'export_excel') {
      $content = "";
      if ($_GET['export'] == 'export_excel') {
          $sel_sql = "SELECT c.rep_id, c.id, c.type,c.is_unsubscribe, c.fname, c.lname, 
                      c.email, c.status, if((c.cell_phone = '' OR c.cell_phone is null),
                      cp.phone,c.cell_phone) as cell_phone,c.user_name,
                      cs.tax_exempted,
                      if((c.city='' OR c.city is null),cp.city,c.city) as city,
                      if((c.zip='' OR c.zip is null),cp.zip,c.zip) as zip,
                      if((c.address='' OR c.address is null),cp.add1,c.address) as address,
                      if((c.address2='' OR c.address2 is null),cp.add2,c.address2) as address2,
                      if((c.state='' OR c.state is null),cp.state,c.state) as state,c.created_at
          FROM customer c
          LEFT JOIN customer s ON(s.id= c.sponsor_id)
          LEFT JOIN customer_settings cs ON(cs.customer_id = c.id)
          LEFT JOIN customer_shipping_profile as cp on (cp.customer_id = c.id)
          ". $billing_incr ."
          WHERE c.type='Customer' AND c.id>0 " . $incr . "  GROUP BY c.id  ORDER BY $SortBy $currSortDirection";
        $export_result = $pdo->select($sel_sql, $sch_params);
      
        if (count($export_result) > 0) {
          $content .= "Cutomer ID" . $csv_seprator . "Name" . $csv_seprator . "User Name" . $csv_seprator . "Email" . $csv_seprator . "Phone Number" . $csv_seprator . "City" . $csv_seprator . "State" . $csv_seprator . "Address" . $csv_seprator . "Address 2" . $csv_seprator . "zip" . $csv_seprator . "Status" . $csv_seprator . "Tax Examption" . $csv_seprator . "Join Date" .  $csv_line;
          foreach ($export_result AS $key => $trans) {
            
            $content .= $trans['rep_id'] . $csv_seprator . $trans['fname'] .' '. $trans['lname'] . $csv_seprator . $trans['user_name'] . $csv_seprator . $trans['email'] . $csv_seprator . $trans['cell_phone'] . $csv_seprator . $trans['city'] . $csv_seprator . $trans['state'] . $csv_seprator . $trans['address'] . $csv_seprator . $trans['address2'] . $csv_seprator . $trans['zip'] . $csv_seprator .  $trans['status'] . $csv_seprator .  $trans['tax_exempted'] . $csv_seprator . date('m-d-Y',strtotime($trans['created_at'])) . $csv_line;
          }
        }
      }
      if ($content) {
        header('Content-type: application/vnd.ms-excel');
        header('Content-disposition: attachment;filename=Customers_' . date('YmdHis') . '.xls');
        echo $content;
        exit;
      }
    }
  }

  /* * ****************    Export Code End ******************** */

$select_state = "SELECT id,name FROM `states_c` WHERE country_id IN ('236','40') ORDER BY name";
$temp_state = $pdo->select($select_state);

  
$exStylesheets = array('thirdparty/colorbox/colorbox.css');
$exJs = array('thirdparty/colorbox/jquery.colorbox.js');

$template = 'customers.inc.php';
include_once 'layout/end.inc.php';
?>