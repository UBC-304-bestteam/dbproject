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
<tr><td><div id='errorarea' class="errorarea"> </div></td></tr>
</table>
</div>

<?php

// USEFUL GENERAL PROCEDURES
function getConnection() {
	return @new mysqli("127.0.0.1:3306", "root", "", "practice");

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

//	Clerk clicked "Return" button so setup what should happen when page is refreshed	
    if (isset($_POST["submit_return"]) && $_POST["submit_return"] == "Return") {
		// just call function that gets and writes the results.
       returnItem();
      }

}
   
// FUNCTIONS THAT DEAL WITH DB REQUESTS
function returnItem(){
	// need to get data, so get a connection to the DB
    $connection = getConnection();

    if (mysqli_connect_errno()) {
        writeMessage("Could not connect to database");
        exit();
    }

	// must enter receiptid, upc and quantity_returned
	// this will also give an error if return quantity is zero
	checkRequiredFields('receiptid','upc','quantity_returned'); // these are values of the name field from the form
	
	// get the values user entered in the form
	$receiptid = $_POST['receiptid'];
	$return_upc = $_POST['upc'];
	$return_quantity = $_POST['quantity_returned'];

	
	// get the order info from receiptid and purchaseitem
	$orderinfo = $connection->prepare(  'SELECT O.odate, O.card, P.upc, P.quantity, O.receiptId
									FROM orders O, purchaseitem P
									WHERE O.receiptId = P.receiptId and O.receiptId = ?;');
	$orderinfo->bind_param("i",$receiptid);
    $orderinfo->execute();
	$orderinfo->store_result();
	$orderinfo->bind_result($odate, $creditcard, $upc, $num_purchased, $receiptId);
	
	// check if the was purchased in this order
	$orderdate_string = '';
	$founditem = FALSE;
	while($row = $orderinfo->fetch()){
		if ($return_upc == $upc){
			$founditem = TRUE;
			$orderdate_string .= $odate;
			break;
		}
	}
	// if upc not found give an error otherwise continue
	if (!$founditem) {
		writeMessage("The item upc was not found for this receiptId");
		exit();
	}
	
	
	// ensure item was purchased and returning valid quantity and current date is within 15 days of order date
	$currdate_string = date('Y-m-d');
	$currdate = strtotime($currdate_string); // seconds since some base time...
	$orderdate = strtotime($orderdate_string); // seconds since some base time...
	$timetaken = $currdate - $orderdate;
	$timelimit = 1296000; // seconds in 15 days
	if ($timetaken > $timelimit){
		writeMessage("Return ineligible: more than 15 days since purchase.");
		exit();
	}
	
	// find any existing returns for that item and get the total returned quantity
	$totalreturned = $connection->prepare( 'SELECT sum(quantity)
											FROM returntransaction R, returnitem RI
											WHERE R.receiptId=? 
												   and upc=? and R.retId = RI.retId;');
	$totalreturned->bind_param("ii",$receiptid,$return_upc);
    $totalreturned->execute();
	$totalreturned->store_result();
	$totalreturned->bind_result($already_returned);
	
	// don't allow returns over what was originally purchased
	$totalreturned->fetch();
	if ($num_purchased < ($already_returned + $return_quantity)){
		writeMessage("Cannot return ".$return_quantity." units, "
						.$num_purchased." were purchased and "
						.$already_returned." were already returned.");
		exit();
	}
	
	// the rest will be a transaction: if one of these fails we want to rollback all the changes
	$connection->autocommit(FALSE);
		
			// create returntransaction
			// retid is automatically generated since it's set to auto_increment in table.sql, just pass in null
			$return = $connection->prepare( 'INSERT INTO returntransaction
											 VALUES (NULL, CURDATE(), ?);');
			$return->bind_param("i",$receiptid);
			$return->execute();
			
			// create returnitem
			$new_retid = $return->insert_id; // get the auto-generated return id from the last query
			$returnitem = $connection->prepare( 'INSERT INTO returnitem
												 VALUES (?, ?, ?)');
			$returnitem->bind_param("iii",$new_retid, $return_upc, $return_quantity);
			$returnitem->execute();
			
			// update stock for the returned item
			$updatestock = $connection->prepare( 'UPDATE item
												  SET stock = (stock+?)
												  WHERE upc=?;');
			$updatestock->bind_param("ii",$return_quantity, $return_upc);
			$updatestock->execute();
	
		// if any of the queries failed then rollback otherwise commit the changes
		if ($return->error || $returnitem->error || $updatestock->error) {
			writeMessage("There was an error while submitting the return: ".$return->error.$returnitem->error.$updatestock->error);
			$connection->rollback();
			mysqli_close($connection);
			exit();
			
		} else {
			$connection->commit();
		}

	// Close the connection to the database once we're done with it.
	writeMessage($return_quantity." units returned, card # ".$creditcard." was refunded.");
    mysqli_close($connection);
}


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
        <tr><td>Receipt ID:</td><td><input type="text" size=30 name="receiptid" value="01000"></td></tr>
        <tr><td>Item UPC:</td><td><input type="text" size=30 name="upc" value="0003"></td></tr>
        <tr><td>Quantity returned:</td><td> <input type="text" size=30 name="quantity_returned" value="1"></td></tr>
        <tr><td></td><td><input type="submit" name="submit_return" border=0 value="Return"></td></tr>
    </table>
</form>
</td>



</tr>
</table>
</body>
</html>
