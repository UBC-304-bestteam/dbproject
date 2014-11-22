<!--
	TODO add error viewing area
	Form to process a return of an item for refund 
-->

<html>
<head>
<meta content="text/html;charset=utf-8" http-equiv="Content-Type">
<meta content="utf-8" http-equiv="encoding">

<title>AMS Clerk Interface</title>
<link href="ams.css" rel="stylesheet" type="text/css">

<!-- Javascript to write errors to a designated area -->
<script>
	function writeMessage(errorString){
		document.getElementById('errorarea').innerHTML += errorString;
	}
</script>
</head>

<div class="headertablecontainer">
	<img src="allegro.png" class="userlogo">
	<a href="index.html" class="homepagelink">Home</a>
	<a href="customer.php">Customer</a>
	<a href="clerk.php">Clerk</a>
	<a href="manager.php">Manager</a>
<table class="headertable">
<tr><td><h1>Clerk Interface</h1></td></tr>
<tr><td><div class="errorarea"> </div></td></tr>
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
<!-- add item form -->
<td>
<h2>Process return:</h2>
<form id="add" name="add" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
    <table border=0 cellpadding=0 cellspacing=0>
        <tr><td>Receipt ID:</td><td><input type="text" size=30 name="new_upc"</td></tr>
        <tr><td>Item UPC:</td><td><input type="text" size=30 name="new_title"</td></tr>
        <tr><td>Quantity returned:</td><td> <input type="text" size=30 name="new_type"></td></tr>
        <tr><td></td><td><input type="submit" name="submit_return" border=0 value="Return"></td></tr>
    </table>
</form>
</td>



</tr>
</table>
</body>
</html>
