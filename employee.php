<?php 
//connecting to db  mysqli_connect("host", "username", "password", "database_name");
$con = mysqli_connect("localhost", "root", "password", "employee");

// perform query
$result = NULL;
if(isset($_POST['form_submit']) && (isset($_POST['FirstName']) && isset($_POST['LastName']))) {
   echo "SELECT * FROM people WHERE FirstName='$first' OR LastName='$last'<BR>";
    $result = mysqli_query($con, "(SELECT * FROM people WHERE FirstName='$first' OR LastName='$last')");
}
else {
   echo "SELECT * FROM people <BR>";
   $result = mysqli_query($con, "(SELECT * FROM employees)");
}
   
// print table and label
Print "<table>";
Print "<tr><td>Employee ID</td>";
Print "<td>First Name</td>";
Print "<td>Last Name</td>";
Print "<td>Gender</td>";
Print "<td>Date Started</td>";
Print "<td>Date Ended</td></tr>";
// fill table with database entries
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

//closing the connection
mysqli_close($con);
?>