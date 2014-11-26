<!--
	login form: custID and pw
	registration form (if it is the first time accessing the system)
	purchase of  items online: item search table, add to cart, checkout
-->
<?php
session_start(); // Used for keeping the current customer id and name after the page refreshes
?>
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
	define('sqlUsername', "root");
     define('sqlPassword', "root");
     define('sqlServerName', "project1");
     define('DB_HOST', '127.0.0.1'); 
	return @new mysqli(DB_HOST, sqlUsername, sqlPassword, sqlServerName);

}

// Uses the today's date and the number of unfilled orders to estimate when a package, ordered today, will be delivered.
function estimateDeliveryDate(){

	// open a connection
	$connection = getConnection();
	
	// Check if connection failed
	if (mysqli_connect_errno()) {
        writeMessage("Could not connect to database");
        exit();
    }
	
	if (!$result = $connection->query("SELECT receiptId FROM orders WHERE deliveredDate IS NULL;")) {
        writeMessage("The Query Has Failed.");
		return;
	}
	
	// Assumes that we can deliver 5 packages every day
	// Rounds up to nearest int
	$numDaysTillDelivery = ($result->num_rows);
	$numDaysTillDelivery = ceil($numDaysTillDelivery);
	$numDaysTillDelivery = intval($numDaysTillDelivery, 10);
	
	$numDaysTillDelivery = (string)$numDaysTillDelivery;
	$dateInterval = date_interval_create_from_date_string($numDaysTillDelivery . " days");
	
	date_default_timezone_set("America/Vancouver");
	$deliveryDate = date_create(date("Y-m-d"));
	$date = date_add($deliveryDate,$dateInterval);
	$deliveryDate = date_format($date,"Y-m-d");
	
	// Close the connection to the database once we're done with it.
    mysqli_close($connection);
	
	return $deliveryDate; // returns the estimated delivery date in YYYY-MM-DD format

	
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
	   //estimateDeliveryDate();
      }


if (isset($_POST["submit_customer"]) && $_POST["submit_customer"] == "Register") {
		// call function that adds a new user
	   addUser();
}

      //	User clicked the "Find Items" Button
	if (isset($_POST["submit_search"]) && $_POST["submit_search"] == "Find Items") {
		// call function that gets and writes the results.
       findItems();

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
	$_SESSION['currentName'] = $currentCustomer['customer_name']; // Sets a global variable currentName to the name of the customer
	$currentName = $currentCustomer['customer_name'];
	}
	$_SESSION['currentCid'] = $cid; // Sets a global variable currentCid to the cid of the current customer
	writeMessage("Welcome $currentName");
	
	// Close the connection to the database once we're done with it.
    mysqli_close($connection);

}


function addUser(){

	// get the values user entered in the form
	$customer_name = $_POST['new_name'];
	$address = $_POST['new_address'];
	$phone = $_POST['new_phone'];
	$cid = $_POST['new_cid'];
	$pword = $_POST['new_password'];
	
	checkRequiredFields('$customer_name','$cid','$pword');


	// open a connection
	$connection = getConnection();
	
	// Check if connection failed
	if (mysqli_connect_errno()) {
        writeMessage("Could not connect to database");
        exit();
   	 }

// Check if username is already taken
	$cid_query = $connection->prepare("SELECT cid FROM customer WHERE cid=?");
	$cid_query->bind_param('s',$cid);
   	 $cid_query->execute();
   	 $cid_query->store_result();
   	 $cid_query->bind_result($cid);

	if(!$cid_query->num_rows==0){ 
	 writeMessage("Username is already taken. Please select a different one.");
    
	}

	 else {
	$stmt = $connection->prepare('INSERT INTO customer 
		(cid,pword,customer_name,address,phone) 
		VALUES (?,?,?,?,?)');

	$stmt->bind_param("sssss", $cid, $pword, $customer_name, $address, $phone);
        
    $stmt->execute();

	if (!$result = $connection->query("SELECT customer_name FROM customer WHERE cid=\"$cid\" AND pword=\"$pword\"")) {
        writeMessage("Adding This Customer Has Failed.");
		return;
	}

	writeMessage("Customer Successfully Added.");
}
	// Close the connection to the database once we're done with it.
    mysqli_close($connection);


}

// FUNCTIONS THAT DEAL WITH DB REQUESTS

function findItems(){
	// need to get data, so get a connection to the DB
    $connection = getConnection();

    if (mysqli_connect_errno()) {
        writeMessage("Could not connect to database");
        exit();
}

	// get the values user entered in the form
	$s_category = $_POST['search_category'];
	$s_title = $_POST['search_title'];
	$s_leadsinger = $_POST['search_leadsinger'];
	$s_quantity = $_POST['search_quantity'];


	$sqlQuery = "SELECT * FROM item";
	
	if ($s_leadsinger != NULL){
		$sqlQuery =$sqlQuery." NATURAL JOIN leadsinger WHERE leadsinger.singer_name =\"$s_leadsinger\" AND";
		writeMessage("Showing results for artist: ".$s_leadsinger.". ");
	}else {
		$sqlQuery = $sqlQuery." WHERE";
	}

	if ($s_category == "Search All"){
		$sqlQuery =$sqlQuery." category is NOT NULL";
	} else {
		$sqlQuery = $sqlQuery." category = \"$s_category\"";
		writeMessage($sqlQuery);
	}
	
	if ($s_title != NULL){
		$sqlQuery = $sqlQuery." AND title = \"$s_title\"";
	} 
	if ($s_quantity != NULL){
		$sqlQuery = $sqlQuery." AND stock = \"$s_quantity\"";
	} 
	//writeMessage("SQLQUERY: ".$sqlQuery);

	//$stmt = $connection->query("SELECT * FROM item WHERE category = \"$s_category\""); 
	
	$stmt = $connection->query($sqlQuery);

	// tell user what happened
	if($stmt->error) {       
		writeMessage("Error when searching for items:".$stmt->error);
	} 
	// set up the table
	echo "<table><tr><td class=reporttitle colspan=9>Search Results</td></tr>
			<tr>
			<td class=rowheader>#</td>
			<td class=rowheader>UPC</td>
			<td class=rowheader>Title</td>
			<td class=rowheader>Item type</td>
			<td class=rowheader>Category</td>
			<td class=rowheader>Company</td>
			<td class=rowheader>Release year</td>
			<td class=rowheader>Price</td>
			<td class=rowheader>Stock</td>
			<td class=rowheader>Add to Cart</td>
			</tr>";
			
	// now write each row from result as a row in the html table
	if ($stmt->num_rows == 0){
		// if there's no such items just write an error
		writeMessage("No inventory matching the search results, please try again!");
		exit();
	} else {
		$i = 1;
		while($row = $stmt->fetch_assoc()){
			echo "<tr>";
			echo "<td>".$i."</td>";
			echo "<td>".$row['upc']."</td>";
			echo "<td>".$row['title']."</td>";
			echo "<td>".$row['item_type']."</td>";
			echo "<td>".$row['category']."</td>";
			echo "<td>".$row['company']."</td>";
			echo "<td>".$row['release_year']."</td>";
			echo "<td>".$row['price']."</td>";
			echo "<td>".$row['stock']."</td>";
echo "<td><input type='text' size=3 name='quantity_wanted'></td>";
echo "<td><input type='submit' name='submit_add' border=0 value='Add'></td>";
			echo "</tr>";
			$i += 1;
		}
	}
	echo "</table>";
    //Close the connection to the database once we're done with it.

	// Close the connection to the database once we're done with it.
    mysqli_close($connection);
   
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
        <tr><td>Password:</td><td><input type="password" size=30 name="password"></td></tr>
        <tr><td></td><td><input type="submit" name="submit_login" border=0 value="Login"></td></tr>
    </table>
</form>
</td>

<td>
<h2>Search Items:</h2>
<form id="search" name="search" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
    <table border=0 cellpadding=0 cellspacing=0>
        <tr><td>Category:</td><td> <select name="search_category">
        	<option value="Search All">Search All</option>
        	<option value="Rock">Rock</option>
        	<option value="Country">Country</option>
        	<option value="Pop">Pop</option>
        	<option value="Rap">Rap</option>
        	<option value="Classical">Classical</option>
        	<option value="Instrumental">Instrumental</option>
        	<option value="New Age">New Age</option></select></td></tr>
        <tr><td>Title:</td><td><input type="text" size=30 name="search_title"></td></tr>
        <tr><td>Lead Singer:</td><td><input type="text" size=30 name="search_leadsinger"></td></tr>
        <tr><td>Quantity:</td><td><input type="text" size=30 name="search_quantity"></td></tr>
        <tr><td></td><td><input type="submit" name="submit_search" border=0 value="Find Items"></td></tr>
    </table>
</form>
</td>

</tr>
</table>
</body>
</html>
