<?php
function loginPage() {
?>
   </br>
   <form method="POST">
   <div align="left">
   Username: <input name="Username" type="text" />
   </br>
   Password: <input name="Password" type="password" />
   <input name="form_submit" type="submit" value="Submit" />
   </div> 
   </form>
<?php
}
function employeePage() {
// User did not use log out button
if (!(isset($_POST['logout_button']))) {
   $name = $_SESSION['username'];
   Print "<div style='text-align:right'>Welcome $name</div>";
?>
   <form method="POST">
   <div align="right"><input type="submit" name="logout_button" value="Logout" /></div>
   </form>

   <form method="POST">
   Search By: 
   <select name="formChoice"> 
   <option value="">Select</option> 
   <option value="Employee_ID">ID</option> 
   <option value="First_Name">First Name</option> 
   <option value="Last_Name">Last Name</option> 
   <option value="Gender">Gender</option> 
   <option value="Date_Started_Employment">Start Date</option> 
   <option value="Date_Left_Employment">Left Date</option> 
   </select>
   <input type="text" name="Value">
   <input type="submit" name="query_submit" value="Submit" default/>
   </form>

<?php
   // open connection
   $con = mysqli_connect("localhost", "root", "password", "employee");

   $formChoice = $_POST['formChoice'];
   $value = $_POST['Value'];
   $result = NULL;
   // Logout button pressed on employee page
   if ((isset($_POST['query_submit'])) && (isset($_POST['Value']))) {
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
   while($info = mysqli_fetch_array( $result)) {  
      Print "<tr>"; 
	   Print "<td>".$info['Employee_ID'] . "</td> "; 
	   Print "<td>".$info['First_Name'] . "</td> ";
	   Print "<td>".$info['Last_Name'] . "</td> ";
	   Print "<td>".$info['Gender'] . " </td>"; 
	   Print "<td>".$info['Date_Started_Employment'] . "</td> "; 
	   Print "<td>".$info['Date_Left_Employment'] . "</td></tr>"; 
   } 
   Print "</tr></table>";
}

function checkInput($username, $password) {
   $con = mysqli_connect("localhost", "root", "password", "employee");
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
session_start();

// if statement for blank login informaion. later function will clear login information if incorrect
if ((!(isset($_SESSION['username']))) || (!(isset($_SESSION['password'])))) {
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