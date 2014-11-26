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
	 define('sqlUsername', "root");
     define('sqlPassword', "");
     define('sqlServerName', "practice");
     define('DB_HOST', '127.0.0.1:3306'); 

// USEFUL GENERAL PROCEDURES
function getConnection() {

    return @new mysqli(DB_HOST, sqlUsername, sqlPassword, sqlServerName);

	// return @new mysqli("localhost:3307", "root", "", "ams");

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
		// call function that gets and writes the results.
       showInventory();
      }
	  
//	User clicked "Get Sales Report" button
	if (isset($_POST["submit_dailysales"]) && $_POST["submit_dailysales"] == "Get Sales Report") {
		//call function that gets and writes the results.
	   showDailySalesReport();
	  }
	  
//	User clicked "Get Top Sellers" button
	if (isset($_POST["submit_topsellers"]) && $_POST["submit_topsellers"] == "Get Top Sellers") {
		//call function that gets and writes the results.
	   showTopSellers();
	  }

//	User clicked "Add Item" button
	if (isset($_POST["submit_additem"]) && $_POST["submit_additem"] == "Add/Edit Item") {
		//call function that gets and writes the results.
	   addNewItem();
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
	echo "<table><tr><td class=reporttitle colspan=5>Incomplete Orders</td></tr>
			<tr>
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
		$i = 1;
		while($row = $result->fetch_assoc()){
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
	echo "<table><tr><td class=reporttitle colspan=9>Inventory List</td></tr>
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
			</tr>";
	// now write each row from result as a row in the html table
	if ($result->num_rows == 0){
		// if there's no such orders just write an error
		writeMessage("No inventory!");
		exit();
	} else {
		$i = 1;
		while($row = $result->fetch_assoc()){
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
	// need to get data, so get a connection to the DB
    $connection = getConnection();

    if (mysqli_connect_errno()) {
        writeMessage("Could not connect to database");
        exit();
    }

	// must at least enter a receiptid and expected delivery date so check that values were entered
	checkRequiredFields('receiptid','new_expecteddate'); // these are values of the name field from the form
	
	// get the values user entered in the form
	$receiptid = $_POST['receiptid'];
	$expecteddate = $_POST['new_expecteddate'];
	$delivereddate = $_POST['new_deliverdate'];
	
	// if it's not empty then make sure the string is only digits in YYYY-MM-DD format
	if (!preg_match("/\d{4}-{1}\d{2}-{1}\d{2}/", $expecteddate) 
			|| (!preg_match("/\d{4}-{1}\d{2}-{1}\d{2}/", $delivereddate) && !empty($delivereddate))){
		writeMessage("Date fields must have only #'s in YYYY-MM-DD or be left blank");
		exit();
	}
	/* these values might not be dates in YYYY-MM-DD format, in which case 
	the date is updated to empty string equivalent 0000-00-00
	so make them NULL instead. */
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
	
	// Close the connection to the database once we're done with it.
    mysqli_close($connection);
	
	showIncompleteOrders();
}

function showDailySalesReport(){
	// need to get data, so get a connection to the DB
    $connection = getConnection();

    if (mysqli_connect_errno()) {
        writeMessage("Could not connect to database");
        exit();
    }
	
	// user must enter a date
	checkRequiredFields('date');
	$reportdate = $_POST['date'];
	
	// check the date format
	if (!preg_match("/\d{4}-{1}\d{2}-{1}\d{2}/", $reportdate)){
		writeMessage("Date field must have only #'s in YYYY-MM-DD format");
		exit();
	}
	
	// get unit totals in 1st query
	$unit_totals = $connection->prepare('SELECT upc, category, truncate(price,2), units, truncate((price * units),2) as unit_total
											  FROM     (SELECT P.upc, I.category, I.price, sum(P.quantity) as units
														FROM orders O, purchaseitem P, item I
														WHERE O.receiptId = P.receiptId and P.upc = I.upc and O.odate = ?
														GROUP BY P.upc) as grouped
											  GROUP BY upc
											  ORDER BY category');
	$unit_totals->bind_param('s',$reportdate);
    $unit_totals->execute();
    $unit_totals->store_result();
    $unit_totals->bind_result($upc, $category, $price, $units, $unit_total);
	
	if($unit_totals->error) {       
		writeMessage("Error getting the report:".$unit_totals->error);
		exit();
	}
	
	// get category totals in 2nd query
	$category_totals = $connection->prepare('SELECT category, sum(unit_total)
										     FROM 
												(SELECT upc, category, price, units, truncate((price * units),2) as unit_total
												 FROM  (SELECT P.upc, I.category, I.price, sum(P.quantity) as units
														FROM orders O, purchaseitem P, item I
														WHERE O.receiptId = P.receiptId and P.upc = I.upc and O.odate = ?
														GROUP BY P.upc) as grouped1
												 GROUP BY upc) as grouped2
											GROUP BY category
											ORDER BY category');
	$category_totals->bind_param('s',$reportdate);
    $category_totals->execute();
	$category_totals->store_result();
	$category_totals->bind_result($unused, $total);
	
	if($category_totals->error) {       
		writeMessage("Error getting the report:".$category_totals->error);
		exit();
	}

	// set up the table
	echo "<table>
			<tr><td class=reporttitle colspan=5>Daily Sales Report for: ".$reportdate.
			"</td><tr>
			<td class=rowheader>UPC</td>
			<td class=rowheader>Category</td>
			<td class=rowheader>Unit Price</td>
			<td class=rowheader>Units</td>
			<td class=rowheader>Total Value</td>
			</tr>";
	// now write each row from result as a row in the html table
	$len_unit_totals = $unit_totals->num_rows;
	$grandtotal = 0;
	$totalunits = 0;
	if ($len_unit_totals == 0 || $category_totals->num_rows == 0){
		writeMessage("No sales on ".$reportdate);
		exit();
	} else {
		$previous_category = NULL;
		$current_category = NULL;
		while($unit_totals->fetch()){
			$current_category = $category;
			// write the previous category's total if we just switched to a new category
			if ($current_category != $previous_category && $previous_category != NULL) {
				$category_totals->fetch();
				$grandtotal += $total;
				echo "<tr><td class=additionline colspan=5>----------------------------</td></tr><tr>";
				echo "<td class=dailyreporttotal colspan=5>Total : $".$total."</td>";
				echo "</tr>";
			}
			// write this unit totals for this category
			$totalunits += $units;
			echo "<tr>";
			echo "<td>".$upc."</td>";
			echo "<td>".$category."</td>";
			echo "<td>$".$price."</td>";
			echo "<td>".$units."</td>";
			echo "<td>$".$unit_total."</td>";
			echo "</tr>";
			$previous_category = $current_category;
		}
		// write the last category total
		$category_totals->fetch();
		$grandtotal += $total;
		echo "<tr><td class=additionline colspan=5>----------------------------</td></tr>";
		echo "<tr><td class=dailyreporttotal colspan=5>Total : $".$total."</td></tr>";
		// write the total sales for the day
		echo "<tr><td class=additionline colspan=5>----------------------------</td></tr>";
		echo "<tr><td class=dailyreporttotal colspan=3>Total Daily Sales :</td>";
		echo "<td class=dailyreporttotal>".$totalunits."</td>";
		echo "<td class=dailyreporttotal> $".$grandtotal."</td></tr>";
	}
	echo "</table>";

    // Close the connection to the database once we're done with it.
    mysqli_close($connection);
   }

function showTopSellers(){
	// need to get data, so get a connection to the DB
    $connection = getConnection();

    if (mysqli_connect_errno()) {
        writeMessage("Could not connect to database");
        exit();
    }
	
	// user must enter a date
	checkRequiredFields('date');
	$reportdate = $_POST['date'];
	$numToGet = $_POST['topnum'];
	
	// check if user forgot to enter number of top sellers to retrieve
	if (empty($numToGet)){
		$numToGet = 1; // show top 1 by default
		writeMessage("You forgot to enter how many, showing you the top seller.");
	}
	// check the date format
	if (!preg_match("/\d{4}-{1}\d{2}-{1}\d{2}/", $reportdate)){
		writeMessage("Date field must have only #'s in YYYY-MM-DD format");
		exit();
	}
	
	// get unit totals in 1st query
	$topsellers = $connection->prepare('SELECT title, company, stock, units_sold
										 FROM    (SELECT P.upc, I.title, I.company, I.stock, sum(P.quantity) as units_sold
													FROM orders O, purchaseitem P, item I
													WHERE O.receiptId = P.receiptId and P.upc = I.upc and O.odate = ?
													GROUP BY P.upc
													ORDER BY category) as grouped
										 GROUP BY units_sold
										 ORDER BY units_sold desc
										 LIMIT ?;');
	$topsellers->bind_param('si',$reportdate,$numToGet);
    $topsellers->execute();
    $topsellers->store_result();
    $topsellers->bind_result($title, $company, $stock, $units_sold);
	
	if($topsellers->error) {       
		writeMessage("Error getting the report:".$topsellers->error);
		exit();
	}

	// set up the table
	echo "<table>
			<tr><td class=reporttitle colspan=5>Top Sellers for ".$reportdate.
			"</td><tr>
			<td class=rowheader>#</td>
			<td class=rowheader>Title</td>
			<td class=rowheader>Company</td>
			<td class=rowheader>Stock</td>
			<td class=rowheader>Units Sold</td>
			</tr>";
	// now write each row from result as a row in the html table
	$len_topsellers = $topsellers->num_rows;
	$i = 1;
	
	if ($len_topsellers == 0){
		writeMessage("No items were sold on this day!");
		exit();
	}
	if ($len_topsellers < $numToGet) {
		writeMessage("You asked for ".$numToGet." but only ".$len_topsellers." top sellers available.");
	}
	while($topsellers->fetch()){
		echo "<tr>";
		echo "<td>".$i."</td>";
		echo "<td>".$title."</td>";
		echo "<td>".$company."</td>";
		echo "<td>".$stock."</td>";
		echo "<td>".$units_sold."</td>";
		echo "</tr>";
		$i += 1;
	}
	echo "</table>";

    // Close the connection to the database once we're done with it.
    mysqli_close($connection);
   }

   function addNewItem(){
	// need to get data, so get a connection to the DB
    $connection = getConnection();

    if (mysqli_connect_errno()) {
        writeMessage("Could not connect to database");
        exit();
    }

    checkRequiredFields('new_upc');
    $upc = $_POST['new_upc'];

    // make the query needed, it gives an array of rows from resulting query table
	$checkstmt = $connection->query("SELECT * from item WHERE upc = \"$upc\"");

	    // tell user what happened
	if($connection->error) {       
		writeMessage("Error when adding items: $connection->error");
	} 

	if ($checkstmt->num_rows > 0){
		// if there's an existing item update it

		checkRequiredFields('new_stock');
		$price = $_POST['new_price'];
		$stock = $_POST['new_stock'];


	if (!preg_match("(^\d{0,8}\.\d{2}$)", $price) && !empty($price)){
		writeMessage("Price field must have only #'s and 2 numbers after a decimal");
		exit();
	}

	$sqlQuery = "UPDATE item SET stock = \"$stock\"";
	
	if ($price != NULL){
		$sqlQuery = $sqlQuery.", price = \"$price\"";
	} 

	$sqlQuery = $sqlQuery." WHERE upc = \"$upc\"";

	$stmt = $connection->query($sqlQuery);

	// tell user what happened
	if($stmt->error) {     
		writeMessage("Error when editing the item:".$stmt->error);
	} else {
         writeMessage("Successfully updated item :".$upc." ");
	}

	} 
	else {
		// No existing item add a new one
	
	// must at least enter a upc, title, type, category, price and stock so check that values were entered
	checkRequiredFields('new_upc','new_title', 'new_item_type', 'new_category', 'new_price', 'new_stock'); 
	
	// get the values user entered in the form
	//$upc = $_POST['new_upc'];
	$title = $_POST['new_title'];
	$item_type = $_POST['new_item_type'];
	$category = $_POST['new_category'];
	$company = $_POST['new_company'];
	$release_year = $_POST['new_release_year'];
	$price = $_POST['new_price'];
	$stock = $_POST['new_stock'];
	

	// check the date format
	if (!preg_match("(^\d{4}$)", $release_year) && !empty($release_year)){
		writeMessage("Year field must be a valid year");
		exit();
	}
	if (!preg_match("(^\d{0,8}\.\d{2}$)", $price)){
		writeMessage("Price field must have only #'s and 2 numbers after a decimal");
		exit();
	}
	// make the query needed, it gives an array of rows from resulting query table
	$stmt = $connection->prepare('INSERT INTO item 
		(upc, title, item_type, category, company, release_year, price, stock) 
		VALUES (?,?,?,?,?,?,?,?)');

	$stmt->bind_param("issssidi", $upc, $title, $item_type, $category, $company, $release_year, $price, $stock);
        
    $stmt->execute();
	
	// make sure a row was actually changed, e.g. 0 rows are changed if receiptId entered does not exist!
	if ($stmt->affected_rows == 0){
		writeMessage("UPC does not exist.");
		exit();
	}
	// tell user what happened
	if($stmt->error) {       
		writeMessage("Error when adding the item:".$stmt->error);
	} else {
         writeMessage("Successfully added the item");
	}
}
	
	// Close the connection to the database once we're done with it.
    mysqli_close($connection);
	
	showInventory();
}

?>


<!--
 USER CONTROLS ARE HERE
-->
<table>
<tr>
<!-- add item form -->
<td>
<h2>Add a New Item/Edit an Existing Item:</h2>
<form id="add" name="add" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
    <table border=0 cellpadding=0 cellspacing=0>
        <tr><td>UPC:</td><td><input type="text" size=30 name="new_upc"></td></tr>
        <tr><td>Title:</td><td><input type="text" size=30 name="new_title"></td></tr>
        <tr><td>Type:</td><td><select name="new_item_type"> 
        	<option value="DVD">DVD</option>
        	<option value="CD">CD</option></select></td></tr>
        <tr><td>Category:</td><td> <select name="new_category">
        	<option value="Rock">Rock</option>
        	<option value="Country">Country</option>
        	<option value="Pop">Pop</option>
        	<option value="Rap">Rap</option>
        	<option value="Classical">Classical</option>
        	<option value="Instrumental">Instrumental</option>
        	<option value="New Age">New Age</option></select></td></tr>
        <tr><td>Company:</td><td> <input type="text" size=30 name="new_company"></td></tr>
        <tr><td>Year:</td><td> <input type="text" size=30 name="new_release_year"></td></tr>
        <tr><td>Price:</td><td> <input type="text" size=30 name="new_price"></td></tr>
        <tr><td>Stock:</td><td> <input type="text" size=30 name="new_stock"></td></tr>
        <tr><td></td><td><input type="submit" name="submit_additem" border=0 value="Add/Edit Item"></td></tr>
    </table>
</form>
</td>

<!-- top selling items form -->
<td>
<h2>See Top Sellers:</h2>
<form id="top" name="top" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
    <table border=0 cellpadding=0 cellspacing=0>
        <tr><td>Date:</td><td><input type="text" size=30 name="date" value="2014-11-02"></td></tr>
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
        <tr><td>Date:</td><td><input type="text" size=30 name="date" value="2014-11-02"></td></tr>
        <tr><td></td><td><input type="submit" name="submit_dailysales" border=0 value="Get Sales Report"></td></tr>
    </table>
</form>
</td>

<!-- view inventory -->
<td>
<h2>View Inventory:&nbsp;&nbsp;</h2>
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
