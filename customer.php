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
<tr><td><h1>Welcome Customer</h1></td></tr>
<tr><td><div id='errorarea' class="errorarea"> </div></td></tr>
</table>
</div>

<?php

$currentCid = "";
$currentName = "";

// USEFUL GENERAL PROCEDURES
function getConnection() {
	return @new mysqli("localhost:3306", "root", "Frellingfahrbot!", "practice");

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

//	User clicked the "Login" button
	if (isset($_POST["submit_login"]) && $_POST["submit_login"] == "Login") {
		// call function that gets and writes the results.
       customerLogin();
      }


}
   
// FUNCTIONS THAT DEAL WITH DB REQUESTS
function customerLogin(){
		
	// get the values user entered in the form
	$currentName = "";
	$cid = $_POST['cid'];
	$password = $_POST['password'];
	
	// open a connection
	$connection = getConnection();
	
	// Check if connection failed
	if (mysqli_connect_errno()) {
        writeMessage("Could not connect to database");
        exit();
    }
	
	if (!$result = $connection->query("SELECT customer_name FROM customer WHERE cid=\"$cid\" AND pword=\"$password\"")) {
        writeMessage("The Query to Find This Customer Has Failed.");
		return;
	}
	
	if ($result->num_rows == 0){
		writeMessage("You Have Not Registered With Us Before. Please Register First And Then Trying Logging In.");
		return;
	}

	
	while($currentCustomer = $result->fetch_assoc()){
	$GLOBALS['currentName'] = $currentCustomer['customer_name']; // Sets a global variable currentName to the name of the customer
	$currentName = $currentCustomer['customer_name'];
	}
	$GLOBALS['currentCid'] = $cid; // Sets a global variable currentCid to the cid of the current customer
	writeMessage("Welcome $currentName");

}

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
        <tr><td></td><td><input type="submit" name="submit_customer" border=0 value="Register"></td></tr>
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
