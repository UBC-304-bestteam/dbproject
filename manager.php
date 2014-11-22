<html>
<head>
<meta content="text/html;charset=utf-8" http-equiv="Content-Type">
<meta content="utf-8" http-equiv="encoding">

<title>AMS Manager Interface</title>
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
<tr><td><h1>Manager Interface</h1></td></tr>
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


//	User clicked "Get Incomplete Orders" button so setup what should happen when page is refreshed	
    if (isset($_POST["submit_view_incompleteorders"]) && $_POST["submit_view_incompleteorders"] == "Get Incomplete Orders") {
		// just call function that gets and writes the results.
       showIncompleteOrders();
      }
	  
//	User clicked "Update order" button so setup what should happen when page is refreshed	
    if (isset($_POST["submit_updateorder"]) && $_POST["submit_updateorder"] == "Update order") {
		// just call function that gets and writes the results.
       updateIncompleteOrder();
      }
   
//	User clicked "Get Inventory" button
	if (isset($_POST["submit_viewinventory"]) && $_POST["submit_viewinventory"] == "Get Inventory") {
		// 2. call function that gets and writes the results.
       showInventory();
      }
}
   
// FUNCTIONS THAT DEAL WITH DB REQUESTS

function showIncompleteOrders(){
	// need to get data, so get a connection to the DB
    $connection = getConnection();

    if (mysqli_connect_errno()) {
        writeMessage("Could not connect to database");
        exit();
    }
	
    // make the query needed, it gives an array of rows from resulting query table
    if (!$result = $connection->query("SELECT receiptId, odate, expectedDate, deliveredDate FROM orders WHERE expectedDate is null or deliveredDate is null")) {
        writeMessage("Error making the query...");
		exit();
    }
	// set up the table
	echo "<table><tr>
			<td class=rowheader>#</td>
			<td class=rowheader>Receipt ID</td>
			<td class=rowheader>Order Date</td>
			<td class=rowheader>Expected Delivery Date</td>
			<td class=rowheader>Delivered Date</td>
			</tr>";
	// now write each row from result as a row in the html table
	if ($result->num_rows == 0){
		// if there's no such orders just write an error
		writeMessage("No incomplete orders");
		exit();
	} else {
		while($row = $result->fetch_assoc()){
			$i = 1;
			echo "<tr>";
			echo "<td>".$i."</td>";
			echo "<td>".$row['receiptId']."</td>";
			echo "<td>".$row['odate']."</td>";
			echo "<td>".$row['expectedDate']."</td>";
			echo "<td>".$row['deliveredDate']."</td>";
			echo "</tr>";
			$i += 1;
		}
	}
	echo "</table>";

    // Close the connection to the database once we're done with it.
    mysqli_close($connection);
   }
   
function showInventory(){
	// need to get data, so get a connection to the DB
    $connection = getConnection();

    if (mysqli_connect_errno()) {
        writeMessage("Could not connect to database");
        exit();
    }
	
    // make the query needed, it gives an array of rows from resulting query table
    if (!$result = $connection->query("SELECT * 
										FROM item")) {
        writeMessage("Error making the query...");
		exit();
    }
	// set up the table
	echo "<table><tr>
			<td class=rowheader>#</td>
			<td class=rowheader>UPC</td>
			<td class=rowheader>Title</td>
			<td class=rowheader>Item type</td>
			<td class=rowheader>Category</td>
			<td class=rowheader>Company</td>
			<td class=rowheader>Release year</td>
			<td class=rowheader>Price</td>
			<td class=rowheader>Stock</td>
			</tr>";
	// now write each row from result as a row in the html table
	if ($result->num_rows == 0){
		// if there's no such orders just write an error
		writeMessage("No inventory!");
		exit();
	} else {
		while($row = $result->fetch_assoc()){
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
			echo "</tr>";
			$i += 1;
		}
	}
	echo "</table>";

    // Close the connection to the database once we're done with it.
    mysqli_close($connection);
   }

function updateIncompleteOrder(){
	// receiptid, new_expecteddate, new deliverdate
	// need to get data, so get a connection to the DB
    $connection = getConnection();

    if (mysqli_connect_errno()) {
        writeMessage("Could not connect to database");
        exit();
    }

	// must at least enter a receiptid so check that value was entered
	checkRequiredFields('receiptid','new_expecteddate'); // value of the name field from the form
	
	// get the values user entered in the form
	$receiptid = $_POST['receiptid'];
	$expecteddate = $_POST['new_expecteddate'];
	$delivereddate = $_POST['new_deliverdate'];
	
	// these values might be strings, then the date is updated to empty string equivalent 0000-00-00
	// so make them NULL instead.
	if(empty($expecteddate)){
		$expecteddate = NULL;
	}
	if(empty($delivereddate)){
		$delivereddate = NULL;
	}
	
	// make the query needed, it gives an array of rows from resulting query table
	$stmt = $connection->prepare('UPDATE orders
								  SET expectedDate = ?, deliveredDate = ?
								  WHERE receiptId = ?');
	$stmt->bind_param("ssi",$expecteddate,$delivereddate,$receiptid);
    $stmt->execute();
	
	// make sure a row was actually changed, e.g. 0 rows are changed if receiptId entered does not exist!
	if ($stmt->affected_rows == 0){
		writeMessage("Receipt ID does not exist.");
		exit();
	}
	// tell user what happened
	if($stmt->error) {       
		writeMessage("Error when updating the order:".$stmt->error);
	} else {
         writeMessage("Successfully updated the order");
	}
}
?>


<!--
 USER CONTROLS ARE HERE
-->
<table>
<tr>
<!-- add item form -->
<td>
<h2>Add a New Item:</h2>
<form id="add" name="add" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
    <table border=0 cellpadding=0 cellspacing=0>
        <tr><td>UPC:</td><td><input type="text" size=30 name="new_upc"></td></tr>
        <tr><td>Title:</td><td><input type="text" size=30 name="new_title"></td></tr>
        <tr><td>Type:</td><td> <input type="text" size=30 name="new_type"></td></tr>
        <tr><td>Category:</td><td> <input type="text" size=30 name="new_category"></td></tr>
        <tr><td>Company:</td><td> <input type="text" size=30 name="new_company"></td></tr>
        <tr><td>Year:</td><td> <input type="text" size=30 name="new_year"></td></tr>
        <tr><td>Price:</td><td> <input type="text" size=30 name="new_price"></td></tr>
        <tr><td>Stock:</td><td> <input type="text" size=30 name="new_stock"></td></tr>
        <tr><td></td><td><input type="submit" name="submit_additem" border=0 value="Add Item"></td></tr>
    </table>
</form>
</td>

<!-- top selling items form -->
<td>
<h2>See Top Sellers:</h2>
<form id="top" name="top" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
    <table border=0 cellpadding=0 cellspacing=0>
        <tr><td>Date:</td><td><input type="text" size=30 name="date" value="YYYY-MM-DD"></td></tr>
        <tr><td>How many?:</td><td><input type="text" size=30 name="topnum"></td></tr>
        <tr><td></td><td><input type="submit" name="submit_topsellers" border=0 value="Get Top Sellers"></td></tr>
    </table>
</form>
</td>

<!-- daily sales report -->
<td>
<h2>See Daily Sales Report:</h2>
<form id="daily" name="daily" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
    <table border=0 cellpadding=0 cellspacing=0>
        <tr><td>Date:</td><td><input type="text" size=30 name="date" value="YYYY-MM-DD"></td></tr>
        <tr><td></td><td><input type="submit" name="submit_dailysales" border=0 value="Get Sales Report"></td></tr>
    </table>
</form>
</td>

<!-- view inventory -->
<td>
<h2>View Inventory:</h2>
<form id="view" name="view" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
    <table border=0 cellpadding=0 cellspacing=0>
        <tr><td></td><td><input type="submit" name="submit_viewinventory" border=0 value="Get Inventory"></td></tr>
    </table>
</form>
</td>



<!-- process orders -->
<td>
<h2>Process Orders:</h2>
<form id="view_incompleteorders" name="view_incompleteorders" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
    <table border=0 cellpadding=0 cellspacing=0>
        <tr><td></td><td><input type="submit" name="submit_view_incompleteorders" border=0 value="Get Incomplete Orders"></td></tr>
    </table>
</form>
<form id="updateorder" name="updateorder" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
    <table border=0 cellpadding=0 cellspacing=0>
        <tr><td>Receipt ID:</td><td><input type="text" size=30 name="receiptid"></td></tr>
        <tr><td>Expected delivery date:</td><td><input type="text" size=30 name="new_expecteddate" value="YYYY-MM-DD"></td></tr>
        <tr><td>Delivered date:</td><td><input type="text" size=30 name="new_deliverdate" value="YYYY-MM-DD"></td></tr>
        <tr><td></td><td><input type="submit" name="submit_updateorder" border=0 value="Update order"></td></tr>
    </table>
</form>
</td>

</tr>
</table>



</body>
</html>
