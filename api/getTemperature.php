<?php
/*
             __  __                             _
            |  \/  |                    /\     (_)
            | \  / |   __ _  __  __    /  \     _   _ __
            | |\/| |  / _` | \ \/ /   / /\ \   | | |  __|
            | |  | | | (_| |  >  <   / ____ \  | | | |
            |_|  |_|  \__,_| /_/\_\ /_/    \_\ |_| |_|
                    S M A R T   T H E R M O S T A T
*************************************************************************"
* MaxAir is a Linux based Central Heating Control systems. It runs from *"
* a web interface and it comes with ABSOLUTELY NO WARRANTY, to the      *"
* extent permitted by applicable law. I take no responsibility for any  *"
* loss or damage to you or your property.                               *"
* DO NOT MAKE ANY CHANGES TO YOUR HEATING SYSTEM UNTILL UNLESS YOU KNOW *"
* WHAT YOU ARE DOING                                                    *"
*************************************************************************"
*/

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

require_once(__DIR__.'../../st_inc/connection.php');
require_once(__DIR__.'../../st_inc/functions.php');

if(isset($_GET['sensorname'])) {
        $sensorname = $_GET['sensorname'];
        $query = "SELECT sensor_id, sensor_child_id FROM sensors where name = '{$sensorname}' LIMIT 1;";
        $results = $conn->query($query);
        $row = mysqli_fetch_assoc($results);
        if(! $row) {
                http_response_code(400);
                echo json_encode(array("success" => False, "state" => "No Sensor with that name found."));
         } else {
                $sensor_id=$row['sensor_id'];
                $child_id=$row['sensor_child_id'];

		//get the node_id for this sensor
		$query = "SELECT * FROM nodes WHERE id = ".$sensor_id." AND status IS NOT NULL LIMIT 1;";
		$result = $conn->query($query);
		$nodes = mysqli_fetch_assoc($result);
		if(! $nodes) {
			http_response_code(400);
			echo json_encode(array("success" => False, "state" => "No Matching Node found for this Sensor."));
		} else {
                	//query to get temperature from messages_in_view_24h table view
			$node_id=$nodes['node_id'];
                 	$query = "SELECT * FROM messages_in_view_24h WHERE node_id = '{$node_id}' AND child_id = {$child_id} LIMIT 1;";
                 	$result = $conn->query($query);
                 	$sensor = mysqli_fetch_array($result);
                 	if(! $sensor) {
                        	http_response_code(400);
                        	echo json_encode(array("success" => False, "state" => "Sensor has not reported in the last 24 hours."));
                  	} else {
                        	$sensor_temp = $sensor['payload'];
                        	$sensor_time = $sensor['datetime'];

                        	//query to get battery info from nodes_battery table
                        	$query = "SELECT * FROM nodes_battery WHERE node_id = '{$node_id}' ORDER BY id desc LIMIT 1;";
                        	$result = $conn->query($query);
                        	$node = mysqli_fetch_array($result);
                        	if(! $node) {
                                	http_response_code(200);
                                	echo json_encode(array("success" => True, "state" => $sensor_temp, "datetime" => $sensor_time));
                        	} else {
                                	$sensor_bat_voltage = $node['bat_voltage'];
                                	$sensor_bat_level = $node['bat_level'];
                                	http_response_code(200);
                                	echo json_encode(array("success" => True, "state" => $sensor_temp, "datetime" => $sensor_time, "bat_voltage" => $sensor_bat_voltage, "bat_level" => $sensor_bat_level));
                        	}
			}
                }
        }
} else {
        http_response_code(400);
        echo json_encode(array("success" => False, "state" => "Data is incomplete."));
}
?>

