<?php
$server_name="localhost";
$username="root";
$password="";
$database_name="database1";
$conn=mysqli_connect($server_name,$username,$password,$database_name);
if(!$conn)
{
die("Connection Failed:" . mysqli_connect_error());
}

//code for submit button in admin login 
/*if(isset($_POST['submit']))
{
$name = $_POST['name'];
$aadhar = $_POST['aadhar'];
$mobile = $_POST['mobile'];
$email= $_POST['email'];
$loanreq = $_POST['loanreq'];
$sql_query = "INSERT INTO customerdetails (name,aadhar,mobile,email,loanreq) VALUES ('$name','$aadhar','$mobile','$email','$loanreq')";
if (mysqli_query($conn, $sql_query))
{
header("Location:lender.html");
}

	 else
     {
		echo "Error: " . $sql . "" . mysqli_error($conn);
	 }
	 mysqli_close($conn);
}*/
?>






