<html>
<head>
<meta content="text/html;charset=utf-8" http-equiv="Content-Type">
<meta content="utf-8" http-equiv="encoding">

<title>CPSC 304 Project</title>
<!--
    A simple stylesheet is provided so you can modify colours, fonts, etc.
-->
    <link href="bookbiz.css" rel="stylesheet" type="text/css">

<!--
    Javascript to submit a title_id as a POST form, used with the "delete" links
-->
<script>
function formSubmit(cid) {
    'use strict';
    if (confirm('Are you sure you want to delete this entry?')) {
      // Set the value of a hidden HTML element in this form
      var form = document.getElementById('delete');
      form.cid.value = cid;
      // Post this form
      form.submit();
    }
}
</script>
</head>

<body>
<h1>Inventory</h1>

<?php

   /****************************************************
     STEP 1: Connect to the MySQL database
     ****************************************************/

    // CHANGE this to connect to your own MySQL instance in the labs or on your own computer
     define('sqlUsername', "");
     define('sqlPassword', "");
     define('sqlServerName', "");
     define('DB_HOST', '127.0.0.1'); 

    $connection = new mysqli(DB_HOST, sqlUsername, sqlPassword, sqlServerName);


    // Check that the connection was successful, otherwise exit
    if (mysqli_connect_errno()) {
        printf("Connect failed: %s\n", mysqli_connect_error());
        exit();
    }
    
   /****************************************************
     showCustomer
     ****************************************************/
   function showCustomer(){
   
    /****************************************************
     STEP 1: Connect to the MySQL database
     ****************************************************/

    // CHANGE this to connect to your own MySQL instance in the labs or on your own computer
    $connection = new mysqli(DB_HOST, sqlUsername, sqlPassword, sqlServerName);


    // Check that the connection was successful, otherwise exit
    if (mysqli_connect_errno()) {
        printf("Connect failed: %s\n", mysqli_connect_error());
        exit();
    }
   
   // Select all of the book rows columns title_id, title and pub_id
    if (!$result = $connection->query("SELECT cid, customer_name, address FROM customer ORDER BY customer_name")) {
        die('There was an error running the query [' . $db->error . ']');
    }

    // Avoid Cross-site scripting (XSS) by encoding PHP_SELF (this page) using htmlspecialchars.
    echo "<form id=\"delete\" name=\"delete\" action=\"";
    echo htmlspecialchars($_SERVER["PHP_SELF"]);
    echo "\" method=\"POST\">";
    // Hidden value is used if the delete link is clicked
    echo "<input type=\"hidden\" name=\"cid\" value=\"-1\"/>";
   // We need a submit value to detect if delete was pressed 
    echo "<input type=\"hidden\" name=\"submitDelete\" value=\"DELETE\"/>";

    /****************************************************
     STEP 2: Detect the user action
     ****************************************************/

    if ($_SERVER["REQUEST_METHOD"] == "POST") {

      if (isset($_POST["submitDelete"]) && $_POST["submitDelete"] == "DELETE") {
       /*
          Delete the selected customer using the customer id
        */
       
       // Create a delete query prepared statement with a ? for the cid
       $stmt = $connection->prepare("DELETE FROM customer WHERE cid=?");
       $deleteCid = $_POST['cid'];
       // Bind the title_id parameter, 's' indicates a string value
       $stmt->bind_param("s", $deleteCid);
       
       // Execute the delete statement
       $stmt->execute();
          
       if($stmt->error) {
         printf("<b>Error: %s.</b>\n", $stmt->error);
       } else {
         echo "<b>Successfully deleted ".$deleteCid."</b>";
       }
            
      } elseif (isset($_POST["submit"]) && $_POST["submit"] ==  "ADD") {       
       /*
        Add a customer using the post variables.
        */
        $cid = $_POST["new_column_1"];
        $pword = $_POST["new_column_2"];
        $customer_name = $_POST["new_column_3"];
        $address = $_POST["new_column_4"];
        $phone = $_POST["new_column_5"];
          
        $stmt = $connection->prepare("INSERT INTO customer (cid, pword, customer_name, address, phone) VALUES (?,?,?,?,?)");
         
        $stmt->bind_param("sssss", $cid, $pword, $customer_name, $address, $phone);
        
        // Execute the insert statement
        $stmt->execute();
          
        if($stmt->error) {       
          printf("<b>Error: %s.</b>\n", $stmt->error);
        } else {
          echo "<b>Successfully added account for ".$customer_name."</b>";
        }
      }
   }

    /****************************************************
     STEP 3: Display the customer information
     ****************************************************/
    // Display each Customer databaserow as a table row
    while($row = $result->fetch_assoc()){
        
       echo "<td>".$row['cid']."</td>";
       echo "<td>".$row['customer_name']."</td>";
       echo "<td>".$row['address']."</td><td>";
       
       //Display an option to delete this customer using the Javascript function and the hidden title_id
       echo "<a href=\"javascript:formSubmit('".$row['cid']."');\">DELETE</a>";
       echo "</td></tr>";
        
    }
    echo "</form>";

    // Close the connection to the database once we're done with it.
    mysqli_close($connection);
   }
   
   /****************************************************
     showHasSong
     ****************************************************/
   function showHasSong(){
   
    /****************************************************
     STEP 1: Connect to the MySQL database
     ****************************************************/

    // CHANGE this to connect to your own MySQL instance in the labs or on your own computer
    $connection = new mysqli(DB_HOST, sqlUsername, sqlPassword, sqlServerName);


    // Check that the connection was successful, otherwise exit
    if (mysqli_connect_errno()) {
        printf("Connect failed: %s\n", mysqli_connect_error());
        exit();
    }
   
   // Select all of the hassong rows columns upc, and title
    if (!$result = $connection->query("SELECT upc, title FROM hassong ORDER BY title")) {
        die('There was an error running the query [' . $db->error . ']');
    }

    // Avoid Cross-site scripting (XSS) by encoding PHP_SELF (this page) using htmlspecialchars.
    echo "<form id=\"delete\" name=\"delete\" action=\"";
    echo htmlspecialchars($_SERVER["PHP_SELF"]);
    echo "\" method=\"POST\">";
    // Hidden value is used if the delete link is clicked
    echo "<input type=\"hidden\" name=\"upc\" value=\"-1\"/>";
   // We need a submit value to detect if delete was pressed 
    echo "<input type=\"hidden\" name=\"submitDelete\" value=\"DELETE\"/>";

    /****************************************************
     STEP 2: Detect the user action
     ****************************************************/

    if ($_SERVER["REQUEST_METHOD"] == "POST") {

      if (isset($_POST["submitDelete"]) && $_POST["submitDelete"] == "DELETE") {
       /*
          Delete the selected song using the upc, and title
        */
       
       $stmt = $connection->prepare("DELETE FROM hassong WHERE upc=? AND title=?");
       $deleteUpc = $_POST['upc'];
       $deleteTitle = $_POST['title'];
       $stmt->bind_param("is", $deleteUpc, $deleteTitle);
       
       // Execute the delete statement
       $stmt->execute();
          
       if($stmt->error) {
         printf("<b>Error: %s.</b>\n", $stmt->error);
       } else {
         echo "<b>Successfully deleted ".$deleteTitle." for ".$deleteUpc."</b>";
       }
            
      } elseif (isset($_POST["submit"]) && $_POST["submit"] ==  "ADD") {       
       /*
        Add a hassong using the post variables.
        */
        $upc = $_POST["new_column_1"];
        $title = $_POST["new_column_2"];
          
        $stmt = $connection->prepare("INSERT INTO hassong (upc, title) VALUES (?,?)");
          
        $stmt->bind_param("is", $upc, $title);
        
        // Execute the insert statement
        $stmt->execute();
          
        if($stmt->error) {       
          printf("<b>Error: %s.</b>\n", $stmt->error);
        } else {
          echo "<b>Successfully added song for ".$title."</b>";
        }
      }
   }

    /****************************************************
     STEP 3: Display the list of hassong
     ****************************************************/
    // Display each book title databaserow as a table row
    while($row = $result->fetch_assoc()){
        
       echo "<td>".$row['upc']."</td>";
       echo "<td>".$row['title']."</td>";
       
       echo "<a href=\"javascript:formSubmit('".$row['upc']."');\">DELETE</a>";
       echo "</td></tr>";
        
    }
    echo "</form>";

    // Close the connection to the database once we're done with it.
    mysqli_close($connection);
   }


   /****************************************************
     showLeadSinger
     ****************************************************/
   function showLeadSinger(){
   
    /****************************************************
     STEP 1: Connect to the MySQL database
     ****************************************************/

    // CHANGE this to connect to your own MySQL instance in the labs or on your own computer
    $connection = new mysqli(DB_HOST, sqlUsername, sqlPassword, sqlServerName);

    // Check that the connection was successful, otherwise exit
    if (mysqli_connect_errno()) {
        printf("Connect failed: %s\n", mysqli_connect_error());
        exit();
    }
   
   // Select all of the lead singers
    if (!$result = $connection->query("SELECT upc, singer_name FROM leadsinger ORDER BY upc")) {
        die('There was an error running the query [' . $db->error . ']');
    }

    // Avoid Cross-site scripting (XSS) by encoding PHP_SELF (this page) using htmlspecialchars.
    echo "<form id=\"delete\" name=\"delete\" action=\"";
    echo htmlspecialchars($_SERVER["PHP_SELF"]);
    echo "\" method=\"POST\">";
    // Hidden value is used if the delete link is clicked
    echo "<input type=\"hidden\" name=\"upc\" value=\"-1\"/>";
   // We need a submit value to detect if delete was pressed 
    echo "<input type=\"hidden\" name=\"submitDelete\" value=\"DELETE\"/>";

     /****************************************************
     STEP 2: Detect the user action
     ****************************************************/

    if ($_SERVER["REQUEST_METHOD"] == "POST") {

      if (isset($_POST["submitDelete"]) && $_POST["submitDelete"] == "DELETE") {
       /*
          Delete the selected lead singer
        */
       
       // Create a delete query prepared statement with a ? for the title_id
       $stmt = $connection->prepare("DELETE FROM leadsinger WHERE upc=? AND singer_name=?");
       $deleteUpc = $_POST['upc'];
       $deleteSingerName = $_POST['singer_name'];
       // Bind the title_id parameter, 's' indicates a string value
       $stmt->bind_param("is", $deleteUpc, $deleteSingerName);
       
       // Execute the delete statement
       $stmt->execute();
          
       if($stmt->error) {
         printf("<b>Error: %s.</b>\n", $stmt->error);
       } else {
         echo "<b>Successfully deleted ".$deleteSingerName." for ".$deleteUpc."</b>";
       }
            
      } elseif (isset($_POST["submit"]) && $_POST["submit"] ==  "ADD") {       
       /*
        Add a book title using the post variables title_id, title and pub_id.
        */
        $upc = $_POST["new_column_1"];
        $singer_name = $_POST["new_column_2"];
          
        $stmt = $connection->prepare("INSERT INTO leadsinger (upc, singer_name) VALUES (?,?)");
          
        // Bind the title and pub_id parameters, 'sss' indicates 3 strings
        $stmt->bind_param("is", $upc, $title);
        
        // Execute the insert statement
        $stmt->execute();
          
        if($stmt->error) {       
          printf("<b>Error: %s.</b>\n", $stmt->error);
        } else {
          echo "<b>Successfully added singer ".$singer_name."</b>";
        }
      }
   }

    /****************************************************
     STEP 3: Display the list lead singers
     ****************************************************/
    // Display each lead singer databaserow as a table row
    while($row = $result->fetch_assoc()){
        
       echo "<td>".$row['upc']."</td>";
       echo "<td>".$row['singer_name']."</td>";
	   
       
       //Display an option to delete this title using the Javascript function and the hidden title_id
       echo "<a href=\"javascript:formSubmit('".$row['upc']."');\">DELETE</a>";
       echo "</td></tr>";
        
    }
    echo "</form>";

    // Close the connection to the database once we're done with it.
    mysqli_close($connection);
   }

   /****************************************************
     showItem
     ****************************************************/
   function showItem(){
   
    /****************************************************
     STEP 1: Connect to the MySQL database
     ****************************************************/

    // CHANGE this to connect to your own MySQL instance in the labs or on your own computer
    $connection = new mysqli(DB_HOST, sqlUsername, sqlPassword, sqlServerName);


    // Check that the connection was successful, otherwise exit
    if (mysqli_connect_errno()) {
        printf("Connect failed: %s\n", mysqli_connect_error());
        exit();
    }
   
   // Select all of the book rows columns title_id, title and pub_id
    if (!$result = $connection->query("SELECT upc, title, item_type, category, company, release_year, price, stock FROM item ORDER BY upc")) {
        die('There was an error running the query [' . $db->error . ']');
    }

    // Avoid Cross-site scripting (XSS) by encoding PHP_SELF (this page) using htmlspecialchars.
    echo "<form id=\"delete\" name=\"delete\" action=\"";
    echo htmlspecialchars($_SERVER["PHP_SELF"]);
    echo "\" method=\"POST\">";
    // Hidden value is used if the delete link is clicked
    echo "<input type=\"hidden\" name=\"upc\" value=\"-1\"/>";
   // We need a submit value to detect if delete was pressed 
    echo "<input type=\"hidden\" name=\"submitDelete\" value=\"DELETE\"/>";

    /****************************************************
     STEP 2: Detect the user action
     ****************************************************/

    if ($_SERVER["REQUEST_METHOD"] == "POST") {

      if (isset($_POST["submitDelete"]) && $_POST["submitDelete"] == "DELETE") {
       /*
          Delete the selected lead singer
        */
       
       // Create a delete query prepared statement with a ? for the title_id
       $stmt = $connection->prepare("DELETE FROM item WHERE upc=?");
       $deleteUpc = $_POST['upc'];

       $stmt->bind_param("i", $deleteUpc);
       
       // Execute the delete statement
       $stmt->execute();
          
       if($stmt->error) {
         printf("<b>Error: %s.</b>\n", $stmt->error);
       } else {
         echo "<b>Successfully deleted ".$deleteUpc."</b>";
       }
            
      } elseif (isset($_POST["submit"]) && $_POST["submit"] ==  "ADD") {       
       /*
        Add a book title using the post variables title_id, title and pub_id.
        */
        $upc = $_POST["new_column_1"];
        $title = $_POST["new_column_2"];
        $item_type = $_POST["new_column_3"];
        $category = $_POST["new_column_4"];
        $company = $_POST["new_column_5"];
        $release_year = $_POST["new_column_6"];
        $price = $_POST["new_column_7"];
        $stock = $_POST["new_column_8"];
          
        $stmt = $connection->prepare("INSERT INTO item (upc, title, item_type, category, company, release_year, price, stock) VALUES (?,?,?,?,?,?,?,?)");
          
        // Bind the title and pub_id parameters, 'sss' indicates 3 strings
        $stmt->bind_param("issssidi", $upc, $title, $item_type, $category, $company, $release_year, $price, $stock);
        
        // Execute the insert statement
        $stmt->execute();
          
        if($stmt->error) {       
          printf("<b>Error: %s.</b>\n", $stmt->error);
        } else {
          echo "<b>Successfully added item ".$title."</b>";
        }
      }
   }
    /****************************************************
     STEP 3: Display the list items
     ****************************************************/
    // Display each book title databaserow as a table row
    while($row = $result->fetch_assoc()){

      echo "<td>".$row['upc']."</td>";
      echo "<td>".$row['title']."</td>";
      echo "<td>".$row['item_type']."</td>";
      echo "<td>".$row['category']."</td>";
      echo "<td>".$row['company']."</td>";
      echo "<td>".$row['release_year']."</td>";
      echo "<td>".$row['price']."</td>";
      echo "<td>".$row['stock']."</td><td>";
	   
       
       //Display an option to delete this title using the Javascript function and the hidden title_id
       echo "<a href=\"javascript:formSubmit('".$row['upc']."');\">DELETE</a>";
       echo "</td></tr>";
        
    }
    echo "</form>";

    // Close the connection to the database once we're done with it.
    mysqli_close($connection);
   }
   
   /****************************************************
     showOrder
     ****************************************************/
   function showOrder(){
   
    /****************************************************
     STEP 1: Connect to the MySQL database
     ****************************************************/

    // CHANGE this to connect to your own MySQL instance in the labs or on your own computer
    $connection = new mysqli(DB_HOST, sqlUsername, sqlPassword, sqlServerName);


    // Check that the connection was successful, otherwise exit
    if (mysqli_connect_errno()) {
        printf("Connect failed: %s\n", mysqli_connect_error());
        exit();
    }
   
   // Select all of the book rows columns title_id, title and pub_id
    if (!$result = $connection->query("SELECT receiptId, odate, cid, card, expiryDate, expectedDate, deliveredDate FROM orders ORDER BY receiptId")) {
        die('There was an error running the query [' . $db->error . ']');
    }

    // Avoid Cross-site scripting (XSS) by encoding PHP_SELF (this page) using htmlspecialchars.
    echo "<form id=\"delete\" name=\"delete\" action=\"";
    echo htmlspecialchars($_SERVER["PHP_SELF"]);
    echo "\" method=\"POST\">";
    // Hidden value is used if the delete link is clicked
    echo "<input type=\"hidden\" name=\"receiptId\" value=\"-1\"/>";
   // We need a submit value to detect if delete was pressed 
    echo "<input type=\"hidden\" name=\"submitDelete\" value=\"DELETE\"/>";

     /****************************************************
     STEP 2: Detect the user action
     ****************************************************/

    if ($_SERVER["REQUEST_METHOD"] == "POST") {

      if (isset($_POST["submitDelete"]) && $_POST["submitDelete"] == "DELETE") {
       /*
          Delete the selected order
        */
       $stmt = $connection->prepare("DELETE FROM order WHERE receiptId=?");
       $deleteReceiptId = $_POST['receiptId'];

       $stmt->bind_param("i", $deleteReceiptId);
       
       // Execute the delete statement
       $stmt->execute();
          
       if($stmt->error) {
         printf("<b>Error: %s.</b>\n", $stmt->error);
       } else {
         echo "<b>Successfully deleted ".$deleteReceiptId."</b>";
       }
            
      } elseif (isset($_POST["submit"]) && $_POST["submit"] ==  "ADD") {       


        $receiptId = $_POST["new_column_1"];
        $odate = $_POST["new_column_2"];
        $cid = $_POST["new_column_3"];
        $card = $_POST["new_column_4"];
        $expiryDate = $_POST["new_column_5"];
        $expectedDate = $_POST["new_column_6"];
        $deliveredDate = $_POST["new_column_7"];
          
        $stmt = $connection->prepare("INSERT INTO order (receiptId, odate, cid, card, expiryDate, expectedDate, deliveredDate) VALUES (?,?,?,?,?,?,?)");
          
        // Bind the title and pub_id parameters, 'sss' indicates 3 strings
        $stmt->bind_param("ississs", $receiptId, $odate, $cid, $card, $expiryDate, $expectedDate, $deliveredDate);
        
        // Execute the insert statement
        $stmt->execute();
          
        if($stmt->error) {       
          printf("<b>Error: %s.</b>\n", $stmt->error);
        } else {
          echo "<b>Successfully added order ".$receiptId."</b>";
        }
      }
   }


    /****************************************************
     STEP 3: Display the orders
     ****************************************************/
    // Display each book title databaserow as a table row
    while($row = $result->fetch_assoc()){
        
       echo "<td>".$row['receiptId']."</td>";
       echo "<td>".$row['odate']."</td>";
       echo "<td>".$row['cid']."</td>";
	   echo "<td>".$row['card']."</td>";
       echo "<td>".$row['expiryDate']."</td>";
	   echo "<td>".$row['expectedDate']."</td>";
       echo "<td>".$row['deliveredDate']."</td>";
	   
       
       //Display an option to delete this title using the Javascript function and the hidden title_id
       echo "<a href=\"javascript:formSubmit('".$row['receiptId']."');\">DELETE</a>";
       echo "</td></tr>";
        
    }
    echo "</form>";

    // Close the connection to the database once we're done with it.
    mysqli_close($connection);
   }
   

   /****************************************************
     showPurchaseItem
     ****************************************************/
    function showPurchaseItem(){
   
     /****************************************************
     STEP 1: Connect to the MySQL database
     ****************************************************/

    // CHANGE this to connect to your own MySQL instance in the labs or on your own computer
    $connection = new mysqli(DB_HOST, sqlUsername, sqlPassword, sqlServerName);


    // Check that the connection was successful, otherwise exit
    if (mysqli_connect_errno()) {
        printf("Connect failed: %s\n", mysqli_connect_error());
        exit();
    }
   
   // Select all of the book rows columns title_id, title and pub_id
    if (!$result = $connection->query("SELECT receiptId, upc, quantity FROM purchaseitem ORDER BY receiptId")) {
        die('There was an error running the query [' . $db->error . ']');
    }

    // Avoid Cross-site scripting (XSS) by encoding PHP_SELF (this page) using htmlspecialchars.
    echo "<form id=\"delete\" name=\"delete\" action=\"";
    echo htmlspecialchars($_SERVER["PHP_SELF"]);
    echo "\" method=\"POST\">";
    // Hidden value is used if the delete link is clicked
    echo "<input type=\"hidden\" name=\"receiptId\" value=\"-1\"/>";
   // We need a submit value to detect if delete was pressed 
    echo "<input type=\"hidden\" name=\"submitDelete\" value=\"DELETE\"/>";

    /****************************************************
     STEP 2: Detect the user action
     ****************************************************/

    if ($_SERVER["REQUEST_METHOD"] == "POST") {

      if (isset($_POST["submitDelete"]) && $_POST["submitDelete"] == "DELETE") {
       /*
          Delete the selected order
        */
       $stmt = $connection->prepare("DELETE FROM purchaseitem WHERE receiptId=? AND upc=?");
       $deleteReceiptId = $_POST['receiptId'];
       $deleteUpc = $_POST['upc'];

       $stmt->bind_param("ii", $deleteReceiptId, $deleteUpc);
       
       // Execute the delete statement
       $stmt->execute();
          
       if($stmt->error) {
         printf("<b>Error: %s.</b>\n", $stmt->error);
       } else {
         echo "<b>Successfully deleted ".$deleteReceiptId." for ".$deleteUpc."</b>";
       }
            
      } elseif (isset($_POST["submit"]) && $_POST["submit"] ==  "ADD") {       


        $receiptId = $_POST["new_column_1"];
        $upc = $_POST["new_column_2"];
        $quantity = $_POST["new_column_3"];
          
        $stmt = $connection->prepare("INSERT INTO purchaseitem (receiptId, upc, quantity) VALUES (?,?,?)");
          
        // Bind the title and pub_id parameters, 'sss' indicates 3 strings
        $stmt->bind_param("iii", $receiptId, $upc, $quantity);
        
        // Execute the insert statement
        $stmt->execute();
          
        if($stmt->error) {       
          printf("<b>Error: %s.</b>\n", $stmt->error);
        } else {
          echo "<b>Successfully added purchase ".$receiptId." for ".$upc."</b>";
        }
      }
   }


    /****************************************************
     STEP 3: Display the purchases
     ****************************************************/
    // Display each purchase item databaserow as a table row
    while($row = $result->fetch_assoc()){
        
       echo "<td>".$row['receiptId']."</td>";
       echo "<td>".$row['upc']."</td>";
       echo "<td>".$row['quantity']."</td>";
	   
       
       //Display an option to delete this title using the Javascript function and the hidden title_id
       echo "<a href=\"javascript:formSubmit('".$row['receiptId']."');\">DELETE</a>";
       echo "</td></tr>";
        
    }
    echo "</form>";

    // Close the connection to the database once we're done with it.
    mysqli_close($connection);
   }
   
   /****************************************************
     showReturnItem
     ****************************************************/
    function showReturnItem(){
   
     /****************************************************
     STEP 1: Connect to the MySQL database
     ****************************************************/

    // CHANGE this to connect to your own MySQL instance in the labs or on your own computer
    $connection = new mysqli(DB_HOST, sqlUsername, sqlPassword, sqlServerName);


    // Check that the connection was successful, otherwise exit
    if (mysqli_connect_errno()) {
        printf("Connect failed: %s\n", mysqli_connect_error());
        exit();
    }
   
   // Select all of the book rows columns title_id, title and pub_id
    if (!$result = $connection->query("SELECT retid, upc, quantity FROM returnitem ORDER BY retid")) {
        die('There was an error running the query [' . $db->error . ']');
    }

    // Avoid Cross-site scripting (XSS) by encoding PHP_SELF (this page) using htmlspecialchars.
    echo "<form id=\"delete\" name=\"delete\" action=\"";
    echo htmlspecialchars($_SERVER["PHP_SELF"]);
    echo "\" method=\"POST\">";
    // Hidden value is used if the delete link is clicked
    echo "<input type=\"hidden\" name=\"retid\" value=\"-1\"/>";
   // We need a submit value to detect if delete was pressed 
    echo "<input type=\"hidden\" name=\"submitDelete\" value=\"DELETE\"/>";


    /****************************************************
     STEP 2: Detect the user action
     ****************************************************/

    if ($_SERVER["REQUEST_METHOD"] == "POST") {

      if (isset($_POST["submitDelete"]) && $_POST["submitDelete"] == "DELETE") {
       /*
          Delete the selected order
        */
       $stmt = $connection->prepare("DELETE FROM returnitem WHERE retid=? AND upc=?");
       $deleteRetId = $_POST['retid'];
       $deleteUpc = $_POST['upc'];

       $stmt->bind_param("ii", $deleteRetId, $deleteUpc);
       
       // Execute the delete statement
       $stmt->execute();
          
       if($stmt->error) {
         printf("<b>Error: %s.</b>\n", $stmt->error);
       } else {
         echo "<b>Successfully deleted ".$deleteRetId." for ".$deleteUpc."</b>";
       }
            
      } elseif (isset($_POST["submit"]) && $_POST["submit"] ==  "ADD") {       


        $retId = $_POST["new_column_1"];
        $upc = $_POST["new_column_2"];
        $quantity = $_POST["new_column_3"];
          
        $stmt = $connection->prepare("INSERT INTO returnitem (retId, upc, quantity) VALUES (?,?,?)");
          
        // Bind the title and pub_id parameters, 'sss' indicates 3 strings
        $stmt->bind_param("iii", $retId, $upc, $quantity);
        
        // Execute the insert statement
        $stmt->execute();
          
        if($stmt->error) {       
          printf("<b>Error: %s.</b>\n", $stmt->error);
        } else {
          echo "<b>Successfully added item return ".$retId." for ".$upc."</b>";
        }
      }
   }

    /****************************************************
     STEP 3: Display the list of item returns
     ****************************************************/
    // Display each book title databaserow as a table row
    while($row = $result->fetch_assoc()){
        
       echo "<td>".$row['retid']."</td>";
       echo "<td>".$row['upc']."</td>";
       echo "<td>".$row['quantity']."</td>";
	   
       
       //Display an option to delete this title using the Javascript function and the hidden title_id
       echo "<a href=\"javascript:formSubmit('".$row['retid']."');\">DELETE</a>";
       echo "</td></tr>";
        
    }
    echo "</form>";

    // Close the connection to the database once we're done with it.
    mysqli_close($connection);
   }
   

   /****************************************************
     showReturnTransaction
     ****************************************************/
      function showrReturnTransaction(){
   
     /****************************************************
     STEP 1: Connect to the MySQL database
     ****************************************************/

    // CHANGE this to connect to your own MySQL instance in the labs or on your own computer
    $connection = new mysqli(DB_HOST, sqlUsername, sqlPassword, sqlServerName);


    // Check that the connection was successful, otherwise exit
    if (mysqli_connect_errno()) {
        printf("Connect failed: %s\n", mysqli_connect_error());
        exit();
    }
   
   // Select all of the book rows columns title_id, title and pub_id
    if (!$result = $connection->query("SELECT retid, rdate, receiptId FROM returntransaction ORDER BY retid")) {
        die('There was an error running the query [' . $db->error . ']');
    }

    // Avoid Cross-site scripting (XSS) by encoding PHP_SELF (this page) using htmlspecialchars.
    echo "<form id=\"delete\" name=\"delete\" action=\"";
    echo htmlspecialchars($_SERVER["PHP_SELF"]);
    echo "\" method=\"POST\">";
    // Hidden value is used if the delete link is clicked
    echo "<input type=\"hidden\" name=\"retid\" value=\"-1\"/>";
   // We need a submit value to detect if delete was pressed 
    echo "<input type=\"hidden\" name=\"submitDelete\" value=\"DELETE\"/>";

    /****************************************************
     STEP 2: Detect the user action
     ****************************************************/

    if ($_SERVER["REQUEST_METHOD"] == "POST") {

      if (isset($_POST["submitDelete"]) && $_POST["submitDelete"] == "DELETE") {
       /*
          Delete the selected order
        */
       $stmt = $connection->prepare("DELETE FROM returntransaction WHERE retid=?");
       $deleteRetId = $_POST['retid'];

       $stmt->bind_param("i", $deleteRetId);
       
       // Execute the delete statement
       $stmt->execute();
          
       if($stmt->error) {
         printf("<b>Error: %s.</b>\n", $stmt->error);
       } else {
         echo "<b>Successfully deleted ".$deleteRetId."</b>";
       }
            
      } elseif (isset($_POST["submit"]) && $_POST["submit"] ==  "ADD") {       


        $retId = $_POST["new_column_1"];
        $rdate = $_POST["new_column_2"];
        $receiptId = $_POST["new_column_3"];
          
        $stmt = $connection->prepare("INSERT INTO returntransaction (retId, rdate, receiptId) VALUES (?,?,?)");
          
        // Bind the title and pub_id parameters, 'sss' indicates 3 strings
        $stmt->bind_param("isi", $retId, $rdate, $receiptId);
        
        // Execute the insert statement
        $stmt->execute();
          
        if($stmt->error) {       
          printf("<b>Error: %s.</b>\n", $stmt->error);
        } else {
          echo "<b>Successfully added item return ".$retId."</b>";
        }
      }
   }

    /****************************************************
     STEP 3: Display the list of return transactions
     ****************************************************/
    // Display each book title databaserow as a table row
    while($row = $result->fetch_assoc()){
        
       echo "<td>".$row['retid']."</td>";
       echo "<td>".$row['rdate']."</td>";
       echo "<td>".$row['receiptId']."</td>";
	   
       
       //Display an option to delete this title using the Javascript function and the hidden title_id
       echo "<a href=\"javascript:formSubmit('".$row['retid']."');\">DELETE</a>";
       echo "</td></tr>";
        
    }
    echo "</form>";

    // Close the connection to the database once we're done with it.
    mysqli_close($connection);
   }
   
?>

<h2>Entries in alphabetical order</h2>
<!-- Set up a table to view the book titles -->
<table border=0 cellpadding=0 cellspacing=0>
<!-- Create the table column headings -->

<tr valign=center>
<td class=rowheader>Column Header 1</td>
<td class=rowheader>Column Header 2</td>
<td class=rowheader>Column Header 3</td>
<td class=rowheader>Column Header 4</td>
<td class=rowheader>Column Header 5</td>
<td class=rowheader>Column Header 6</td>
<td class=rowheader>Column Header 7</td>
<td class=rowheader>Column Header 8</td>
</tr>

<?php
	showItem();
?>

</table>

<h2>Add a New Entry</h2>

<!--
  /****************************************************
   STEP 5: Build the form to add a book title
   ****************************************************/
    Use an HTML form POST to add a book, sending the parameter values back to this page.
    Avoid Cross-site scripting (XSS) by encoding PHP_SELF using htmlspecialchars.

    This is the simplest way to POST values to a web page. More complex ways involve using
    HTML elements other than a submit button (eg. by clicking on the delete link as shown above).
-->

<form id="add" name="add" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
    <table border=0 cellpadding=0 cellspacing=0>
        <tr><td>Parameter 1</td><td><input type="text" size=30 name="new_column_1"</td></tr>
        <tr><td>Parameter 2</td><td><input type="text" size=30 name="new_column_2"</td></tr>
        <tr><td>Parameter 3</td><td><input type="text" size=30 name="new_column_3"</td></tr>
        <tr><td>Parameter 4</td><td><input type="text" size=30 name="new_column_4"</td></tr>
        <tr><td>Parameter 5</td><td><input type="text" size=30 name="new_column_5"</td></tr>
        <tr><td>Parameter 6</td><td><input type="text" size=30 name="new_column_6"</td></tr>
        <tr><td>Parameter 7</td><td><input type="text" size=30 name="new_column_7"</td></tr>
        <tr><td>Parameter 8</td><td><input type="text" size=30 name="new_column_8"</td></tr>
        <tr><td></td><td><input type="submit" name="submit" border=0 value="ADD"></td></tr>
    </table>
</form>
</body>
</html>
