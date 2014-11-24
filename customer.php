<!--
	login form: custID and pw
	registration form (if it is the first time accessing the system)
	purchase of  items online: item search table, add to cart, checkout
-->
<html>
<head>
<meta content="text/html;charset=utf-8" http-equiv="Content-Type">
<meta content="utf-8" http-equiv="encoding">

<title>Allegro Music Store</title>
<link href="ams.css" rel="stylesheet" type="text/css">

<!-- Javascript to write errors to a designated area -->
<script>
	function writeMessage(errorString){
		document.getElementById('errorarea').innerHTML += errorString;
	}
</script>
</head>

<body>

<div class="headertablecontainer">
	<img src="allegro.png" class="userlogo">
	<a href="index.html" class="homepagelink">Home</a>
	<a href="customer.php">Customer</a>
	<a href="clerk.php">Clerk</a>
	<a href="manager.php">Manager</a>
<table class="headertable">
<tr><td><h1>Welcome Customer!</h1></td></tr>
<tr><td><div id='errorarea' class="errorarea"> </div></td></tr>
</table>
</div>

<?php

// USEFUL GENERAL PROCEDURES
function getConnection() {
	return @new mysqli("localhost:3307", "root", "", "ams");

}

// write a message to the designated message area
function writeMessage($msg) {
	$asString = '"'.$msg.'"';
	echo '<script> writeMessage('.$asString.');</script>'; 
}

// ARGS MUST BE PROVIDED! just list the name field for all required fields from a form like checkRequiredFields('a','b','c')
// if any required fields are not present it writes an error.
function checkRequiredFields() { 
	// vals is an array containing all arguments to checkRequiredFields(...)
	$vals = func_get_args();
	$missingVals = '';
	// add field names that are missing to the string
	foreach($vals as $requiredval){
		if (empty($_POST[$requiredval])) {
			$missingVals .= $requiredval.', ';
		}
	}

	// if there are missing vals then print an error message.
	if (! empty($missingVals)){
		$errorString = 'You forgot to enter these values: '.$missingVals;
		writeMessage($errorString);
		exit();
	}
}
	
	
	
// DEAL WITH UI REQUESTS HERE (like what do when user clicks a button to submit info)
if ($_SERVER["REQUEST_METHOD"] == "POST") {


}
   
// FUNCTIONS THAT DEAL WITH DB REQUESTS


?>


<!--
 USER CONTROLS ARE HERE
-->
<table>
<tr>
<!-- new customer -->
<td>
<h2>Customer Registration:</h2>
<form id="reg" name="reg" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
    <table border=0 cellpadding=0 cellspacing=0>
        <tr><td>Name:</td><td><input type="text" size=30 name="new_name"></td></tr>
        <tr><td>Address:</td><td><input type="text" size=30 name="new_address"></td></tr>
        <tr><td>Phone #:</td><td> <input type="text" size=30 name="new_phone"></td></tr>
        <tr><td>Login ID:</td><td> <input type="text" size=30 name="new_cid"></td></tr>
        <tr><td>Password:</td><td> <input type="text" size=30 name="new_password"></td></tr>
        <tr><td></td><td><input type="register" name="submit_customer" border=0 value="Register"></td></tr>
    </table>
</form>
</td>

<!-- existing customer -->
<td>
<h2>Login:</h2>
<form id="login" name="login" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
    <table border=0 cellpadding=0 cellspacing=0>
        <tr><td>Login ID:</td><td><input type="text" size=30 name="cid"></td></tr>
        <tr><td>Password:</td><td><input type="text" size=30 name="password"></td></tr>
        <tr><td></td><td><input type="submit" name="submit_login" border=0 value="Login"></td></tr>
    </table>
</form>
</td>

<td>
<h2>Search Items:</h2>
<form id="search" name="search" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
    <table border=0 cellpadding=0 cellspacing=0>
        <tr><td>Category:</td><td><input type="text" size=30 name="category"></td></tr>
        <tr><td>Title:</td><td><input type="text" size=30 name="title"></td></tr>
        <tr><td>Lead Singer:</td><td><input type="text" size=30 name="leadsinger"></td></tr>
        <tr><td>Quantity:</td><td><input type="text" size=30 name="new_quantity"></td></tr>
        <tr><td></td><td><input type="submit" name="submit_search" border=0 value="Find Items"></td></tr>
    </table>
</form>
</td>

</tr>
</table>
</body>
</html>
