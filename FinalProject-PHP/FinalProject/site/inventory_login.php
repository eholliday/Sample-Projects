<?php # Script 12.13 - loggedin.php #3
// The user is redirected here from login.php.

//View inventory
$page_title = 'Inventory';
include ('includes/header.html');
echo '<h1>Inventory</h1>';

require ('../mysqli_connect.php');

// Number of records to show per page:
$display = 10;

// Determine how many pages there are...
if (isset($_GET['p']) && is_numeric($_GET['p'])) { // Already been determined.
	$pages = $_GET['p'];
} else { // Need to determine.
 	// Count the number of records:
	$q = "SELECT COUNT(inventory_id) FROM inventory";
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
$sort = (isset($_GET['sort'])) ? $_GET['sort'] : 'invid';

// Determine the sorting order:
switch ($sort) {
	case 'invid':
		$order_by = 'inventory_id ASC';
		break;
	case 'compid':
		$order_by = 'company_id ASC';
		break;
	case 'quant':
		$order_by = 'quantity ASC';
		break;
	default:
		$order_by = 'inventory_id ASC';
		$sort = 'invid';
		break;
}

// Define the query:
$q = "SELECT inventory.inventory_id, inventory.beverage_name, inventory.beverage_type, inventory.company_id, company.brewer_name, inventory.quantity, beverage.beverage 
FROM inventory INNER JOIN company ON company.company_id = inventory.company_id 
INNER JOIN beverage ON beverage.beverage_type = inventory.beverage_type ORDER BY $order_by LIMIT $start, $display";		
$r = @mysqli_query ($dbc, $q); // Run the query.

// Table header:
echo '<table align="center" cellspacing="0" cellpadding="5" width="75%">
<tr>
	<td align="left"><b><a href="loggedin.php?sort=fn">Inventory ID</a></b></td>
	<td align="left"><b><a href="loggedin.php?sort=ln">Beverage Name</a></b></td>
	<td align="left"><b><a href="loggedin.php?sort=fn">Beverage Type</a></b></td>
	<td align="left"><b><a href="loggedin.php?sort=fn">Company Name</a></b></td>
	<td align="left"><b><a href="loggedin.php?sort=rd">Quantity</a></b></td>
	<td align="left"><b><font color="white">Edit</font></b></td>
</tr>
';

// Fetch and print all the records....
$bg = '#eeeeee'; 
while ($row = mysqli_fetch_array($r, MYSQLI_ASSOC)) {
	$bg = ($bg=='#eeeeee' ? '#ffffff' : '#eeeeee');
		echo '<tr bgcolor="' . $bg . '">
		
		<td align="center">' . $row['inventory_id'] . '</td>
		<td align="left">' . $row['beverage_name'] . '</td>
		<td align="left">' . $row['beverage'] . '</td>
		<td align="left">' . $row['brewer_name'] . '</td>
		<td align="left">' . $row['quantity'] . '</td>
		<td align="left"><a href="edit_inventory.php?id=' . $row['inventory_id'] . '"><font color="#1B78DC">Edit</font></a></td>
	</tr>
	';
} // End of WHILE loop.

echo '</table>';
mysqli_free_result ($r);
mysqli_close($dbc);

// Make the links to other pages, if necessary.
if ($pages > 1) {
	
	echo '<br /><p style="padding-top:600px">';
	$current_page = ($start/$display) + 1;
	
	// If it's not the first page, make a Previous button:
	if ($current_page != 1) {
		echo '<a href="inventory_login.php?s=' . ($start - $display) . '&p=' . $pages . '&sort=' . $sort . '">Previous</a> ';
	}
	
	// Make all the numbered pages:
	for ($i = 1; $i <= $pages; $i++) {
		if ($i != $current_page) {
			echo '<a href="inventory_login.php?s=' . (($display * ($i - 1))) . '&p=' . $pages . '&sort=' . $sort . '">' . $i . '</a> ';
		} else {
			echo $i . ' ';
		}
	} // End of FOR loop.
	
	// If it's not the last page, make a Next button:
	if ($current_page != $pages) {
		echo '<a href="inventory_login.php?s=' . ($start + $display) . '&p=' . $pages . '&sort=' . $sort . '">Next</a>';
	}
	
	echo '</p>'; // Close the paragraph.
	
} // End of links section.

include ('includes/footer.html');
?>