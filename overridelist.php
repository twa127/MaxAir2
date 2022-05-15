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

require_once(__DIR__.'/st_inc/session.php');
confirm_logged_in();
require_once(__DIR__.'/st_inc/connection.php');
require_once(__DIR__.'/st_inc/functions.php');

$page_refresh = page_refresh($conn);
?>
<div class="panel panel-primary">
	<div class="panel-heading">
		<i class="bi bi-arrow-repeat icon-fw"></i>  <?php echo $lang['override']; ?>
                <div class="dropdown pull-right">
                        <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                                <i class="bi bi-file-earmark-pdf text-white bg-dark"></i>
                        </a>
                        <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="pdf_download.php?file=setup_override.pdf" target="_blank"><i class="bi bi-file-earmark-pdf icon-fw"></i><?php echo $lang['setup_override']; ?></a></li>
                        </ul>
                        <div class="btn-group" id="override_date"><?php echo '&nbsp;&nbsp;'.date("H:i"); ?></div>
                </div>
	</div>
	<!-- /.panel-heading -->
	<div class="panel-body">
		<ul class="chat">
			<?php
			if ((settings($conn, 'mode') & 0b1) == 0) { // Boiler Mode
				$query = "SELECT override.id, override.status, override.zone_id, zone.index_id, override.time, override.temperature FROM override join zone on override.zone_id = zone.id WHERE override.`purge` = '0' order by zone.index_id, override.temperature;";
				$results = $conn->query($query);
				while ($row = mysqli_fetch_assoc($results)) {
					//query to search location device_id
					$query = "SELECT * FROM zone_view WHERE id = {$row['zone_id']} LIMIT 1";
					$result = $conn->query($query);
					$pi_device = mysqli_fetch_array($result);
					$zone = $pi_device['name'];
					$type = $pi_device['type'];
			        	$category = $pi_device['category'];
					$zone_status = $pi_device['status'];
                			$sensor_type_id = $pi_device["sensor_type_id"];
					if ($zone_status != 0) {
						echo '
						<li class="left clearfix animated fadeIn">
							<a href="javascript:active_override('.$row["zone_id"].');">
								<span class="chat-img pull-left override">';
									if($row["status"]=="0"){ $shactive="bluesch"; $status="Off"; }else{ $shactive="orangesch"; $status="On"; }
									$unit = SensorUnits($conn,$row['sensor_type_id']);
		        		        			if ($category == 2) {
										echo '<div class="circle '. $shactive.'"><p class="schdegree">'.$row["temperature"].$unit.'</p></div>';
									} else {
		        	                				echo '<div class="circle '. $shactive.'"><p class="schdegree">'.number_format(DispSensor($conn,$row["temperature"],$sensor_type_id),0).$unit.'</p></div>';
									}
	        			        		echo '</span>
							</a>
        	                			<div class="chat-body clearfix">
		                	        		<div class="header">';
									if($row["status"]=="0" && $type=="Heating"){ $image = "radiator.png";  }
									elseif($row["status"]=="0" && $type=="Water"){ $image = "off_hot_water.png";  }
									elseif($row["status"]=="1" && $type=="Heating"){ $image = "radiator1.png";  }
									elseif($row["status"]=="1" && $type=="Water"){ $image = "hot_water.png"; }
									echo '<strong class="primary-font">&nbsp;&nbsp;'. $zone.' </strong><span class="pull-right text-muted small"><em> <img src="images/'.$image.'" border="0"></em></span>
									<br> &nbsp;&nbsp;'. $row['time']. '';
									echo '';
								echo '</div>
							</div>
						</li>';
					}
				}
			} else { // HVAC mode, 3 = fan, 4 = heat, 5 = cool
			        $query = "SELECT * FROM `override` WHERE `purge` = 0 ORDER BY `hvac_mode`;";
		        	$results = $conn->query($query);
			        while ($row = mysqli_fetch_assoc($results)) {
					$hvac_mode = $row['hvac_mode'];
			                //query to search location device_id
                			$query = "SELECT * FROM zone_view WHERE id = {$row['zone_id']} LIMIT 1";
		                	$result = $conn->query($query);
	                		$pi_device = mysqli_fetch_array($result);
			                $zone_status = $pi_device['status'];
                			$sensor_type_id = $pi_device["sensor_type_id"];
                			if ($zone_status != 0) {
		        	                echo '
                			        <li class="left clearfix animated fadeIn">
		                        		<a href="javascript:active_boost('.$row["id"].');">
	                		        		<span class="chat-img pull-left override">';
			                        			if($row["status"]=="0"){ $shactive="bluesch"; $status="Off"; }else{ $shactive="redsch"; $status="On"; }
									$unit = SensorUnits($conn,$row['sensor_type_id']);
			                			        if ($hvac_mode == 3) {
                        				        		echo '<div class="circle '. $shactive.'"><p class="schdegree"></p></div>';
						                        } else {
        	        					                echo '<div class="circle '. $shactive.'"><p class="schdegree">'.number_format(DispSensor($conn,$row["temperature"],$sensor_type_id),0).$unit.'</p></div>';
									}
                	                			echo '</span>
							</a>
		        	                        <div class="chat-body clearfix">
	                			                <div class="header">';
				                		        if($row["status"]=="0" && $hvac_mode == 3){ $pi_image = "hvac_fan_stop_30.png"; $device = 'FAN'; }
			        			                elseif($row["status"]=="1" && $hvac_mode == 3){ $pi_image = "hvac_fan_start_40.png"; $device = 'FAN'; }
			                			        elseif($row["status"]=="0" && $hvac_mode == 4){ $pi_image = "hvac_heat_off_30.png"; $device = 'HEAT'; }
		        				                elseif($row["status"]=="1" && $hvac_mode == 4){ $pi_image = "hvac_heat_on_30.png"; $device = 'HEAT'; }
									elseif($row["status"]=="0" && $hvac_mode == 5){ $pi_image = "hvac_cool_off_30.png"; $device = 'COOL'; }
				                		        elseif($row["status"]=="1" && $hvac_mode == 5){ $pi_image = "hvac_cool_on_30.png"; $device = 'COOL'; }
						                        echo '<strong class="primary-font">&nbsp;&nbsp;'. $zone.' </strong><span class="pull-right text-muted small"><em> <img src="images/'.$image.'" border="0"></em></span>
                			        			<br> &nbsp;&nbsp;'. $row['time']. '';
					        	                echo '';
                					        echo '</div>
							</div>
						</li>';
			                }
				}
			}
			?>
		</ul>
	</div>
	<!-- /.panel-body -->
	<div class="panel-footer">
	        <div class="btn-group" id="footer_weather">
                	<?php ShowWeather($conn); ?>
             	</div>
	</div>
	<!-- /.panel-footer -->
</div>
<!-- /.panel-primary -->
<?php if(isset($conn)) { $conn->close();} ?>
<script>
// update page data every x seconds
$(document).ready(function(){
  var delay = '<?php echo $page_refresh ?>';

  (function loop() {
    $('#override_date').load("ajax_fetch_data.php?id=0&type=13").fadeIn("slow");
    $('#footer_weather').load("ajax_fetch_data.php?id=0&type=14").fadeIn("slow");
    setTimeout(loop, delay);
  })();
});
</script>

