<?php
session_start();

//log in form for both administrator and member
function login(){
?>
  <div id="background">
    <form method="post" action="calls.php?action=authenticate" id="form1" >
      <table>
        <tr>
          <td>User name:</td>
          <td><input type="text" placeholder="Username" name="un" id="username" required="required"/></td>
        </tr>

        <tr>
          <td>Password:</td>
          <td><input type="password" placeholder="password" name="pwd" id="password" required="required"/></td>
        </tr>

        <tr>
          <td colspan="2"><input type="submit" name="login" value="Log in" class="button"></td>
        </tr>
      </table>
    </form>
  </div>

<?php
}

function savedata(){
  if (isset($_POST['register'])) {
    $username="";
      $error = array();
      $time1 = time();
      $ad = $_SESSION['un'];
      $date1 = date('Y-m-d',$time1);

      $name =mysql_real_escape_string($_POST['name']);
      $username =mysql_real_escape_string($_POST['username']);
      $password =mysql_real_escape_string($_POST['password']);
      $cpassword =mysql_real_escape_string($_POST['cpassword']);
      $cont3 =mysql_real_escape_string($_POST['cont3']);
      $receiptNo =mysql_real_escape_string($_POST['receiptNo']);

      $admin = mysql_fetch_assoc(mysql_query("select adminId from Administrator where adminUsername='$ad'"));
      $adm = $admin['adminId'];

      $highest =mysql_fetch_assoc(mysql_query('SELECT sum(contributionAmount) as contribution,memberName,username from Contribution,Member where Member.memberId=Contribution.memberId GROUP by username  
            ORDER BY contribution  DESC limit 1'));
      $ch = $highest['contribution'];
      $initial = (0.75*$ch);

      if (empty($name)) {
        array_push($error, "Name required");
      }

      if (empty($username)) {
        array_push($error, "Username required");
      }
      if (empty($cont3)) {
        array_push($error, "contribution required");
      }
      if (empty($receiptNo)) {
        array_push($error, "receiptNo required");
      }

      if (empty($password)) {
        array_push($error, "Password required");
      }
      if ($cpassword!=$password) {
        array_push($error, "Passwords don't match");
      }

      

    else if (count($error)==0 && $cont3>=$initial) {
      $status= 'accepted';

      mysql_query("INSERT INTO Member (memberName,username,password,additionDate) VALUES ('$name','$username','$password','$date1')");

      $mid = mysql_fetch_assoc(mysql_query("select memberId from Member where username='$username'"));
      $mid1 = $mid['memberId'];

      mysql_query("INSERT INTO Contribution (memberId,receiptNo,c_sub_status,contributionDate,contributionAmount,adminId) VALUES ('$mid1','$receiptNo','$status','$date1','$cont3','$adm')");

      return  admin_page();
    }

    else if (count($error)>0) {
      ?>
      <div class="section">
      <?php
          foreach ($error as $value) {
            ?>
            
              <p> <?php echo $value; ?> </p><br>
            <?php
          }
          ?>
          </div>
          <?
          return register();
        }
  }
      
   return  admin_page();
}
        

function login_status(){
  if (isset($_POST['login'])) {
    $un = $_POST['un'];
    $pwd = $_POST['pwd'];
    //$pwd2 = md5($pwd);

    $sql = mysql_query("select username,password from Member where username='$un' AND password='$pwd'");
    $admin = mysql_query("select adminUsername,adminPassword from Administrator where adminUsername='$un' && adminPassword='$pwd'");

    if ($rows = mysql_fetch_assoc($admin)) {
      $_SESSION['un'] = $un;
      //echo $_SESSION['un']." (<a href='?action=logout'>log out</a>)";
      admin_page();
    }

    else if ($row = mysql_fetch_assoc($sql)) {
      $_SESSION['un'] = $un;
    $_SESSION['success'] = "You are logged in";
      member_account();
    }
    

    else
      
      return false;
  }
  
}

function member_account(){
  ?>
  <div class="main_menu">
  <?php  echo $_SESSION['un']; ?> 
  <a href="?action=logout">log out</a><br>
    <li>My Account
      <ul>
        <li><a href="calls.php?action=contribution">Contribution</a>
        
        </li>
        <li><a href="calls.php?action=my_benefits">Benefits</a></li>
        <li><a href="calls.php?action=lstatus">Loan Status</a></li>
    </li>
    
  </div>
   <div class="aside">
    <h3>Recently Viewed</h3>
  </div>
  <?php
}

function log_out(){
  if (isset($_POST['logout'])) {
    session_destroy();
    unset($_SESSION['un']);
    lodin();
  }
}

/*function accept_loan(){
  $allan_loan = 510000;
  
  $time = time();

  $time = date('Y-m-d',$time);
  $date = date('Y-m-d',strtotime('- 6 month'));
  $pay = date('Y-m-d',strtotime('+1 year'));
  
  $sum_contribution = mysql_query('SELECT memberName, sum(contributionAmount) as Sum from Member,Contribution,Loan where Contribution.memberId=Member.memberId && Loan.memberId=Member.memberId GROUP by memberName');

//checks the appearence of a member in the past 6 months
  $appear = mysql_query("SELECT memberName,COUNT(memberName) AS appearence,loanAmount from Member,Contribution,Loan where Contribution.memberId=Member.memberId && Loan.memberId=Member.memberId && contributionDate>'$date' GROUP by memberName");

  while ($period = mysql_fetch_assoc($sum_contribution)) {
    $cu = $period['Sum'];

    while ($period2 = mysql_fetch_assoc($appear)) {
      $dt = $period2['appearence'];
      $dta = $period2['loanAmount'];
      break;
    }
     $dt = $period2['appearence'];
      $dta = $period2['loanAmount'];
    if ($dt==6) {
        $mloan = (0.5*$cu);
    if ($dta<=$mloan) {
        echo $period2['memberName']." your loan of ".$dta." as been accepted"."<br>";
        //mysql_query("");
        //$gh = array_sum(array)
        //return $gh;
      }
      else{

      echo $period2['memberName']." your loan of ".$dta." as been denied"."<br>";
      }
      }
    else{
        echo $period2['memberName']." your loan of ".$dta." as been denied"."<br>";
    }
    
  }
}*/

//function for page displayed when the administrator has just logged into the system
function admin_page(){
?>
  <div class="main_menu">
  <?php echo $_SESSION['un']." (<a href='?action=logout' style='color:red;'>log out</a>)"; ?>
    <ul>
      <li><a href="calls.php?action=reg">Add Member</a></li>
      <li><a href="calls.php?action=crecords">Pending Records</a></li>
      <li><a href="calls.php?action=follow"> Add Business Details</a></li>
      <li class="report"><a href="#">View Reports</a>
        <ul class="report2">
          <li><a href="calls.php?action=regular">Regular Members</a></li>
          <li><a href="calls.php?action=loan">Loan Details</a></li>
          <li><a href="calls.php?action=benefit">Benefits</a></li>
          <li><a href="calls.php?action=rfollowup">Business follow up</a></li>
          <li><a href="calls.php?action=approved_l">Approved Loans</a></li>
          <li><a href="calls.php?action=approved_c">Approved Contributions</a></li>
          <li><a href="calls.php?action=de">Loan Defaulter</a></li>
          <li><a href="calls.php?action=saving">Saaco Savings</a></li>
        </ul>

      </li>
    </ul>
  </div>
  <div class="aside">
    <h3>Recently Viewed</h3>
  </div>
<?php
}

function report_page(){
?>

  <div id="s1" class="aside">
  </h2> <a href="?action=logout">log out</a>
    <ul> 
      <li><a href="?action=regular">Regular Members</a></li>
      <li><a href="?action=ldetails">Loan Details</a></li>
      <li><a href="?action=benefits">Benefits</a></li>
      <li><a href="?action=ibusiness">Business idea details</a></li>
    </ul>
  </div>

<?php
}


function Business_followup()
{
  $profit = mysql_query('SELECT (sum(businessReturn)-initialInvestment) as returns,sum(businessReturn),initialInvestment,businessDescription,memberName from Business_idea,Business_followup,Member where Business_idea.ideaId=Business_followup.ideaId && Business_idea.memberId=Member.memberId GROUP by Business_idea.ideaId');
?>
    <div class="section">
      <h3>Business Follow up</h3>
      <table class="table">
        <tr class="th">
          <th>Idea Name</th>
          <th>Member suggested</th>
          <th>Initial Investment</th>
          <th>Outcome</th>
          <th>Performance (%)</th>
        </tr>
<?php
  while($f=mysql_fetch_assoc($profit))
  {
    ?>
          <tr>
          <td class='odd'><?php echo $f['businessDescription']; ?></td>
          <td class='even'><?php echo $f['memberName']; ?></td>
          <td class='odd'><?php echo $f['initialInvestment']; ?></td>
    <?php
    $out=$f['returns'];
    if ($out>=0) {
      $per = round((($out/$f['sum(businessReturn)'])*100));
      ?>
      <td id='out_status' style='color:blue' class='even'><?php echo $out; ?></td>
      <td style='color:blue'><?php  echo $per; ?></td>
      <?php
    }
    else{
      $out1 = ($out*-1);
      $per2 = round((($out1/$f['sum(businessReturn)'])*100));
      ?>
      <td id='out_status' style='color:red' class='even'><?php echo $out1;?></td>
      <td style='color:red'><?php  echo $per2; ?></td>
    </tr>
    <?php
    }
  }
  ?>
  </table>
  <div id="key">
    <h6>Key</h6>
    <div style="background-color: red; width: 30px; height: 20px;"></div><p>Loss</p>
    <div style="background-color: blue; width: 30px; height: 20px;"></div><p>Profit</p>
  </div>
</div>
  
  <?
  return admin_page();
}

function benefits(){
   $btime = time();
  $benefitdate = date('Y-m-d',$btime)."<br>";
  $admin = $_SESSION['un'];
  $ad = mysql_fetch_assoc(mysql_query("select adminId from Administrator where adminUsername='$admin'"));
  $adminId = $ad['adminId'];

  $return = mysql_real_escape_string($_POST['breturn']);
  $bname = mysql_real_escape_string($_POST['business_name']);

//returns the id of the selected business idea
$iname1 = mysql_fetch_assoc(mysql_query("select ideaId from Business_idea where businessDescription='$bname'"));
    $ideaId = $iname1['ideaId'];

//add return to the business followup table
  mysql_query("insert into Business_followup (followupDate,businessReturn,ideaId,adminId) value ('$benefitdate','$return','$ideaId','$adminId')");

 
// returns the highest contributor basing on the start of the business
  $highest =mysql_fetch_assoc(mysql_query("select username,SUM(contributionAmount) as sum_each from Member,Contribution where Member.memberId=Contribution.memberId && additionDate<=(select ideaDate from Business_idea where businessDescription='$bname') GROUP by memberName order by sum_each desc LIMIT 1"));
  $hig = $highest['sum_each'];
  $hign = $highest['username'];

//returns the total amount of the the qualified member
  $total =mysql_fetch_assoc(mysql_query("select sum(contributionAmount) as total from Contribution,Member where additionDate<=(select ideaDate from Business_idea where businessDescription='$bname') && Member.memberId=Contribution.memberId"));
  $to = $total['total'];

  //returns the profit/loss from the business for a specific business idea
    $sel=mysql_fetch_assoc(mysql_query("SELECT initialInvestment,businessDescription from Business_idea where businessDescription='$bname'"));
    $outcome1 = ($return-$sel['initialInvestment']);
  //members qualified to receive benefits
  $hey = mysql_query("select SUM(contributionAmount) as sum_each,username from Member,Contribution where Member.memberId=Contribution.memberId && additionDate<=(select ideaDate from Business_idea where businessDescription='$bname') GROUP by memberName");

  while ($member = mysql_fetch_assoc($hey)) {
    $qualified = $member['username'];
    $total_each = $member['sum_each'];

    $selt = mysql_fetch_assoc(mysql_query("select memberId from Member where username='$qualified'"));
    $st = $selt['memberId'];
      
    if ($outcome1>0) {
      $highest_cont = (0.05*$outcome1);
      $savings = round((0.3*$outcome1));
      $distribution = (0.65*$outcome1);
      if ($qualified==$hign) {
        $fg =round(((($total_each/$to)*$distribution)+$highest_cont));
          mysql_query("insert into Benefit (benefitAmount,benefitDate,saving,memberId,ideaId) value ('$fg','$benefitdate','$savings','$st','$ideaId')");
      }
      else{
          $fg =round((($total_each/$to)*$distribution));
          mysql_query("insert into Benefit (benefitAmount,benefitDate,memberId,ideaId) value ('$fg','$benefitdate','$st','$ideaId')");
      }   
    }
       
  }
  return admin_page();
}

function investment_approval(){

//total contribution amount in the saaco
$total_contribution = mysql_fetch_assoc(mysql_query('select sum(contributionAmount) as total_cont from Contribution'));

// total amount invested in different businesses
$total_investment = mysql_fetch_assoc(mysql_query('select sum(initialInvestment) as total_invest from Business_idea'));

//returns the total amount of loans accepted 
$total_loans = mysql_fetch_assoc(mysql_query("select sum(loanAmount) as l_total from Loan where loanStatus='accepted'"));

//selects only loans accepted
$total_benefits = mysql_fetch_assoc(mysql_query("select sum(benefitAmount) as total_benefit,sum(saving) as total_saving from Benefit"));

$available = (($total_contribution['total_cont']+$total_benefits['total_saving']+$total_benefits['total_benefit'])-($total_loans['l_total']+$total_investment['total_invest']));
}

function regular_member(){

$time = $_POST['time'];
$time1 = date_create($time);
date_modify($time1,"-6 month");
$d_date = date_format($time1,"Y-m-d");

$appears = mysql_query("select memberName,username,count(username) as appearences from Member,Contribution where Contribution.memberId=Member.memberId && contributionDate between '$d_date' and '$time' group by username");

//sregular_member();
  ?>

  <div class="section">
  <?php ?>
      <h3>Regular Members</h3><br>
      <table class="table">
        <tr>
          <th>Member Name</th>
          <th>Username</th>
        </tr>
  <?php
  while ($regular = mysql_fetch_assoc($appears)) {

    $regular1 = $regular['appearences'];

      if ($regular1==6) {
        ?>
        <tr>
          <td><?php echo $regular['memberName']; ?></td>
          <td><?php echo $regular['username']; ?></td>
        </tr>
        
        <?php
      }

      
  }
  ?>
  </table>
  </div>
  <?php
  return admin_page();
}

function benefit_report(){

  $bresult = mysql_query("select memberName,username,benefitAmount,benefitDate,businessDescription from Member,Benefit,Business_idea where Member.memberId=Benefit.memberId && Business_idea.ideaId=Benefit.ideaId");
?>
  <div class="section">
  <h3>Benefit Report</h3>
    <table class="table">
      <tr>
        <th>Member Name </th>
        <th>Benefit Amount </th>
        <th>Benefit Date </th>
        <th>Business Idea Name </th>
      </tr>
<?php
    while ($b_result = mysql_fetch_assoc($bresult)) {
      ?>
      <tr>
        <td><?php echo $b_result['memberName']; ?></td>
        <td><?php echo $b_result['benefitAmount']; ?></td>
        <td><?php echo $b_result['benefitDate']; ?></td>
        <td><?php echo $b_result['businessDescription']; ?></td>
      </tr>
      <?php
    }
    ?>
     </table>
  </div>
<?php
return admin_page();
}

//display benefits depending on the logged user
function member_benefits(){
  $smember = $_SESSION['un'];
  $bresult = mysql_query("select memberName,username,benefitAmount,benefitDate,businessDescription from Member,Benefit,Business_idea where Member.memberId=Benefit.memberId && Business_idea.ideaId=Benefit.ideaId && username='$smember'");
?>
  <div id="benefit" class="section">
      <table class="table">
      <h3>My Benefits</h3>
        <tr>
          <th>Member Name </th>
          <th>Benefit Amount </th>
          <th>Benefit Date </th>
          <th>Business Idea Name </th>
        </tr>
<?php

  while ($b_result = mysql_fetch_assoc($bresult)) {
    ?>
    <tr>
      <td><?php echo $b_result['memberName']; ?></td>
      <td><?php echo $b_result['benefitAmount']; ?></td>
      <td><?php echo $b_result['benefitDate']; ?></td>
      <td><?php echo $b_result['businessDescription']; ?></td>
    </tr>
    
<?php

  }

?>
   </table>
  </div>
<?php

  return member_account();

}

// adding a new member
function register(){
?>
  <div id="reg" class="section">
    <form method="post" action="calls.php?action=saveinfo"  >
      <h2>Register new Member </h2>

      <table>
        <tr>
          <td colspan="2"></td>
        </tr>
        <tr>
          <td>Name:</td>
          <td><input type="text" name="name" required="required"></td>
        </tr>

        <tr>
          <td>Username:</td>
          <td><input type="text" name="username" required="required"></td>
        </tr>

        <tr>
          <td>Initial Contribution:</td>
          <td><input type="text" name="cont3" required="required"></td>
        </tr>

        <tr>
          <td>Receipt Number:</td>
          <td><input type="text" name="receiptNo" required="required"></td>
        </tr>

        <tr>
          <td>Password:</td>
          <td><input type="password" name="password" id="pwd" required="required"></td>
        </tr>
        <tr>
          <td>Confirm Password:</td>
          <td><input type="password" name="cpassword" id="cpwd"></td><span id="note"></span>
        </tr>

        <tr>
          <td colspan="2"><input type="submit" name="register" value="Add member" class="button"></td>
        </tr>

      </table>
  </form>
  </div>   
<?php 
return admin_page();
}

//entering business returns 
function followerup(){
?>
  <div class="section">
    <form name="business_returns" action="calls.php?action=save" method="post">
      <h3>Enter Rusiness Returns</h3><br>
        <select name="business_name">
          <option selected="selected">None</option>
      <?php
      $idea = mysql_query('select ideaId,businessDescription from Business_idea');

      while ($idea1 = mysql_fetch_assoc($idea)) {
      ?>

        <option><?php echo $idea1['businessDescription']; ?></option>

      <?php
      }
      ?>
      </select>
      <input type="text" name="breturn">
      <input type="submit" name="save" value="Save" class="button">
    </form>
  </div>

<?php
return admin_page();
}

// display the list of regular member basing on a specified date
function sregular_member(){
?>
  <div class="section" id="regular">
  <h3>Enter Date</h3> 
    <form action="calls.php?action=regul" method="post">
      Enter Date: <input type="date" name="time" placeholder="yyyy-mm-dd">
       <input type="submit" name="regulardate" value="Submit" class="button">
    </form>
  </div>
<?php
return admin_page();
}

function my_contribution(){

  $unm = $_SESSION['un'];
  $mysum = mysql_fetch_assoc(mysql_query("select memberName,contributionAmount,sum(contributionAmount) as Mysum from Contribution,Member where Contribution.memberId=Member.memberId && Member.username='$unm' group by Member.username"));

  $mycont = mysql_query("select memberName,contributionAmount,contributionDate from Contribution,Member where Contribution.memberId=Member.memberId && Member.username='$unm' ORDER by contributionDate asc");
?>
<div class="section">
  <h3>Contributions</h3>
  <table class="table">
    <tr>
      <th>Contribution Amount </th>
      <th>Contribution Date </th>
    </tr>
    <?php
    while ($my_cont = mysql_fetch_assoc($mycont)) 
    {
    ?>
      <tr>
        <td><?php echo $my_cont['contributionAmount']; ?> </td>
        <td><?php echo $my_cont['contributionDate']; ?></td>
      </tr>
    <?php
    }
    ?>
    <tr>
    <td>Total</td>
    <td><?php echo $mysum['Mysum']; ?></td>
    </tr>
  </table>
</div>
  <?php
  return member_account();
}

function sent_file(){
return admin_page();
}

function approved_contr(){

  $approved = mysql_query("SELECT memberName,contributionAmount,contributionDate FROM Contribution,Member WHERE c_sub_status='accepted' && Contribution.memberId=Member.memberId ORDER BY contributionDate DESC");
?>
  <div class="section">
  <h3>List of Approved Contributions</h3>
  <table class="table">
  <tr>
    <th>Member Name</th>
    <th>Contribution Amount</th>
    <th>Contribution Date</th>
  </tr>
  
<?php
  while ($apr_cont = mysql_fetch_assoc($approved)) {

?>
      <tr>
        <td><?php echo $apr_cont['memberName']; ?></td>
        <td><?php echo $apr_cont['contributionAmount']; ?></td>
        <td><?php echo $apr_cont['contributionDate']; ?></td>
      </tr>

<?php
  }
?>  
  </table>
</div>
<?php

return admin_page();
}

function approved_loan(){

  $approved = mysql_query("SELECT memberName,loanAmount,loanDate FROM Loan,Member WHERE loanStatus='accepted' && Loan.memberId=Member.memberId ORDER BY loanDate DESC");
?>
  <div class="section">
  <h3>List of Approved Loan</h3>
  <table class="table">
  <tr>
    <th>Member Name</th>
    <th>Loan Amount</th>
    <th>Loan Date</th>
  </tr>
<?php
  while ($apr_cont = mysql_fetch_assoc($approved)) {

?>
      <tr>
        <td><?php echo $apr_cont['memberName']; ?></td>
        <td><?php echo $apr_cont['loanAmount']; ?></td>
        <td><?php echo $apr_cont['loanDate']; ?></td>
      </tr>

<?php
  }
?>  
  </table>
</div>
<?php

return admin_page();
}

function pending_contribution(){
  $filename = "/home/nicholaws/Desktop/ppp/records.txt";
  $fileopen = fopen($filename, "r");
  $read = fread($fileopen, filesize($filename));

  $hold = explode("\n", $read);

  $size=count($hold);
?>
<div class="section">
<?php
  for ($d=0; $d < $size; $d++) {
    $hold2=explode(" ", $hold[$d]);
    if (strcmp($hold2[0], "contribution")==0) {
      list($val1,$val2,$val3,$val4)=$hold2;
      
?>
  <p><?php echo $val1; ?></p>
  <p><?php echo $val2; ?></p>
  <p><?php echo $val3; ?></p>
  <p><?php echo $val4; ?></p>
  <form action="" method="POST">
    <input type="hidden" name="amount" value="<?php echo($val2); ?>">
    <input type="hidden" name='receiptNo' value="<?php echo ($val3); ?>">
    <input type="hidden" name="username" value="<?php echo($val4); ?>">
    <button type="submit" name="approveContribution" class="btn btn-success" >Approve </button>
    <button type="submit" name="denyContribution" class="btn btn-danger">Deny </button>
  </form>


<?php
    $a = "SELECT memberId FROM Member WHERE username = '$hold2[3]'"; 
    $z = mysql_fetch_assoc(mysql_query($a));
    $x = $z['memberId'];
    $contstatus = "accepted";
    $contrdate=time();
    $contrdate=date('y-m-d',$contrdate);

    $insert = "INSERT INTO Contribution (receiptNo, memberId,c_sub_status, contributionDate, contributionAmount)VALUES ('$hold2[2]','$x','$contstatus','$contrdate','$hold2[1]')";

    $run=mysql_query($insert);

    
  }
  if (strcmp($hold2[0], "loan_request")==0) {
    list($loan1,$loan2,$loan3)=$hold2;
        
  ?>
    <p><?php echo $loan1; ?></p>
    <p><?php echo $loan2; ?></p>
    <p><?php echo $loan3; ?></p>
    <form action="" method="POST">
      <input type="hidden" name="requestamount" value="<?php echo($loan2); ?>">
      <input type="hidden" name='username' value="<?php echo ($loan3); ?>">
      <!--<input type="hidden" name="receipt" value="<?php echo ($val5); ?>"> -->
      <button type="submit" name="approveloanrequest" class="btn btn-success" >Approve </button>
      <button type="submit" name="denyloanrequest" class="btn btn-danger">Deny </button>
    </form>

  <?php
        //$a = "SELECT memberId FROM Member WHERE username = '$hold2[2]'"; 
        //$z = mysql_fetch_assoc(mysql_query($a));
        //$x = $z['memberId'];
    $loanstatus = "accepted";
    $requestdate=time();
    $requestdate=date('y-m-d',$requestdate);
    $insert2 = "INSERT INTO Loan (loanDate,loanStatus,loanAmount)VALUES (,'$requestdate','$loanstatus','$hold2[1]')";
    $run2=mysql_query($insert2);
      
  }
  if (strcmp($hold2[0], "business_idea")==0) {
        list($idea1,$idea2,$idea3)=$hold2;
        
  ?>        
    <p><?php echo $idea1; ?></p>
    <p><?php echo $idea2; ?></p>
    <p><?php echo $idea3; ?></p>
    <form action="" method="POST">
      <input type="hidden" name="ideadescription" value="<?php echo($idea2); ?>">
      <input type="hidden" name='initialinvestment' value="<?php echo ($idea3); ?>">
      <!--<input type="hidden" name="receipt" value="<?php echo ($val5); ?>"> -->
      <button type="submit" name="approveloanidea" class="btn btn-success" >Approve </button>
      <button type="submit" name="denyidea" class="btn btn-danger">Deny </button>
    </form>

  <?php
    $a3 = "SELECT memberId FROM Member WHERE memberName = '$hold2[3]'"; 
    $z3 = mysql_fetch_assoc(mysql_query($a3));
    $x3 = $z3['memberId'];
    $ideadate=time();
    $ideadate=date('y-m-d',$ideadate);
    $insert4 = "INSERT INTO Business_idea (businessDescription,initialInvestment,ideaDate, memberId)VALUES ('$hold2[1]','$hold2[2]','$ideadate', '$x3')";
    $run4=mysql_query($insert4);
      
  }

  }
} 
  
  /*$a = "SELECT memberId FROM Member WHERE username = '$hold2[3]'"; 
  $z = mysql_fetch_assoc(mysql_query($a));
  $x = $z['memberId'];
  $contstatus = "accepted";
  $contrdate=time();
  $contrdate=date('y-m-d',$contrdate);

  $insert = "INSERT INTO Contribution (receiptNo, memberId,c_sub_status, contributionDate, contributionAmount)VALUES ('$hold2[2]','$x','$contstatus','$contrdate','$hold2[1]')";

  $run=mysql_query($insert);
  }
}*/
//return admin_page();

function loan_status(){
  $sa = mysql_query("select loanAmount,memberName,username from Loan,Member where Loan.memberId=Member.memberId");
?>
  <div class="section">
    <h3>Loan Details</h3>
    <table>
      <tr>
        <th>Member Name</th>
        <th>Loan Amount</th>
        <th>Monthly Repayment</th>
        <th>Remaining Amount</th>
        <th>Loan Due Date</th>
      </tr>
<?php
  while ($lst = mysql_fetch_assoc($sa)) {
    $lrs = $lst['loanAmount'];
    $uname = $lst['username'];
    $sa1 = mysql_query("select payDate,sum(amountPaid) from Loan_repayment,Member where Loan_repayment.memberId=Member.memberId and username='$uname' group by username");
    while ($sdd = mysql_fetch_assoc($sa1)) {
      $sd1 = $sdd['sum(amountPaid)'];
      $sd2 = $sdd['payDate'];
    }
    $lp = ((($lrs*0.03)*12)+$lrs);
    $lpm = ($lp/12);
    $remain = ($lp - $sd1);
?>
  <tr>
    <td><?php echo $lst['memberName']; ?></td>
    <td><?php echo $lrs; ?></td>
    <td><?php echo $lpm; ?></td>
    <td><?php echo $remain; ?></td>
    <td><?php echo $sd2; ?></td>
    
  </tr>
<?php
  }
?>
  </table>
</div>

<?php
  return admin_page();
}

function my_loan_status(){
   $smember = $_SESSION['un'];
  $lst = mysql_fetch_assoc(mysql_query("select loanAmount,loanDate,memberName,username from Loan,Member where Loan.memberId=Member.memberId"));
  $time1 = date_create($lst['loanDate']);
    date_modify($time1,"+1 year");
    $d_date2 = date_format($time1,"Y-m-d");

  $lrs = $lst['loanAmount'];
  $uname = $lst['username'];
  $sdd = mysql_fetch_assoc(mysql_query("select payDate,sum(amountPaid) from Loan_repayment,Member where Loan_repayment.memberId=Member.memberId and username='$smember' group by username"));
  $sd1 = $sdd['sum(amountPaid)'];
  $sd2 = $sdd['payDate'];
  $lp = ((($lrs*0.03)*12)+$lrs);
  $lpm = ($lp/12);
  $remain = ($lp - $sd1);
?>
  <div class="section">
    <h3>Loan Details</h3>
    <table>
      <tr>
        <th>Member Name</th>
        <th>Loan Amount</th>
        <th>Monthly Repayment</th>
        <th>Remaining Amount</th>
        <th>Loan Due Date</th>
      </tr>
      <tr>
        <td><?php echo $lst['memberName']; ?></td>
        <td><?php echo $lrs; ?></td>
        <td><?php echo $lpm; ?></td>
        <td><?php echo $remain; ?></td>
        <td><?php echo $d_date2; ?></td>
        
      </tr>
    </table>
  </div>

<?php
  return member_account();
}

function loan_defaulter(){
  

  $d_date = date('Y-m-d',time());
  $sa = mysql_query("select loanAmount,loanDate,memberName,username from Loan,Member where Loan.memberId=Member.memberId");
?>
  <div class="section">
    <h3>Loan Defaulters</h3>
    <table>
      <tr>
        <th>Member Name</th>
        <th>Loan Amount</th>
        <th>Monthly Repayment</th>
        <th>Remaining Amount</th>
        <th>Loan Due Date</th>
      </tr>
<?php
  while ($lst = mysql_fetch_assoc($sa)) {

    $time1 = date_create($lst['loanDate']);
    date_modify($time1,"+1 year");
    $d_date2 = date_format($time1,"Y-m-d");

    $lrs = $lst['loanAmount'];
    $uname = $lst['username'];

    $sa1 = mysql_query("select sum(amountPaid) from Loan_repayment,Member where Loan_repayment.memberId=Member.memberId and username='$uname' group by username");
    while ($sdd = mysql_fetch_assoc($sa1)) {
      $sd1 = $sdd['sum(amountPaid)'];
    }
    $lp = ((($lrs*0.03)*12)+$lrs);
    $lpm = ($lp/12);
    $remain = ($lp - $sd1);
    if ($remain>0 && $d_date2<$d_date) {
?>
  <tr>
    <td><?php echo $lst['memberName']; ?></td>
    <td><?php echo $lrs; ?></td>
    <td><?php echo $lpm; ?></td>
    <td><?php echo $remain; ?></td>
    <td><?php echo $d_date2; ?></td>
    
  </tr>
<?php
    }
  }
?>
  </table>
</div>

<?php
  return admin_page();
}

function savings(){
  $total_benefits = mysql_query("select sum(saving) as total_saving,businessDescription,benefitDate from Benefit,Business_followup,Business_idea where Business_followup.ideaId=Benefit.ideaId and Business_idea.ideaId=Business_followup.ideaId group by businessDescription");
?>
  <div class="section">
    <h3>Saaco Savings</h3>
    <table>
      <tr>
        <th>Business Name</th>
        <th>Date</th>
        <th>Savings</th>
      </tr>
<?php

  while ($benefit = mysql_fetch_assoc($total_benefits)) {
?>
      <tr>
        <td><?php echo $benefit['businessDescription']; ?></td>
        <td><?php echo $benefit['benefitDate']; ?></td>
        <td><?php echo $benefit['total_saving']; ?></td>
      </tr>

<?php
  }
?>
    </table>
  </div>
<?php

return admin_page();
}


function file_record(){
  $filename = "records.txt";
  $fileopen = fopen($filename, "r");
  $read = fread($fileopen, filesize($filename));

  $hold = explode("\n", $read);
?>
<div class="section">
<h3>Sent Records</h3>
<table>
<?php
  $size=count($hold);
  for ($d=0; $d < $size; $d++) { 
      $hold2=explode(" ", $hold[$d]);

    if (strcmp($hold2[0], "contribution")==0) {
      list($val1,$val2,$val3,$val4)=$hold2;
      
?>
<tr>
            <td><?php echo $val1; ?></td>
            <td><?php echo $val2; ?></td>
            <td><?php echo $val3; ?></td>
            <td><?php echo $val4; ?></td>
            <td>
              <form action="" method="POST">
                <input type="hidden" name="amount" value="<?php echo($val2); ?>">
                <input type="hidden" name='receiptNo' value="<?php echo ($val3); ?>">
                <input type="hidden" name="username" value="<?php echo($val4); ?>">
                <!--<input type="hidden" name="receipt" value="<?php echo ($val5); ?>"> -->
                <button type="submit" name="approveContribution" class="btn btn-success" >Approve </button>
                <button type="submit" name="denyContribution" class="btn btn-danger">Deny </button>
              </form>
            </td>
          </tr>

<?php
      $a = "SELECT memberId FROM Member WHERE username = '$hold2[3]'"; 
      $z = mysql_fetch_assoc(mysql_query($a));
      $x = $z['memberId'];
      
      $contrdate=time();
      $contrdate=date('y-m-d',$contrdate);
      if(isset($_POST['approveContribution'])) {
        $contstatus = "accepted";
      $insert = "INSERT INTO Contribution (receiptNo, memberId,c_sub_status, contributionDate, contributionAmount)VALUES ('$hold2[2]','$x','$contstatus','$contrdate','$hold2[1]')";

      $run=mysql_query($insert);}
      else if(isset($_POST['denyContribution'])) {
        $contstatus = "dennied";
      $insert = "INSERT INTO Contribution (receiptNo, memberId,c_sub_status, contributionDate, contributionAmount)VALUES ('$hold2[2]','$x','$contstatus','$contrdate','$hold2[1]')";

      $run=mysql_query($insert);
}

    
  }
      if (strcmp($hold2[0], "loan_request")==0) {
      list($loan1,$loan2,$loan3)=$hold2;
      
?>
<tr>
            <td><?php echo $loan1; ?></td>
            <td><?php echo $loan2; ?></td>
            <td><?php echo $loan3; ?></td>
            
            <td>
              <form action="" method="POST">
                <input type="hidden" name="requestamount" value="<?php echo($loan2); ?>">
                <input type="hidden" name='username' value="<?php echo ($loan3); ?>">

                <button type="submit" name="approveloanrequest" >Approve </button>
                <button type="submit" name="denyloanrequest" >Deny </button>
              </form>
            </td>
          </tr>

<?php
      $a = "SELECT memberId FROM Member WHERE username = '$hold2[2]'"; 
      $z = mysql_fetch_assoc(mysql_query($a));
      $x = $z['memberId'];
      $loanstatus = "accepted";
      $requestdate=time();
      $requestdate=date('y-m-d',$requestdate);
      $insert2 = "INSERT INTO Loan (loanDate,loanStatus,loanAmount)VALUES (,'$requestdate','$loanstatus','$hold2[1]')";
      $run2=mysql_query($insert2);
      
    }
if (strcmp($hold2[0], "business_idea")==0) {
      list($idea1,$idea2,$idea3)=$hold2;
      
?>
<tr>
            <td><?php echo $idea1; ?></td>
            <td><?php echo $idea2; ?></td>
            <td><?php echo $idea3; ?></td>
            
            
            <td>
              <form action="" method="POST">
                <input type="hidden" name="ideadescription" value="<?php echo($idea2); ?>">
                <input type="hidden" name='initialinvestment' value="<?php echo ($idea3); ?>">
                <!--<input type="hidden" name="receipt" value="<?php echo ($val5); ?>"> -->
                <button type="submit" name="approveloanidea"  >Approve </button>
                <button type="submit" name="denyidea" >Deny </button>
              </form>
            </td>
          </tr>
<?php
      $a3 = "SELECT memberId FROM Member WHERE memberName = '$hold2[3]'"; 
      $z3 = mysql_fetch_assoc(mysql_query($a3));
      $x3 = $z3['memberId'];
      $ideadate=time();
      $ideadate=date('y-m-d',$ideadate);
      $insert4 = "INSERT INTO Business_idea (businessDescription,initialInvestment,ideaDate, memberId)VALUES ('$hold2[1]','$hold2[2]','$ideadate', '$x3')";
      $run4=mysql_query($insert4);

    
} 
if (strcmp($hold2[0], "loan_repayment")==0) {
      list($repayment1,$repayment2,$repayment3)=$hold2;
      
?>
<tr>
            <td><?php echo $repayment1; ?></td>
            <td><?php echo $repayment2; ?></td>
            <td><?php echo $repayment3; ?></td>
            
            <td>
              <form action="" method="POST">
                <input type="hidden" name="amount" value="<?php echo($repayment2); ?>">
                <input type="hidden" name='date' value="<?php echo ($repayment3); ?>">
                <button type="submit" name="approverepayment" class="btn btn-success" >Approve </button>
                <button type="submit" name="denyrepayment" class="btn btn-danger">Deny</button>
              </form>
            </td>
          </tr>
<?php
      $a2 = "SELECT memberId FROM Member WHERE memberName = '$hold2[3]'"; 
      $z2 = mysql_fetch_assoc(mysql_query($a2));
      $x2 = $z2['memberId'];
      $lnid="SELECT loanID FROM Member,Loan WHERE Member.memberId=Loan.memberId ";
      $ln1 = mysql_fetch_assoc(mysql_query($lnid));
      $ln2 = $z2['loanId'];
      $paydate=time();
      $paydate=date('y-m-d',$paydate);
      $insert3 = "INSERT INTO Loan_repayment (amountPaid,payDate, memberId,loanId)VALUES ('$hold2[1]','$paydate','$x2','$ln2')";
      $run3=mysql_query($insert3);
      
}
}
?>
</table>
</div>
<?php
      
  fclose($fileopen);

  return admin_page();
}

?>
