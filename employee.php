<?php
// global variables for connecting to database
$host = "localhost";
$user = "root";
$pass = "password";
$db = "Employee";

function loginPage() {
	// creates login page
	print "</br>";
   print "<form method=\"POST\">";
   print "<div align=\"left\">";
   print "Username: <input name=\"Username\" type=\"text\" />";
   print "</br>";
   print "Password: <input name=\"Password\" type=\"password\" />";
   print "<input type=\"submit\" name=\"login_submit\" value=\"Login\" />";
   print "</div> ";
   print "</form>";
}
function employeePage() {
// User is not logging out
if (!(isset($_POST['logout_button']))) {
	$name = $_SESSION['username'];
	Print "<div style='text-align:right'>Welcome $name </div>";
	//Separate Form, Logout button would be prioritized for enter after typing into fields.
	print "<form method=\"POST\">";
		print "<div align=\"right\"><input type=\"submit\" name=\"logout_button\" value=\"Logout\" /></div>";
	print "</form>";

	// open connection
	$con = mysqli_connect($GLOBALS['host'], $GLOBALS['user'], $GLOBALS['pass'], $GLOBALS['db']);
	$result = NULL;

	// handle various different buttons
	if (isset($_POST['addButton'])) {
         // Generate a unique employee id
         $EmpID;
         $temp_result;
         do {
            $empID = rand(10000000, 99999999);
            $temp_result = mysqli_query($con, "SELECT * FROM `employees` WHERE `Employee_ID`=" . $empID);
         } while (mysqli_fetch_array($temp_result) == TRUE);

         // Display text boxes
         Print "<h1>Add New Employee</h1>";
         Print "</br><form method=\"POST\"><div align=\"left\">";
         Print "Employee ID: " . $empID;
         Print "<input name=\"id\" type=\"hidden\" value=" . $empID . " /> </br>";
         Print "First Name: <input name=\"first\" type=\"text\" /></br>";
         Print "Last Name: <input name=\"last\" type=\"text\" /> </br>";
         Print "Gender (M/F): <input name=\"gender\" type=\"text\" maxlength=\"1\" size=\"1\" /> </br>";
         Print "Date Started: <input name=\"s_year\" type=\"text\" maxlength=\"4\" size=\"4\" placeholder=\"YYYY\" /> - " . 
                  "<input name=\"s_month\" type=\"text\" maxlength=\"2\" size=\"2\" placeholder=\"MM\" /> - " . 
                  "<input name=\"s_day\" type=\"text\" maxlength=\"2\" size=\"2\" placeholder=\"DD\" /> </br>";
         Print "Date Ended: <input name=\"e_year\" type=\"text\" maxlength=\"4\" size=\"4\" placeholder=\"YYYY\" /> - " . 
                  "<input name=\"e_month\" type=\"text\" maxlength=\"2\" size=\"2\" placeholder=\"MM\" /> - " . 
                  "<input name=\"e_day\" type=\"text\" maxlength=\"2\" size=\"2\" placeholder=\"DD\" /> </br>";
         Print "Social Security Number: <input name=\"ssn1\" type=\"text\" maxlength=\"3\" size=\"3\" placeholder=\"123\" /> - " . 
                  "<input name=\"ssn2\" type=\"text\" maxlength=\"2\" size=\"2\" placeholder=\"45\" /> - " . 
                  "<input name=\"ssn3\" type=\"text\" maxlength=\"4\" size=\"4\" placeholder=\"6789\" /> </br>";
         Print "Bank Number: <input name=\"bank\" type=\"text\" maxlength=\"9\" size=\"9\" /> </br>";
         Print "Address: <input name=\"address\" type=\"text\" /> </br>";
         Print "Phone Number (<input name=\"phone1\" type=\"text\" maxlength=\"3\" size=\"3\" placeholder=\"000\" />) " . 
                  "<input name=\"phone2\" type=\"text\" maxlength=\"3\" size=\"3\" placeholder=\"123\" /> - " . 
                  "<input name=\"phone3\" type=\"text\" maxlength=\"4\" size=\"4\" placeholder=\"4567\" /> </br>";
         Print "<input type=\"submit\" name=\"createSubmit\" value=\"Submit\" />";
         Print "<input type=\"submit\" name=\"cancel\" value=\"Cancel\" /></div></form>";
         Print "<hr></br>";
   }
   // Creating new employee on database
   elseif (isset($_POST['createSubmit'])) {
      // format date, ssn and phone number for insertion
      $start = $_POST['s_year'] . "-" . $_POST['s_month'] . "-" . $_POST['s_day'];
      $end = $_POST['e_year'] . "-" . $_POST['e_month'] . "-" . $_POST['e_day'];
      $ssn = $_POST['ssn1'] . "-" . $_POST['ssn2'] . "-" . $_POST['ssn3'];
      $phone = "(" . $_POST['phone1'] . ") " . $_POST['phone2'] . "-" . $_POST['phone3'];

      // generate username and password from name
      $userpass = $_POST['last'] . $_POST['first'][0];

      $query1 = "INSERT INTO `employees`(`Employee_ID`, `First_Name`, `Last_Name`, `Gender`, ";
      $query1 .= "`Date_Started_Employment`, `Date_Left_Employment`) VALUES ('".$_POST['id']."', '".$_POST['first']."', '".$_POST['last'];
      $query1 .= "', '".$_POST['gender']."', '".$start."', '".$end."')";
      $query2 = "INSERT INTO `employee_private`(`Employee_ID`, `Employee_Username`, `Employee_Password`, ";
      $query2 .= "`Employee_SSN`, `Employee_Bank`, `Employee_Address`, `Employee_Phone`) VALUES ";
      $query2 .= "('".$_POST['id']."', '".$userpass."', '".$userpass."', '".$ssn."', '".$_POST['bank'] ;
      $query2 .= "', '".$_POST['address']."', '".$phone."')";
      if (mysqli_query($con, $query1) && mysqli_query($con, $query2)) {
         Print "Successfully Created Information. </BR>";
      }
      else {
         Print mysqli_error($con) . "</BR>";
         Print "Error! Unsuccessful Creation.</BR>";
      }
   }
   // Edit button pressed on a row, create and fills text boxes
   elseif ((isset($_POST['editButton']))) {
      $result1 = mysqli_query($con, "Select * FROM `employees` WHERE `Employee_ID`='" . $_POST['editButton'] . "'");
      $result2 = mysqli_query($con, "Select * FROM `employee_private` WHERE `Employee_ID`='" . $_POST['editButton'] . "'");
      if (($info1 = mysqli_fetch_array($result1)) && ($info2 = mysqli_fetch_array($result2))) {
         // prepare dates, ssn and phone numbers for multiple html text boxes
         $start = $info1['Date_Started_Employment'];
         $s_year = substr($start, 0, 4);
         $s_month = substr($start, 5, 2);
         $s_day = substr($start, 8, 2);
         $end = $info1['Date_Left_Employment'];
         $e_year = substr($end, 0, 4);
         $e_month = substr($end, 5, 2);
         $e_day = substr($end, 8, 2);
         $ssn = $info2['Employee_SSN'];
         $ssn1 = substr($ssn, 0, 3);
         $ssn2 = substr($ssn, 4, 2);
         $ssn3 = substr($ssn, 7, 4);
         $phone = $info2['Employee_Phone'];
         $phone1 = substr($phone, 1, 3);
         $phone2 = substr($phone, 6, 3);
         $phone3 = substr($phone, 10, 4);

         Print "<h1>Edit Employee</h1>";
         Print "</br><form method=\"POST\"><div align=\"left\">";
         Print "Employee ID: " . $info1['Employee_ID'];
         Print "<input name=\"id\" type=\"hidden\" value=" . $info1['Employee_ID'] . " /> </br>";
         Print "First Name: <input name=\"first\" type=\"text\" value=\"" . $info1['First_Name'] . "\" /> </br>";
         Print "Last Name: <input name=\"last\" type=\"text\" value=\"" . $info1['Last_Name'] . "\" /> </br>";
         Print "Gender (M/F): <input name=\"gender\" type=\"text\" maxlength=\"1\" size=\"1\" value=\"" . $info1['Gender'] . "\" /> </br>";
         Print "Date Started: <input name=\"s_year\" type=\"text\" maxlength=\"4\" size=\"4\" placeholder=\"YYYY\" value=\"" . $s_year . "\" /> - " . 
                  "<input name=\"s_month\" type=\"text\" maxlength=\"2\" size=\"2\" placeholder=\"MM\" value=\"" . $s_month . "\" /> - " . 
                  "<input name=\"s_day\" type=\"text\" maxlength=\"2\" size=\"2\" placeholder=\"DD\" value=\"" . $s_day . "\" /> </br>";
         Print "Date Ended: <input name=\"e_year\" type=\"text\" maxlength=\"4\" size=\"4\" placeholder=\"YYYY\" value=\"" . $e_year . "\" /> - " . 
                  "<input name=\"e_month\" type=\"text\" maxlength=\"2\" size=\"2\" placeholder=\"MM\" value=\"" . $e_month . "\" /> - " . 
                  "<input name=\"e_day\" type=\"text\" maxlength=\"2\" size=\"2\" placeholder=\"DD\" value=\"" . $e_day . "\" /> </br>";
         Print "Social Security Number: <input name=\"ssn1\" type=\"text\" maxlength=\"3\" size=\"3\" placeholder=\"123\" value=\"" . $ssn1 . "\" /> - " . 
                  "<input name=\"ssn2\" type=\"text\" maxlength=\"2\" size=\"2\" placeholder=\"45\" value=\"" . $ssn2 . "\" /> - " . 
                  "<input name=\"ssn3\" type=\"text\" maxlength=\"4\" size=\"4\" placeholder=\"6789\" value=\"" . $ssn3 . "\" /> </br>";
         Print "Bank Number: <input name=\"bank\" type=\"text\" maxlength=\"9\" size=\"9\" value=\"" . $info2['Employee_Bank'] . "\" /> </br>";
         Print "Address: <input name=\"address\" type=\"text\" value=\"" . $info2['Employee_Address'] . "\" /> </br>";
         Print "Phone Number (<input name=\"phone1\" type=\"text\" maxlength=\"3\" size=\"3\" placeholder=\"000\" value=\"" . $phone1 . "\" />) " . 
                  "<input name=\"phone2\" type=\"text\" maxlength=\"3\" size=\"3\" placeholder=\"123\" value=\"" . $phone2 . "\" /> - " . 
                  "<input name=\"phone3\" type=\"text\" maxlength=\"4\" size=\"4\" placeholder=\"4567\" value=\"" . $phone3 . "\" /> </br>";
         Print "<input type=\"submit\" name=\"editSubmit\" value=\"Submit\" />";
         Print "<input type=\"submit\" name=\"cancel\" value=\"Cancel\" /></div></form>";
         Print "<hr></br>";
      }
   }
   // Submiting any edits to database.
   elseif (isset($_POST['editSubmit'])) {
      // prepare dates, ssn and phone number
      $start = $_POST['s_year'] . "-" . $_POST['s_month'] . "-" . $_POST['s_day'];
      $end = $_POST['e_year'] . "-" . $_POST['e_month'] . "-" . $_POST['e_day'];
      $ssn = $_POST['ssn1'] . "-" . $_POST['ssn2'] . "-" . $_POST['ssn3'];
      $phone = "(" . $_POST['phone1'] . ") " . $_POST['phone2'] . "-" . $_POST['phone3'];

      //prepare update query for employees table
      $query1 = "UPDATE `employees` SET `First_Name`='".$_POST['first']."',`Last_Name`='".$_POST['last']."'";
      $query1 .= ",`Gender`='".$_POST['gender']."',`Date_Started_Employment`='".$start."'";
      $query1 .= ",`Date_Left_Employment`='".$end."' WHERE `Employee_ID`='". $_POST['id'] ."'";

      //prepare update query for employee_private table
      $query2 = "UPDATE `employee_private` SET `Employee_SSN`='".$ssn."',`Employee_Bank`='".$_POST['bank']."',";
      $query2 .= "`Employee_Address`='".$_POST['address']."',`Employee_Phone`='".$phone."' WHERE `Employee_ID`='".$_POST['id']."'";
      
      if ((mysqli_query($con, $query1)) && (mysqli_query($con, $query2))) {
         Print "Successfully Edited Information. </BR>";
      }
      else {
         Print mysqli_error($con) . "</BR>";
         Print "Error! Unsuccessful Edit.</BR>";
      }
   }
   // Cancel should do nothing, this elseif isn't necessary.
   elseif (isset($_POST['cancel'])) {
      ;
   }
   // Delete button pressed on a row, remove row from DB
   elseif ((isset($_POST['deleteButton']))) {
      if (mysqli_query($con, "DELETE FROM employees WHERE `Employee_ID`='" . $_POST['deleteButton'] . "'") == TRUE) {
         Print "Employee with ID " . $_POST['deleteButton'] . " has been removed. <BR>";
      }
   }
	
   // Create search bar and drop down for category of search
	Print "<h1>Employee Database</h1>";
	print "<form method=\"POST\">";
	print "Search By: </br>";
	print "<select name=\"formChoice\"> ";
	print "<option value=\"\">Select</option>";
	print "<option value=\"Employee_ID\">ID</option>";
	print "<option value=\"First_Name\">First Name</option>";
	print "<option value=\"Last_Name\">Last Name</option>";
	print "<option value=\"Gender\">Gender</option>";
	print "<option value=\"Date_Started_Employment\">Start Date</option>";
	print "<option value=\"Date_Left_Employment\">Left Date</option></select>";
	print "<input type=\"text\" name=\"Value\">";
	print "<input type=\"submit\" name=\"query_submit\" value=\"Submit\" default/>";
	print "<input type=\"submit\" name=\"query_submit\" value=\"Reset\" default/>";
	print "</form>";
	Print "<div align=\"left\"><form method=\"POST\"><input type=\"submit\" name=\"addButton\" value=\"Add A New Employee\"></form></div>";
	
	// Logout and Reset button not pressed and form dropdown is not 'select' on employee page
	if ((isset($_POST['query_submit'])) && ($_POST['query_submit'] != "Reset") && 
			(isset($_POST['formChoice'])) && ($_POST['formChoice'] != "") && (isset($_POST['Value']))) {
		$formChoice = $_POST['formChoice'];
		$value = $_POST['Value'];  
		Print "SELECT * FROM employees WHERE $formChoice='$value'";
		$result = mysqli_query($con, "SELECT * FROM employees WHERE $formChoice='$value'");
		printResult($result);
	}
	else {
		Print "SELECT * FROM Employees<BR>";
		$result = mysqli_query($con, "(SELECT * FROM employees)");
		printResult($result);
	}
//closing the connection
mysqli_close($con);
}
else {
	session_unset();
	session_destroy(); 
	Print "You have been logged out.";
	loginPage();
}
}

function printResult($result) {
	// print query into table 
	Print "<table>";
	Print "<tr><td>Employee ID</td>";
	Print "<td>First Name</td>";
	Print "<td>Last Name</td>";
	Print "<td>Gender</td>";
	Print "<td>Date Started</td>";
	Print "<td>Date Ended</td></tr>";
	while($info = mysqli_fetch_array($result)) {  
		Print "<tr>"; 
      Print "<td>".$info['Employee_ID'] . "</td> "; 
      Print "<td>".$info['First_Name'] . "</td> ";
      Print "<td>".$info['Last_Name'] . "</td> ";
      Print "<td>".$info['Gender'] . "</td>"; 
      Print "<td>".$info['Date_Started_Employment'] . "</td> "; 
      Print "<td>".$info['Date_Left_Employment'] . "</td>";
      Print "<form method=\"POST\"><td><button type=\"submit\" name=\"editButton\" value=\"".$info['Employee_ID']."\">Edit</button></td></div></form>";
      Print "<form method=\"POST\"><td><button type=\"submit\" name=\"deleteButton\" value=\"".$info['Employee_ID']."\">Delete</button></td></tr></div></form>";
	} 
	Print "</tr></table>";
}

function checkInput($username, $password) {
	$con = mysqli_connect($GLOBALS['host'], $GLOBALS['user'], $GLOBALS['pass'], $GLOBALS['db']);
	$result = mysqli_query($con, "(SELECT * FROM employee_private WHERE Employee_Username='$username' AND BINARY Employee_Password='$password')");
	mysqli_close($con);
	if (mysqli_num_rows($result) == 0) {
		return false;
	}
	else {
		return true;
	}
}
?>

<?php
// "main" function
session_start();

// if statement for blank login informaion. later function will clear login information if incorrect
if ((isset($_POST['login_submit'])) && (!(isset($_SESSION['username'])) || !(isset($_SESSION['password'])))) {
	$_SESSION['username'] = $_POST['Username'];
	$_SESSION['password'] = $_POST['Password'];
}
// attempt login if login information is present
if ((isset($_SESSION['username'])) && (isset($_SESSION['password']))) {
	// checkInput() will verify username and password
	if (checkInput($_SESSION['username'], $_SESSION['password'])) {
		employeePage();
	}
	// Incorrect login information
	else {
		Print "Error.  Incorrect Username or Password";
		unset($_SESSION['username']);
		unset($_SESSION['password']);
		loginPage();
	}
}
// Initial case, User has not logged in yet.
else {
	loginPage();
}
?>
