<HTML>
<HEAD>
<!--
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
-->
<TITLE>CREDIT SCORING - Get Company Registration Number - First Approval </TITLE>

<!--
<link href="btu.css" rel="stylesheet" type="text/css">
-->

<script>
  
  function MainMenu()
  {
	document.GetRegNo.action='index.php';
  }
  function SelectRegNo()
  {
	  document.forms["GetRegNo"]["application_ref"].value = "";
  }	 

  function SelectApplicationRef()
  {
	  document.forms["GetRegNo"]["company_reg_no"].value = "";
	  document.forms["GetRegNo"]["loan_number"].value="";
  }	
  function Validate()
  {	  
    /**Exclude Validate if the user wants to return to main menu. when the return to main menu
	   button is clicked the MainMenu function assigns the form action path to index.php.  This
	   function starts by testing if the form action is index.php so as not to validate
	**/
	
	var FormAction = document.GetRegNo.action;	  
	if (FormAction.includes("index.php")){
	   window.location.assign("index.php");    //jump to the login page
	}
	
	//otherwise validate the inputs
    else{ 
	  var ErrorMessage = "";
	  var is_ALLEmpty = false;
	  var is_application_refEmpty = false;
	  var is_loan_numberEmpty = false;
	  var is_company_reg_noEmpty = false;
	  
	  if (document.GetRegNo.application_ref.value == ""){is_application_refEmpty = true;}
	  if (document.GetRegNo.company_reg_no.value == "") {is_company_reg_noEmpty = true;}
	  if (document.GetRegNo.loan_number.value == "") {is_loan_numberEmpty = true;}
	  
	  if (document.GetRegNo.status1.value == "") {
		  alert ("The approval status cannot be empty");
	      return false;
	  }
	  is_ALLEmpty = is_application_refEmpty && is_loan_numberEmpty && is_company_reg_noEmpty;
	     
	  if (is_ALLEmpty)
	  {
		  ErrorMessage = "Select application ref or company registration number";
	      alert (ErrorMessage);
		  return false;
	  }
      
	  if (is_company_reg_noEmpty && !is_loan_numberEmpty)
	  {
		  ErrorMessage = "You may not enter loan number alone. Please enter the company registration number";
          alert (ErrorMessage);
		  return false;
	  }
	  if (!is_company_reg_noEmpty && is_loan_numberEmpty)
	  {
		  ErrorMessage = "You may not enter company registration alone. Please enter the loan number";
          alert (ErrorMessage);
		  return false;
	  }
	}  
		  
	  
  }
  
</script>


</HEAD>

<BODY BGCOLOR=#FFFFFF >
<!--
<font class="s_text">
-->
<?php
error_reporting(0);



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
//  echo $username;


$pass= "";//edward "sefalana2008";
$host="localhost"; // Host name 
$user="root"; // Mysql username 
$db_name="corporatescoring"; // Database name 
// Connect to server and select databse.
//edward $ip =gethostbyname($_SERVER['REMOTE_ADDR']); 
//echo $ip;


$connect=mysqli_connect($host,$user,$pass,$db_name); 
//echo $pass;
  if (!$connect) {
  mysqli_close($connect,$db_name); 
  echo "Cannot connect to the database! Please Check your username and password."; 
  die();
}
else {

 
}


?>

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
<td width="1286" align="CENTER" bgcolor="#000040" class="s_text"><font  color="#FFFFFF" size="7" face="Arial Narrow">CORPORATE CREDIT SCORING</font></td><tr>
      </tr>
   <table class="s_text" >
        <tr bgcolor="#ECF8FF" class="s_text"> 
          <td>
        <p></br> 
        <form action="summary_results.php" 
		      name="GetRegNo" id = "GetRegNo" method="post" onsubmit = "return Validate()">
            <td><table><tr><td><p align="justify" class="s_text">
<table class="s_text">
<tr>
    <td><strong>SECOND APPROVAL:</td>
</tr>
<tr>
    <td><strong><em><font color="#FF9900" size="1">&nbsp;&nbsp;&nbsp;&nbsp;Enter company's loan identification details below</font></em></td>
</tr>
<tr>
    <td>Unique Loan Application No :</td>
    <td><input name="application_ref" type="text" onclick = "SelectApplicationRef()"></td>
<tr>
<tr><td colspan = 3>_____________________________<strong>OR</strong>________________________;</td></tr>
<tr>
    <td>Certificate Of Registration No :</td>
	<td><input name="company_reg_no" type="text" onclick = "SelectRegNo()" ></td>
</tr>
<tr><td><strong>AND</strong></td></tr>
<tr>
    <td>Company Specific Loan Count No:</td>
    <td>
<SELECT NAME="loan_number" onclick = "SelectRegNo()">
<option value=""> </option>
<option value="1">1 </option>
<option value="2">2</option>
<option value="3">3</option>
<option value="4">4</option>
<option value="5">5</option>
<option value="6">6</option>
<option value="7">7</option>
<option value="8">8</option>
<option value="9">9</option>
<option value="10">10</option>
<option value="11">11</option>
<option value="12">12</option>
<option value="13">13</option>
<option value="14">14</option>
<option value="15">15</option>
<option value="16">16</option>
<option value="17">17</option>
<option value="18">18</option>
<option value="19">19</option>
<option value="20">20</option>
<option value="21">21</option>
<option value="22">22</option>
<option value="23">23</option>
<option value="24">24</option>
<option value="25">25</option>
<option value="26">26</option>
<option value="27">27</option>
<option value="28">28</option>
<option value="29">29</option>
<option value="30">30</option>
<option value="31">31</option>
<option value="32">32</option>
<option value="33">33</option>
<option value="34">34</option>
<option value="35">35</option>
<option value="36">36</option>
<option value="37">37</option>
<option value="38">38</option>
<option value="39">39</option>
<option value="40">40</option>
<option value="41">41</option>
<option value="42">42</option>
<option value="43">43</option>
<option value="44">44</option>
<option value="45">45</option>
<option value="46">46</option>
<option value="47">47</option>
<option value="48">48</option>
<option value="49">49</option>
<option value="50">50</option>
<option value="51">51</option>
<option value="52">52</option>
<option value="53">53</option>
<option value="54">54</option>
<option value="55">55</option>
<option value="56">56</option>
<option value="57">57</option>
<option value="58">58</option>
<option value="59">59</option>
<option value="60">60</option>
</SELECT></td>
</tr>
<tr><td colspan = 4>_________________________________________________________</td></tr>

<tr> 
<td align="right">
<input type="hidden" name="username"        value="<?php echo $_POST['username'] ?>"/>
<input type="hidden" name="password"        value="<?php echo $_POST['password'] ?>"/>
<button type="submit" name="submit">Submit</button>
<button type = "text" name="mainmenu" onclick="MainMenu()">Return To Main Menu</button>
</tr>
<script>
   document.GetRegNo.username.value = localStorage.username;
</script>
</form>


`
</td></tr>
</table>
</td>
</tr>    
 </table> 
</table>



</table>
</font>
</BODY>
</HTML>
