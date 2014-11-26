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
<tr><td>
<?php 
if (isset($_SESSION['currentName'])) {
	echo '<h1>Welcome '.$_SESSION['currentName'].'</h1>';
} else {
	echo '<h1>Welcome Customer</h1>';
}
?>
</td></tr>
<tr><td><div id='errorarea' class="errorarea"> </div></td></tr>
</table>
</div>

<?php

$currentCid = "";
$currentName = "";

	 define('sqlUsername', "root");
     define('sqlPassword', "");
     define('sqlServerName', "practice");
     define('DB_HOST', '127.0.0.1:3306'); 

// USEFUL GENERAL PROCEDURES
function getConnection() {

	// return @new mysqli(DB_HOST, sqlUsername, sqlPassword, sqlServerName);
	//return @new mysqli("localhost:3307", "root", "", "ams");
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
	if (isset($_POST["submit_add"]) && $_POST["submit_add"] == "Add") {
		// call function that adds items to basket
	   addToBasket($_POST["formname"]);
	}

	if (isset($_POST["view_basket"]) && $_POST["view_basket"] == "View Basket") {
		// call function that shows items in basket
	   viewBasket();
	}

	if (isset($_POST["empty_basket"]) && $_POST["empty_basket"] == "Empty Basket") {
		// call function that shows items in basket
	   emptyBasket();
	}
	
	if (isset($_POST["submit_payment"]) && $_POST["submit_payment"] == "Complete Purchase") {
		// call function that completes the purchase and provides delivery estimate
	   completePurchase();
	}
	
	if (isset($_POST["logout"]) && $_POST["logout"] == "Log Out") {
		// call function that shows items in basket
	   userLogout();
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

function userLogout(){
	unset($_SESSION['currentName']);
	unset($_SESSION['currentCid']);
	writeMessage("You've Been Logged Off");
}


function addUser(){

	// get the values user entered in the form
	$customer_name = $_POST['new_name'];
	$address = $_POST['new_address'];
	$phone = $_POST['new_phone'];
	$cid = $_POST['new_cid'];
	$pword = $_POST['new_password'];
	
	checkRequiredFields('new_name','new_cid','new_password');


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

	$stmt = $connection->query($sqlQuery);

	// tell user what happened
	if($connection->error) {       
		writeMessage("Error when searching for items: $connection->error");
	} 

			
	// now write each row from result as a row in the html table
	if ($stmt->num_rows == 0){
		// if there's no such items just write an error
		writeMessage("No inventory matching the search results, please try again!");
		return;
	}
	if ($stmt->num_rows == 1){
		writeMessage("Only one item matching. ");

		$row = $stmt->fetch_assoc();

		if ( (empty($_POST['search_quantity'])) || ($row['stock'] < $s_quantity)){
		
		// set up the table
		echo "<table><tr><td class=reporttitle colspan=10>Search Results</td></tr>
			<tr >
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


		writeMessage("Invalid quantity. Input a new quantity and click add. ");
		$i = 1;
		echo "<tr>";
		echo "<td>".$i."</td>";
		echo "<td>".$row['upc']."</td>";
		echo "<td>".$row['title']."</td>";
		echo "<td>".$row['item_type']."</td>";
		echo "<td>".$row['category']."</td>";
		echo "<td>".$row['company']."</td>";
		echo "<td>".$row['release_year']."</td>";
		echo "<td>$".$row['price']."</td>";
		echo "<td>".$row['stock']."</td>";	
		echo "<form id='".$row['upc']."' name='add' method='post' action='".$_SERVER['PHP_SELF'] ."'>
			<td><input type='text' size=5 name='quantity_wanted'></td>
			<td><input type='submit' name='submit_add' border=0 value='Add'></td>
			<td><input type='hidden' form='".$row['upc']."' name='formname' value='".$row['upc']."'></td>
			</form>";
		echo "</tr>";
		echo "</table>";
		}
		else{
			$i = 0;

			// find empty space in array
			while(!empty($_SESSION['basket'][$i])){
				$i += 1;
			}

			$_SESSION['basket'][$i] = array('upc' => $row['upc'], 'quantity' => $s_quantity); 

			// check if item was added to basket
			if($_SESSION['basket'][$i] == array('upc' => $row['upc'], 'quantity' => $s_quantity)){
			writeMessage("Item Successfully Added to basket. ");
			}

			viewBasket();
		}
	} 
	else {
		// More than one search result print the table
		echo "<table><tr><td class=reporttitle colspan=10>Search Results</td></tr>
			<tr >
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
			echo "<td>$".$row['price']."</td>";
			echo "<td>".$row['stock']."</td>";	
			echo "<form id='".$row['upc']."' name='add' method='post' action='".$_SERVER['PHP_SELF'] ."'>
				<td><input type='text' size=5 name='quantity_wanted'></td>
				<td><input type='submit' name='submit_add' border=0 value='Add'></td>
				<td><input type='hidden' form='".$row['upc']."' name='formname' value='".$row['upc']."'></td>
				</form>";
			echo "</tr>";
			$i += 1;
		}
		echo "</table>";
	}

	// Close the connection to the database once we're done with it.
    mysqli_close($connection);
   
}

function addToBasket($input){
	$i = 0;
	
	// get the values user entered in the form
	$quantity_wanted = $_POST['quantity_wanted'];
	
	if(empty($_POST['quantity_wanted'])){
		writeMessage("You must specify a quantity for this item.");
		return;
	}
	
	if($quantity_wanted == 0){
		writeMessage("You must specify a quantity for this item.");
		return;
	}

	// find empty space in array
	while(!empty($_SESSION['basket'][$i])){
	$i += 1;
	}

	$_SESSION['basket'][$i] = array('upc' => $input, 'quantity' => $quantity_wanted); 

	// check if item was added to basket
	if($_SESSION['basket'][$i] == array('upc' => $input, 'quantity' => $quantity_wanted)){
	writeMessage("Item Successfully Added. ");
	}
}

function viewBasket(){

	// need to get data, so get a connection to the DB
    $connection = getConnection();

    if (mysqli_connect_errno()) {
        writeMessage("Could not connect to database");
        exit();
}
	
	
	if($_SESSION['basket']==NULL){
	writeMessage("Your Basket is Currently Empty.");
	return;
	}

	$i = 1;
	// create table to display basket contents
	echo "<table><tr><td class=reporttitle colspan=9>Basket Contents</td></tr>
			<tr >
			<td class=rowheader>#</td>
			<td class=rowheader>UPC</td>
			<td class=rowheader>Title</td>
			<td class=rowheader>Price</td>
			<td class=rowheader>Quantity</td>
			<td class=rowheader>Total Cost</td>
			</tr>";
	
	if(empty($_SESSION['basket'])){
	writeMessage("Your Basket is Currently Empty.");
	return;
	}

	// gets missing info for items in basket, adds them to table
	foreach($_SESSION['basket'] as $item){
	$quantity_want = $item['quantity'];
	$item_upc = $item['upc'];

	$upc_query = $connection->prepare("SELECT title,price FROM item WHERE upc=?");
	$upc_query->bind_param('s',$item_upc);
   	 $upc_query->execute();
   	 $upc_query->store_result();
   	 $upc_query->bind_result($item_title,$item_price);

	while($row = $upc_query->fetch()){
			echo "<tr>";
			echo "<td>".$i."</td>";
			echo "<td>".$item_upc."</td>";
			echo "<td>".$item_title."</td>";
			echo "<td>$".$item_price."</td>";
			echo "<td>".$quantity_want."</td>";
			echo "<td>$".$quantity_want * $item_price."</td>";
			echo "</tr>";

			$i += 1;
	}
}
	echo "</table>";

}

// emptys the basket
function emptyBasket(){
	$_SESSION['basket'] = null;
	writeMessage("Basket has been emptied.");
}

// completes a purchase and updates the stock quantities for purchased items
function completePurchase(){
	// need to get data, so get a connection to the DB
    $connection = getConnection();

    if (mysqli_connect_errno()) {
        writeMessage("Could not connect to database");
        exit();
    }
	
	// if empty basket, just give a message
	if(empty($_SESSION['basket'])){
	writeMessage("Your Basket is Currently Empty.");
	return;
	}
	
	if(empty($_SESSION['currentCid'])){
	writeMessage("You Must Log In Before Completing a Purchase.");
	return;
	}

	// must at least enter a receiptid and expected delivery date so check that values were entered
	checkRequiredFields('cardnum','expirydate'); // these are values of the name field from the form
	
	// get the values user entered in the form
	$creditcard = $_POST['cardnum'];
	$expiry = $_POST['expirydate'];
	
	// check the date format
	if (!preg_match("/\d{4}-{1}\d{2}-{1}\d{2}/", $expiry)){
		writeMessage("Expiry date must have only #'s in YYYY-MM-DD format");
		exit();
	}
	
	// get the expected delivery date
	$expecteddate = estimateDeliveryDate();
	echo $expecteddate;
	
	// the rest will be a transaction: if one of these fails we want to rollback all the changes
	$connection->autocommit(FALSE);
		
		foreach($_SESSION['basket'] as $item){
			$purchase_quantity = $item['quantity'];
			$purchase_upc = $item['upc'];
			// create order
			// recieptId is automatically generated since it's set to auto_increment in table.sql, just pass in null
			$order = $connection->prepare( 'INSERT INTO orders
											 VALUES (NULL, CURDATE(), ?, ?, ?, ?, NULL);');
			$order->bind_param("siss",$_SESSION['currentCid'], $creditcard, $expiry, $expecteddate);
			$order->execute();
			
			// create purchaseitem
			$new_receiptId = $order->insert_id; // get the auto-generated receiptId from the last query
			$purchaseitem = $connection->prepare( 'INSERT INTO purchaseitem
												 VALUES (?, ?, ?)');
			$purchaseitem->bind_param("iii",$new_receiptId, $purchase_upc, $purchase_quantity);
			$purchaseitem->execute();
			
			// update stock for the purchased item
			$updatestock = $connection->prepare( 'UPDATE item
												  SET stock = (stock-?)
												  WHERE upc=?;');
			$updatestock->bind_param("ii",$purchase_quantity, $purchase_upc);
			$updatestock->execute();
		}
		
		// if any of the queries failed then rollback otherwise commit the changes
		if ($order->error || $purchaseitem->error || $updatestock->error) {
			writeMessage("There was an error while completing purchase: ".$order->error.$purchaseitem->error.$updatestock->error);
			$connection->rollback();
			mysqli_close($connection);
			exit();
			
		} else {
			$connection->commit();
		}
	
	// Close the connection to the database once we're done with it.
    mysqli_close($connection);
	// tell the user it was successful if we reach here
	 writeMessage("Successfully purchased, your expected delivery date is ".$expecteddate);
	
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
        <tr><td></td><td><input type="submit" name="submit_login" border=0 value="Login"><input type="submit" onClick="window.location.href=window.location.href" name="logout" border=0 value="Log Out"></td></tr>
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

<form id="basket" name="basket" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
<td><input type="submit" name="view_basket" border=0 value="View Basket"></td>
</form>

<form id="empty_basket" name="empty_basket" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
<td><input type="submit" name="empty_basket" border=0 value="Empty Basket"></td>
</form>
</tr>
<tr>
<td>
<h2>Credit Card Info:</h2>
<form id="pay" name="pay" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
    <table border=0 cellpadding=0 cellspacing=0>
        <tr><td>Credit Card #:</td><td><input type="text" size=30 name="cardnum"></td></tr>
        <tr><td>Expiry Date:</td><td><input type="text" size=30 name="expirydate" value="YYYY-MM-DD"></td></tr>
        <tr><td></td><td><input type="submit" name="submit_payment" border=0 value="Complete Purchase"></td></tr>
    </table>
</form>
</td>
</tr>
</table>
</body>
</html>
