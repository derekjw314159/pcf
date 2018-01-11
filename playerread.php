<?php

//require 'dbconfig.php';
require_once '/pcf/class.player.php';

$object = new PLAYER();

// Design initial table header
$data = '<table class="table table-bordered table-striped">
						<tr>
							<th>No.</th>
							<th>First Name</th>
							<th>Last Name</th>
							<th>Email Address</th>
							<th>Update</th>
							<th>Delete</th>
						</tr>';


$users = $object->Read(); 
// var_dump($users);
if (count($users) > 0) {
    $number = 1;
    foreach ($users as $user) {
        $data .= '<tr>
				<td>' . $number . '</td>
				<td>' . $user['playerFirstName'] . '</td>
				<td>' . $user['playerLastName'] . '</td>
				<td>' . $user['playerEmail1'] . '</td>
				<td>
					<button onclick="GetUserDetails(' . $user['playerID'] . ')" class="btn btn-warning">Update</button>
				</td>
				<td>
					<button onclick="DeleteUser(' . $user['playerID'] . ')" class="btn btn-danger">Delete</button>
				</td>
    		</tr>';
        $number++;
    }
} else {
    // records not found
    $data .= '<tr><td colspan="6">Records not found!</td></tr>';
}

$data .= '</table>';

echo $data;

?>
