<!--
	TODO add error viewing area
	form and table to process the delivery of an order (it just sets the delivery date)
	-->

<html>
<head>
<meta content="text/html;charset=utf-8" http-equiv="Content-Type">
<meta content="utf-8" http-equiv="encoding">

<title>AMS Manager Interface</title>
<!--
    A simple stylesheet is provided so you can modify colours, fonts, etc.
-->
    <link href="ams.css" rel="stylesheet" type="text/css">

<!--
    Javascript to submit a title_id as a POST form, used with the "delete" links
-->
<script>
function formSubmit(titleId) {
    'use strict';
    if (confirm('Are you sure you want to delete this title?')) {
      // Set the value of a hidden HTML element in this form
      var form = document.getElementById('delete');
      form.title_id.value = titleId;
      // Post this form
      form.submit();
    }
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
<tr><td><div class="errorarea"> </div></td></tr>
</table>
</div>
<?php
    /****************************************************
     STEP 1: Connect to the bookbiz MySQL database
     ****************************************************/

	 // CHANGE this to connect to your own MySQL instance in the labs or on your own computer
    $connection = new mysqli("localhost:3307", "root", "", "bookbiz");

    // Check that the connection was successful, otherwise exit
    if (mysqli_connect_errno()) {
        printf("Connect failed: %s\n", mysqli_connect_error());
        exit();
    }
    /****************************************************
     STEP 2: Detect the user action

     Next, we detect what the user did to arrive at this page
     There are 3 possibilities 1) the first visit or a refresh,
     2) by clicking the Delete link beside a book title, or
     3) by clicking the bottom Submit button to add a book title
     
     NOTE We are using POST superglobal to safely pass parameters
        (as opposed to URL parameters or GET)
     ****************************************************/

    if ($_SERVER["REQUEST_METHOD"] == "POST") {

      if (isset($_POST["submitDelete"]) && $_POST["submitDelete"] == "DELETE") {
       /*
          Delete the selected book title using the title_id
        */
       
       // Create a delete query prepared statement with a ? for the title_id
       $stmt = $connection->prepare("DELETE FROM titles WHERE title_id=?");
       $deleteTitleID = $_POST['title_id'];
       // Bind the title_id parameter, 's' indicates a string value
       $stmt->bind_param("s", $deleteTitleID);
       
       // Execute the delete statement
       $stmt->execute();
          
       if($stmt->error) {
         printf("<b>Error: %s.</b>\n", $stmt->error);
       } else {
         echo "<b>Successfully deleted ".$deleteTitleID."</b>";
       }
            
      } elseif (isset($_POST["submit"]) && $_POST["submit"] ==  "ADD") {       
       /*
        Add a book title using the post variables title_id, title and pub_id.
        */
        $title_id = $_POST["new_title_id"];
        $title = $_POST["new_title"];
        $pub_id = $_POST["new_pub_id"];
		$price = $_POST["new_price"];
		$subtitle = $_POST["new_subtitle"];
          
        $stmt = $connection->prepare("INSERT INTO titles (title_id, title, pub_id, price, subtitle) VALUES (?,?,?,?,?)");
          
        // Bind the title and pub_id parameters, 'sss' indicates 3 strings
        $stmt->bind_param("sssis", $title_id, $title, $pub_id, $price, $subtitle);
        
        // Execute the insert statement
        $stmt->execute();
          
        if($stmt->error) {       
          printf("<b>Error: %s.</b>\n", $stmt->error);
        } else {
          echo "<b>Successfully added ".$title."</b>";
        }
      }
   }
?>

<h2>Book Titles in alphabetical order</h2>
<!-- Set up a table to view the book titles -->
<table border=0 cellpadding=0 cellspacing=0>
<!-- Create the table column headings -->

<tr valign=center>
<td class=rowheader>UPC</td>
<td class=rowheader>Title</td>
<td class=rowheader>Publisher ID</td>
<td class=rowheader>Price</td>
<td class=rowheader>Subtitle</td>
</tr>

<?php
    /****************************************************
     STEP 3: Select the most recent list of book titles
     ****************************************************/

   // Select all of the book rows columns title_id, title and pub_id
    if (!$result = $connection->query("SELECT title_id, title, pub_id, price, subtitle FROM titles ORDER BY title")) {
        die('There was an error running the query [' . $db->error . ']');
    }

    // Avoid Cross-site scripting (XSS) by encoding PHP_SELF (this page) using htmlspecialchars.
    echo "<form id=\"delete\" name=\"delete\" action=\"";
    echo htmlspecialchars($_SERVER["PHP_SELF"]);
    echo "\" method=\"POST\">";
    // Hidden value is used if the delete link is clicked
    echo "<input type=\"hidden\" name=\"title_id\" value=\"-1\"/>";
   // We need a submit value to detect if delete was pressed 
    echo "<input type=\"hidden\" name=\"submitDelete\" value=\"DELETE\"/>";


    /****************************************************
     STEP 4: Display the list of book titles
     ****************************************************/
    // Display each book title databaserow as a table row
    while($row = $result->fetch_assoc()){
        
       echo "<td>".$row['title_id']."</td>";
       echo "<td>".$row['title']."</td>";
       echo "<td>".$row['pub_id']."</td>";
	   echo "<td>$".$row['price']."</td>";
	   echo "<td>".$row['subtitle']."</td><td>";
       
       //Display an option to delete this title using the Javascript function and the hidden title_id
       echo "<a href=\"javascript:formSubmit('".$row['title_id']."');\">DELETE</a>";
       echo "</td></tr>";
        
    }
    echo "</form>";

    // Close the connection to the database once we're done with it.
    mysqli_close($connection);
?>

</table>


<!--
  /****************************************************
   STEP 5: Build the form to add a book title
   ****************************************************/
    Use an HTML form POST to add a book, sending the parameter values back to this page.
    Avoid Cross-site scripting (XSS) by encoding PHP_SELF using htmlspecialchars.

    This is the simplest way to POST values to a web page. More complex ways involve using
    HTML elements other than a submit button (eg. by clicking on the delete link as shown above).
-->
<table>
<tr>
<!-- add item form -->
<td>
<h2>Add a New Item:</h2>
<form id="add" name="add" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
    <table border=0 cellpadding=0 cellspacing=0>
        <tr><td>UPC:</td><td><input type="text" size=30 name="new_upc"</td></tr>
        <tr><td>Title:</td><td><input type="text" size=30 name="new_title"</td></tr>
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
        <tr><td>Date:</td><td><input type="text" size=30 name="date"</td></tr>
        <tr><td>How many?:</td><td><input type="text" size=30 name="topnum"</td></tr>
        <tr><td></td><td><input type="submit" name="submit_topsellers" border=0 value="Get Top Sellers"></td></tr>
    </table>
</form>
</td>

<!-- daily sales report -->
<td>
<h2>See Daily Sales Report:</h2>
<form id="daily" name="daily" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
    <table border=0 cellpadding=0 cellspacing=0>
        <tr><td>Date:</td><td><input type="text" size=30 name="date"</td></tr>
        <tr><td></td><td><input type="submit" name="submit_dailysales" border=0 value="Get Sales Report"></td></tr>
    </table>
</form>
</td>

<!-- view inventory -->
<td>
<h2>View Inventory:</h2>
<form id="view" name="view" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
    <table border=0 cellpadding=0 cellspacing=0>
        <tr><td></td><td><input type="submit" name="submit_viewinventory" border=0 value="Get Inventory Table"></td></tr>
    </table>
</form>
</td>

</tr>
</table>



</body>
</html>
