<?php 

#name of thing - edit_inventory.php
#Created May 10, 2016
#Created by Eleanor Holliday & Jeremy Hinz 
#This page edits the records for a user and is accessed through view_users.php

error_reporting(E_ALL & ~E_NOTICE);

$page_title = 'Edit Inventory';
include ('includes/header.html');
echo '<h1>Edit Inventory</h1>';

// Check for a valid user ID, through GET or POST:
if ( (isset($_GET['id'])) && (is_numeric($_GET['id'])) ) { // From loggedin.php
	$id = $_GET['id'];
} elseif ( (isset($_POST['id'])) && (is_numeric($_POST['id'])) ) { // Form submission.
	$id = $_POST['id'];
} else { // No valid ID, kill the script.
	echo '<p class="error">This page has been accessed in error.</p>';
	include ('includes/footer.html'); 
	exit();
}

require ('../mysqli_connect.php'); 

// Check if the form has been submitted:
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

	$errors = array();
	
	if(isset($_REQUEST['beverage_types']) && $_REQUEST['beverage_types'] == '0') { 
		echo 'Please select a beverage type.'; 
	}else{
		$bt = mysqli_real_escape_string($dbc, trim($_POST['beverage_types']));
	} 
	
	// Check for a beverage_name:
	if (empty($_POST['beverage_name'])) {
		$errors[] = 'You forgot to enter a beverage name.';
	} else {
		$bn = mysqli_real_escape_string($dbc, trim($_POST['beverage_name']));
	}
	/*
	// Check for an company id:
	if (empty($_POST['company_name'])) {
		$errors[] = 'You forgot to enter a company name.';
	} else {
		$cid = mysqli_real_escape_string($dbc, trim($_POST['company_name']));
	}
	*/
	// Check for a product quantity:
	if (empty($_POST['quantity'])) {
		$errors[] = 'You forgot to enter a product quantity.';
		var_dump($_POST);
	} else {
		$qt = mysqli_real_escape_string($dbc, trim($_POST['quantity']));
	}
	
		
	if (empty($errors)) { // If everything's OK.
	
		// Make the query:
		//UPDATE `inventory` SET `quantity` = '367' WHERE `inventory`.`inventory_id` = 23;

		$bt = $_POST['beverage_types'];
		$bn = $_POST['beverage_name'];
		$qt = $_POST['quantity'];
		
		//echo $bt;
		
	switch ($bt) {
		case 'domestic_beer':
			$bid = 1;
			break;
		case 'foreign_beer':
			$bid = 2;
			break;
		case 'domestic_wine':
			$bid = 3;
			break;
		case 'foreign_wine':
			$bid = 4;
			break;
		case 'domestic_liquour':
			$bid = 5;
			break;
		case 'foreign_liquour':
			$bid = 6;
			break;
	}
			//echo $bid; 
		
			$q1 = "UPDATE inventory SET beverage_name=null,quantity=null, beverage_type=null WHERE inventory_id=$id LIMIT 1";
			$r = @mysqli_query ($dbc, $q1);
		
			$q = "UPDATE inventory SET beverage_name='$bn',quantity='$qt', beverage_type='$bid' WHERE inventory_id=$id LIMIT 1";
			$r = @mysqli_query ($dbc, $q);
			if (mysqli_affected_rows($dbc) == 1) { // If it ran OK.
			
			// Print a message:
				echo '<p>The product has been edited.</p>';	
				
			} else { // If it did not run OK.
				echo '<p class="error">The item could not be edited due to a system error. We apologize for any inconvenience.</p>'; // Public message.
				echo '<p>' . mysqli_error($dbc) . '<br />Query: ' . $q . '</p>'; // Debugging message.
			}
		
	} else { // Report the errors.

		echo '<p class="error">The following error(s) occurred:<br />';
		foreach ($errors as $msg) { // Print each error.
			echo " - $msg<br />\n";
		}
		echo '</p><p>Please try again.</p>';
	
	} // End of if (empty($errors)) IF.

} // End of submit conditional.

// Always show the form...

/*
foreach ($savedareaslist as $savedarea) {
        $output .= "<option ".($savedarea==$adcontact_country?'selected="selected" ':'')."value=\"" . esc_attr($savedarea) . "\">" . stripslashes($savedarea) . "</option>";
    }

*/

// Retrieve the user's information:
$q = "SELECT beverage_name, beverage_type, inventory_id, company_id, quantity FROM inventory WHERE inventory_id=$id";		
$r = @mysqli_query ($dbc, $q);

if (mysqli_num_rows($r) == 1) { // Valid user ID, show the form.

	// Get the user's information: <p>Company Name: <input type="text" name="company_name" size="15" maxlength="30" value="' . $row[1] . '" /></p>
	$row = mysqli_fetch_array ($r, MYSQLI_NUM);
	
	// Create the form:
	echo '<form action="edit_inventory.php" method="post">

<p>Beverage Name: <input type="text" name="beverage_name" size="50" maxlength="50" value="' . $row[0] . '" /></p>
<p>Select the type: 
<select name="beverage_types" id="beverage_types">
<option selected="selected">Please Select an Option</option>
<option value="domestic_beer">Domestic Beer</option>
<option value="foreign_beer">Foreign Beer</option>
<option value="domestic_wine">Domestic Wine</option>
<option value="foreign_wine">Foreign Wine</option>
<option value="domestic_liquour">Domestic Liquour</option>
<option value="foreign_liquour">Foreign Liquour</option></select>
</p>
<p>Quantity: <input type="text" name="quantity" size="20" maxlength="60" value="' . $row[4] . '"  /></p>
<p><input type="submit" name="submit" value="Submit" /></p>
<input type="hidden" name="id" value="' . $id . '" />
<p><a href="inventory_login.php">Return to Inventory Page</a></p>
</form>';

} else { // Not a valid user ID.
	echo '<p class="error">This page has been accessed in error.</p>';
}

		
include ('includes/footer.html');
?>