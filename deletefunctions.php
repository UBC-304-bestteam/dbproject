 <?php
function deleteACustomer($cid){
   
	// Connect to the database
	// Make sure to change these to match your mysqldatabase
	define('sqlServerAddress', "127.0.0.1:3306");
	define('sqlUsername', "root");
	define('sqlPassword', "");
	define('sqlServerName', "practice");

    $connection = new mysqli(sqlServerAddress, sqlUsername, sqlPassword, sqlServerName);


    // Check that the connection was successful, otherwise exit
    if (mysqli_connect_errno()) {
        writeMessage("Could not connect to database");
        exit();
    }
   
   // Select all of the book rows columns title_id, title and pub_id
    if (!$result = $connection->query('DELETE FROM customer WHERE cid ="$cid"')) {
        writeMessage("There was an error running the query. Error Message: $connection->error");
    }

    // Avoid Cross-site scripting (XSS) by encoding PHP_SELF (this page) using htmlspecialchars.
    echo "<form id=\"delete\" name=\"delete\" action=\"";
    echo htmlspecialchars($_SERVER["PHP_SELF"]);
    echo "\" method=\"POST\">";

    // Close the connection to the database once we're done with it.
    mysqli_close($connection);
   }
   
function deleteAHassong($upc, $title){
   
	// Connect to the database
	// Make sure to change these to match your mysqldatabase
	define('sqlServerAddress', "127.0.0.1:3306");
	define('sqlUsername', "root");
	define('sqlPassword', "");
	define('sqlServerName', "practice");

    $connection = new mysqli(sqlServerAddress, sqlUsername, sqlPassword, sqlServerName);


    // Check that the connection was successful, otherwise exit
    if (mysqli_connect_errno()) {
        writeMessage("Could not connect to database");
        exit();
    }
   
   // Select all of the book rows columns title_id, title and pub_id
    if (!$result = $connection->query('DELETE FROM hassong WHERE upc="$upc" AND title="$title"')) {
        writeMessage("There was an error running the query. Error Message: $connection->error");
    }

    // Avoid Cross-site scripting (XSS) by encoding PHP_SELF (this page) using htmlspecialchars.
    echo "<form id=\"delete\" name=\"delete\" action=\"";
    echo htmlspecialchars($_SERVER["PHP_SELF"]);
    echo "\" method=\"POST\">";

    // Close the connection to the database once we're done with it.
    mysqli_close($connection);
   }
   
   function deleteAItem($upc){
   
	// Connect to the database
	// Make sure to change these to match your mysqldatabase
	define('sqlServerAddress', "127.0.0.1:3306");
	define('sqlUsername', "root");
	define('sqlPassword', "");
	define('sqlServerName', "practice");

    $connection = new mysqli(sqlServerAddress, sqlUsername, sqlPassword, sqlServerName);


    // Check that the connection was successful, otherwise exit
    if (mysqli_connect_errno()) {
        writeMessage("Could not connect to database");
        exit();
    }
   
   // Select all of the book rows columns title_id, title and pub_id
    if (!$result = $connection->query('DELETE FROM item WHERE upc="$upc"')) {
        writeMessage("There was an error running the query. Error Message: $connection->error");
    }

    // Avoid Cross-site scripting (XSS) by encoding PHP_SELF (this page) using htmlspecialchars.
    echo "<form id=\"delete\" name=\"delete\" action=\"";
    echo htmlspecialchars($_SERVER["PHP_SELF"]);
    echo "\" method=\"POST\">";

    // Close the connection to the database once we're done with it.
    mysqli_close($connection);
   }
   
   function deleteALeadsinger($upc, $singername){
   
	// Connect to the database
	// Make sure to change these to match your mysqldatabase
	define('sqlServerAddress', "127.0.0.1:3306");
	define('sqlUsername', "root");
	define('sqlPassword', "");
	define('sqlServerName', "practice");

    $connection = new mysqli(sqlServerAddress, sqlUsername, sqlPassword, sqlServerName);


    // Check that the connection was successful, otherwise exit
    if (mysqli_connect_errno()) {
        writeMessage("Could not connect to database");
        exit();
    }
   
   // Select all of the book rows columns title_id, title and pub_id
    if (!$result = $connection->query('DELETE FROM leadsinger WHERE upc="$upc" AND singer_name="$singername"')) {
        writeMessage("There was an error running the query. Error Message: $connection->error");
    }

    // Avoid Cross-site scripting (XSS) by encoding PHP_SELF (this page) using htmlspecialchars.
    echo "<form id=\"delete\" name=\"delete\" action=\"";
    echo htmlspecialchars($_SERVER["PHP_SELF"]);
    echo "\" method=\"POST\">";

    // Close the connection to the database once we're done with it.
    mysqli_close($connection);
   }
   
   function deleteAOrder($receiptId){
   
	// Connect to the database
	// Make sure to change these to match your mysqldatabase
	define('sqlServerAddress', "127.0.0.1:3306");
	define('sqlUsername', "root");
	define('sqlPassword', "");
	define('sqlServerName', "practice");

    $connection = new mysqli(sqlServerAddress, sqlUsername, sqlPassword, sqlServerName);


    // Check that the connection was successful, otherwise exit
    if (mysqli_connect_errno()) {
        writeMessage("Could not connect to database");
        exit();
    }
   
   // Select all of the book rows columns title_id, title and pub_id
    if (!$result = $connection->query('DELETE FROM orders WHERE receiptId="$receiptId"')) {
        writeMessage("There was an error running the query. Error Message: $connection->error");
    }

    // Avoid Cross-site scripting (XSS) by encoding PHP_SELF (this page) using htmlspecialchars.
    echo "<form id=\"delete\" name=\"delete\" action=\"";
    echo htmlspecialchars($_SERVER["PHP_SELF"]);
    echo "\" method=\"POST\">";

    // Close the connection to the database once we're done with it.
    mysqli_close($connection);
   }
   
   function deleteAPurchaseitem($receiptId, $upc){
   
	// Connect to the database
	// Make sure to change these to match your mysqldatabase
	define('sqlServerAddress', "127.0.0.1:3306");
	define('sqlUsername', "root");
	define('sqlPassword', "");
	define('sqlServerName', "practice");

    $connection = new mysqli(sqlServerAddress, sqlUsername, sqlPassword, sqlServerName);


    // Check that the connection was successful, otherwise exit
    if (mysqli_connect_errno()) {
        writeMessage("Could not connect to database");
        exit();
    }
   
   // Select all of the book rows columns title_id, title and pub_id
    if (!$result = $connection->query('DELETE FROM purchaseitem WHERE upc="$upc" AND receiptId="$receiptId"')) {
        writeMessage("There was an error running the query. Error Message: $connection->error");
    }

    // Avoid Cross-site scripting (XSS) by encoding PHP_SELF (this page) using htmlspecialchars.
    echo "<form id=\"delete\" name=\"delete\" action=\"";
    echo htmlspecialchars($_SERVER["PHP_SELF"]);
    echo "\" method=\"POST\">";

    // Close the connection to the database once we're done with it.
    mysqli_close($connection);
   }
   
   function deleteAReturnitem($retid, $upc){
   
	// Connect to the database
	// Make sure to change these to match your mysqldatabase
	define('sqlServerAddress', "127.0.0.1:3306");
	define('sqlUsername', "root");
	define('sqlPassword', "");
	define('sqlServerName', "practice");

    $connection = new mysqli(sqlServerAddress, sqlUsername, sqlPassword, sqlServerName);


    // Check that the connection was successful, otherwise exit
    if (mysqli_connect_errno()) {
        writeMessage("Could not connect to database");
        exit();
    }
   
   // Select all of the book rows columns title_id, title and pub_id
    if (!$result = $connection->query('DELETE FROM hassong WHERE upc="$upc" AND retid="$retid"')) {
        writeMessage("There was an error running the query. Error Message: $connection->error");
    }

    // Avoid Cross-site scripting (XSS) by encoding PHP_SELF (this page) using htmlspecialchars.
    echo "<form id=\"delete\" name=\"delete\" action=\"";
    echo htmlspecialchars($_SERVER["PHP_SELF"]);
    echo "\" method=\"POST\">";

    // Close the connection to the database once we're done with it.
    mysqli_close($connection);
   }
   
   function deleteAReturntransaction($retId){
   
	// Connect to the database
	// Make sure to change these to match your mysqldatabase
	define('sqlServerAddress', "127.0.0.1:3306");
	define('sqlUsername', "root");
	define('sqlPassword', "");
	define('sqlServerName', "practice");

    $connection = new mysqli(sqlServerAddress, sqlUsername, sqlPassword, sqlServerName);


    // Check that the connection was successful, otherwise exit
    if (mysqli_connect_errno()) {
        writeMessage("Could not connect to database");
        exit();
    }
   
   // Select all of the book rows columns title_id, title and pub_id
    if (!$result = $connection->query('DELETE FROM returntransaction WHERE retid="$retid"')) {
        writeMessage("There was an error running the query. Error Message: $connection->error");
    }

    // Avoid Cross-site scripting (XSS) by encoding PHP_SELF (this page) using htmlspecialchars.
    echo "<form id=\"delete\" name=\"delete\" action=\"";
    echo htmlspecialchars($_SERVER["PHP_SELF"]);
    echo "\" method=\"POST\">";

    // Close the connection to the database once we're done with it.
    mysqli_close($connection);
   }
   ?>