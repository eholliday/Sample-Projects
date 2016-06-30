<?php 

#name of thing - view_profiles.php
#Created May 10, 2016
#Created by Eleanor Holliday & Jeremey Hinz
#This page grabs all of the users from the users table

error_reporting(E_ALL & ~E_NOTICE);

$page_title = 'View Contacts';
include ('includes/header.html');
echo '<h1>Contacts</h1>';

require ('../mysqli_connect.php');

// Number of records to show per page:
$display = 10;

// Determine how many pages there are...
if (isset($_GET['p']) && is_numeric($_GET['p'])) { // Already been determined.
	$pages = $_GET['p'];
} else { // Need to determine.
 	// Count the number of records:
	$q = "SELECT COUNT(company_id) FROM company";
	$r = @mysqli_query ($dbc, $q);
	$row = @mysqli_fetch_array ($r, MYSQLI_NUM);
	$records = $row[0];
	// Calculate the number of pages...
	if ($records > $display) { // More than 1 page.
		$pages = ceil ($records/$display);
	} else {
		$pages = 1;
	}
} // End of p IF.

// Determine where in the database to start returning results...
if (isset($_GET['s']) && is_numeric($_GET['s'])) {
	$start = $_GET['s'];
} else {
	$start = 0;
}

// Determine the sort...
// Default is by registration date.
$sort = (isset($_GET['sort'])) ? $_GET['sort'] : 'rd';

// Determine the sorting order:
switch ($sort) {
	case 'bn':
		$order_by = 'brewer_name ASC';
		break;
	case 'bo':
		$order_by = 'brewer_owner ASC';
		break;
	case 'cn':
		$order_by = 'contact ASC';
		break;
	default:
		$order_by = 'brewer_name ASC';
		$sort = 'bn';
		break;
}
	
// Define the query:
$q = "SELECT company_id, brewer_name, brewer_owner, address, city, state, Country, contact, phone_number, email, zip_code, contact FROM company ORDER BY $order_by LIMIT $start, $display";		
$r = @mysqli_query ($dbc, $q); // Run the query.

// Table header:
echo '<table align="center" cellspacing="0" cellpadding="5" width="75%">
<tr>
	<td align="left"><b><a href="view_profiles.php?sort=ln">Company ID</a></b></td>
	<td align="left"><b><a href="view_profiles.php?sort=fn">Brewery Name</a></b></td>
	<td align="left"><b><a href=view_profiles.php?sort=rd">Brewery Owner</a></b></td>
	<td align="left"><b><a href="view_profiles.php?sort=ln">Address</a></b></td>
	<td align="left"><b><a href="view_profiles.php?sort=fn">City</a></b></td>
	<td align="left"><b><a href=view_profiles.php?sort=rd">State</a></b></td>
	<td align="left"><b><a href="view_profiles.php?sort=ln">Zip Code</a></b></td>
	<td align="left"><b><a href="view_profiles.php?sort=fn">Country</a></b></td>
	<td align="left"><b><a href="view_profiles.php?sort=ln">Contact</a></b></td>
	<td align="left"><b><a href="view_profiles.php?sort=fn">Phone Numer</a></b></td>
	<td align="left"><b><a href=view_profiles.php?sort=rd">Email Address</a></b></td>
</tr>
';

// Fetch and print all the records....
$bg = '#eeeeee'; 
while ($row = mysqli_fetch_array($r, MYSQLI_ASSOC)) {
	$bg = ($bg=='#eeeeee' ? '#ffffff' : '#eeeeee');
		echo '<tr bgcolor="' . $bg . '">
		
		<td align="center">' . $row['company_id'] . '</td>
		<td align="left">' . $row['brewer_name'] . '</td>
		<td align="left">' . $row['brewer_owner'] . '</td>
		<td align="left">' . $row['address'] . '</td>
		<td align="left">' . $row['city'] . '</td>
		<td align="left">' . $row['state'] . '</td>
		<td align="left">' . $row['zip_code'] . '</td>
		<td align="left">' . $row['Country'] . '</td>
		<td align="left">' . $row['contact'] . '</td>
		<td align="left">' . $row['phone_number'] . '</td>
		<td align="left">' . $row['email'] . '</td>
	</tr>
	';
} // End of WHILE loop.

echo '</table>';
mysqli_free_result ($r);
mysqli_close($dbc);

// Make the links to other pages, if necessary.
if ($pages > 1) {
	
	echo '<br /><p id="page_navigation" style="padding-top:600px">';
	$current_page = ($start/$display) + 1;
	
	// If it's not the first page, make a Previous button:
	if ($current_page != 1) {
		echo '<a href="view_profiles.php?s=' . ($start - $display) . '&p=' . $pages . '&sort=' . $sort . '">Previous</a> ';
	}
	
	// Make all the numbered pages:
	for ($i = 1; $i <= $pages; $i++) {
		if ($i != $current_page) {
			echo '<a href="view_profiles.php?s=' . (($display * ($i - 1))) . '&p=' . $pages . '&sort=' . $sort . '">' . $i . '</a> ';
		} else {
			echo $i . ' ';
		}
	} // End of FOR loop.
	
	// If it's not the last page, make a Next button:
	if ($current_page != $pages) {
		echo '<a href="view_profiles.php?s=' . ($start + $display) . '&p=' . $pages . '&sort=' . $sort . '">Next</a>';
	}
	
	echo '</p>'; // Close the paragraph.
	
} // End of links section.
	
include ('includes/footer.html');
?>