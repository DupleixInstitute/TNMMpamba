<html>
<head>

<?php

//$username='root';
//$passwd='sefalana2008';
$username=$_POST['username'];
//echo  "BBS\ ";
/*
$bbs='BBS\\';

$new=$bbs.$username;
$ldappass=$_POST['password'];
//echo $new.$ldappass;

$ldaprdn  = $new;     // ldap rdn or dn
$ldappass = $ldappass;  // associated password

// connect to ldap server
$ldapconn = ldap_connect("10.0.9.10")
    or die("Could not connect to LDAP server.");

if ($ldapconn) {

    // binding to ldap server
    $ldapbind = ldap_bind($ldapconn, $ldaprdn, $ldappass);

    // verify binding
    if ($ldapbind) {
        echo "";
    } else {
	
     echo "<meta http-equiv=\"refresh\" content=\"0;URL=index.php\">";
		

    }

}
*/



$pass="";
$host="localhost"; // Host name 
$user="root"; // Mysql username 
$db_name="corporatescoring"; // Database name 
// Connect to server and select databse.
$ip =gethostbyname($_SERVER['REMOTE_ADDR']); 
//echo $ip;


$connect=mysqli_connect($host,$user,$pass,$db_name); 
//echo $pass;
  if (!$connect) {
  mysqli_close($connect); 
  echo "Cannot connect to the database! Please Check your username and password."; 
}
else {
mysqli_select_db($connect,$db_name)
or die("Couldn't open $dbase: ".mysql_error());

}


?>
<link href="btu.css" rel="stylesheet" type="text/css">

<head>
<body>
<table width="760" border="0" align="center" cellpadding="0" cellspacing="0" class="bdr" bgcolor="#ECF8FF">
  <tr>
    <td colspan="6" class="s_text"><table width="100%" border="0" cellspacing="0" cellpadding="0" >
      <tr>
        <td width="1286"><table width="770" height="165">
<td><img src="img1.gif" alt="nknk" name="g" width="385" height="208"/>&nbsp;</td>
<td><font color="#FFFFFF" size="16" face="Arial Narrow"><img src="img2.JPG" alt="nknk" name="g" width="385" height="208"/></font></td>
</table></td>


</tr>
<tr>
<td width="1286" align="CENTER" bgcolor="#000040" class="s_text"><font  color="#FFFFFF" size="6" face="Arial Narrow">CREDIT SCORING - RECOMMENDATION</font></td><tr>
      </tr>
<table>
<font class="btn" color="#333333" size="2"><strong>
<?php
$username = $_POST['username'];
$password = $_POST['password'];
$host="localhost"; //Database host.
$dbase="corporatescoring"; //Database

$status1 = $_POST['status1'];
$reason1 = $_POST['comment'];
$company_reg_no = $_POST['company_reg_no'];
$mydate= date('Y-m-d');
$mytime=date("H.i");
$application_ref=$_POST['application_ref'];
$loan_number=$_POST['loan_number'];

$connect=mysqli_connect($host,$user,$pass,$dbase); 
if (!$connect) {
  mysqli_close($connect); 
  echo "Cannot connect to the database! Please Check your username and password."; 
}
else {
mysqli_select_db($connect,$dbase)
or die("Couldn't open $dbase: ".mysqli_error($connect));


}

$sql2="replace into approvals values ('$application_ref','$company_reg_no','$loan_number','$status1','$username','$reason1','', NOW())";


$result2=mysqli_query($connect,$sql2);
if(!$result2)
{
 die("UPDATE FAILED: ".mysqli_error());
}
else
{

echo "</BR>"."</BR>"."</BR>";

if ($application_ref !== "") {
	$RegNo_Query = "Select company_reg_no, loan_number from loan_data where application_ref = '$application_ref'";
	$RegNo_QueryResult = mysqli_query($connect,$RegNo_Query);
    if(!$RegNo_QueryResult)
	{
		 die("UPDATE FAILED: ".mysqli_error());
    }
    $row = mysqli_fetch_assoc($RegNo_QueryResult);
	$company_reg_no = $row['company_reg_no'];
	$loan_number    = $row['loan_number'];
}
else{
	$RegNo_Query = "Select application_ref from loan_data where company_reg_no = '$company_reg_no' and loan_number = '$loan_number'";
	$RegNo_QueryResult = mysqli_query($connect,$RegNo_Query);
    if(!$RegNo_QueryResult)
	{
		 die("UPDATE FAILED: ".mysqli_error());
    }
    $row = mysqli_fetch_assoc($RegNo_QueryResult);
	$application_ref = $row['application_ref'];
}	
//echo  "<tr><td>";
?>
<table Style = "font-size:12;font-family:bolder 84% 'trebuchet ms', helvetica, sans-serif;
	                font-weight:bold; line-height:2.5">
   <tr>
      <td>Application Reference Number</td><td>:</td>
	  <td><?php echo $application_ref ?></td>
   <tr>
   <tr></tr>
   <tr>
      <td>Company Registration Number</td><td>:</td>
	  <td><?php echo $company_reg_no ?></td>
   </tr>
  <tr>
      <td>Loan Number</td><td>:</td>
	  <td><?php echo $loan_number ?></td>
   </tr>
    <tr>
      <td>Recommendation</td><td>:</td>
	  <td><?php echo $status1 ?></td>
   </tr>
   <tr>
      <td>Reason</td><td>:</td>
	  <td><?php echo $reason1 ?></td>
   </tr>
   <tr>
      <td>Recommended By</td><td>:</td>
	  <td><?php echo $username ?></td>
   </tr>
   <tr>
      <td>Date and Time:</td><td>:</td>
	  <td><?php echo $mydate."        ".$mytime." HRS" ?></td>
   </tr>
   <tr>
      <td>Preapproval Record</td><td>:</td>
	  <td>UPDATED SUCCESSFULLY</td>
   </tr>
   <tr>
      <td>First Level Approval Signature</td><td>:</td>
	  <td>______________________________</td>
   </tr>
   <tr>
      <td>Second Level Approval Signature</td><td>:</td>
	  <td>______________________________</td>
   </tr>
   <tr style = "line-height:normal">
  	  <td colspan = 3>Notes :____________________________________________________________________________</td>
   </tr>
   <tr style = "line-height:normal">
 	  <td colspan = 3>__________________________________________________________________________________</td>
   </tr >
   <tr style = "line-height:normal">
 	  <td colspan = 3>__________________________________________________________________________________</td>
   </tr>
  <tr style = "line-height:normal">
 	  <td colspan = 3>__________________________________________________________________________________</td>
   </tr>
   <tr style = "line-height:normal">
 	  <td colspan = 3>__________________________________________________________________________________</td>
   </tr>
</table>
      
<?php  
}

?>
</font>
<table width="400" border="0" cellspacing="1" cellpadding="0">
<tr>


<tr>


</tr>
<?php
//$mycount=0;
//while($rows=mysql_fetch_array($result)){
//$mycount++; 
//echo $rows['bbsAccount'];
?>

<?php
///}
?>
<tr>

<tr>

</tr>
<tr>
<td colspan="8" align="center" bgcolor="#FFFFFF"><input name="company_reg_no" type="hidden" id="counts" value="<?php echo $company_reg_no ?>"></td>

</tr>

<?php

mysqli_close($connect);
?>

</table>
</form>
</td>
</tr>
</table>
<br>
<br>
<br>
<br>
</body>
</html>



