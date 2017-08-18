<?php
//calls.php 
include("functions.php");
//connect to database 
$c = mysql_connect("localhost","root","nicho123");
If(!$c){  echo mysql_error(); exit();}

$db = mysql_select_db("family_saaco");
If(!$db){  echo mysql_error(); exit();}

?>
<!DOCTYPE html>
<html>
<head>
  <title></title>
  <link rel="stylesheet" href="saaco.css" type="text/css"/>
  
</head>

<body>
<div id="center">
      <header>
      <div id="head">
        <img src="logo.png">
        <h1> FSS SAACO </h1>
      </div>
            
        <a href="?action=login" id="log">Log in</a>
      </header>
      <?php
      @$act = $_REQUEST['action'];

      switch(@$act){
      case "login":  
      login(); 
      break;

      case "approved_c":  
      approved_contr(); 
      break;

      case "saving":  
      savings(); 
      break;

      case "approved_l":  
      approved_loan(); 
      break;

      case "my_benefits":  
      member_benefits(); 
      break;

      case "crecords":  
      file_record(); 
      break;

      case "lrecords":  
      sent_file(); 
      break;

      case "contribution":  
      my_contribution(); 
      break;

      case "saveinfo":
      savedata();
      break;

      case "authenticate":
      login_status();
      break;

      case "reports":
      report_page();
      break;

      case "logout":
      log_out();
      break;

      case "regular":
      sregular_member();
      break;

      case "regul":
      regular_member();
      break;

      case "benefit":
      benefit_report();
      break;

      case "rfollowup":
      Business_followup();
      break;

      case "loan":
      loan_status();
      break;

      case "benefits":
      benefits();
      break;

      case "follow":
      followerup();
      break;

      case "reg":
      register();
      break;

      case "de":
      loan_defaulter();
      break;

      case "lstatus":
      my_loan_status();
      break;

      case "save":
      benefits();
      break;

       case "crecords":
      pending_contribution($val1,$val2,$val3,$val4);
      break;

      }

      ?>
</div>
<script type="text/javascript" src="jquery.js"></script>
<script type="text/javascript" src="saaco.js"></script>
</body>
</html>

