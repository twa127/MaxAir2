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
		<i class="bi bi-send-fill" style="font-size: 1.2rem;"></i> <?php echo $lang['holidays']; ?>
			<div class="pull-right"> <div class="btn-group" id="holiday_date"><?php echo date("H:i"); ?></div> </div>
     	</div>
        <!-- /.panel-heading -->
 	<div class="panel-body">
 		<ul class="chat">
 		<li class="left clearfix">
                <a href="holiday.php" style="color: #777; cursor: pointer;" ><span class="chat-img pull-left">
                <div class="circle orangesch"> <i class="ionicons ion-plus"></i> </div>
                </span>
                <div class="chat-body clearfix">
                	<div class="header">
                       		<strong class="primary-font">   </strong>
				<small class="pull-right text-muted">
				<?php echo $lang['holidays_add']; ?> <i class="bi bi-chevron-right"></i></a>
                             	</small>
                         </div>
                </div>
                </li>
		<?php

		//get the current holidays
		$query = "SELECT * FROM holidays ORDER BY start_date_time asc";
		$hol_results = $conn->query($query);
		while ($hol_row = mysqli_fetch_assoc($hol_results)) {
			echo '
			<li class="left clearfix holidaysli">
			<a href="javascript:active_holidays('.$hol_row["id"].');">

			<span class="chat-img pull-left">';
			if($hol_row["status"]=="0"){ $shactive="bluesch"; }else{ $shactive="orangesch"; }
			$time = strtotime(date("G:i:s"));
			$start_date_time = strtotime($hol_row['start_date_time']);
			$end_date_time = strtotime($hol_row['end_date_time']);
			if ($time >$start_date_time && $time <$end_date_time && $hol_row["status"]=="1"){$shactive="redsch";}
			echo '<div class="circle '. $shactive.'"> <i class="bi bi-send-fill blueinfo"></i></div>
                     	</span></a>

			<a style="color: #333; cursor: pointer; text-decoration: none;" data-toggle="collapse" data-parent="#accordion" href="#collapse'.$hol_row['id'].'">

			<a href="scheduling.php?hol_id='.$hol_row["id"].'" style="color: #777; cursor: pointer;" ><small class="pull-right text-muted">
			'.$lang['schedule_add'].' <i class="bi bi-chevron-right"></i></a>
			</small>

			<div class="header">
				<div class="text-info">&nbsp;&nbsp;&nbsp;&nbsp;From: '.$hol_row['start_date_time'].' </div> 
				<div class="text-info">&nbsp;&nbsp;&nbsp;&nbsp;Until:&nbsp; '.$hol_row['end_date_time'].'</div></a>


				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				<a href="javascript:delete_holidays('.$hol_row["id"].');"><button class="btn btn-danger btn-xs" data-toggle="confirmation" data-title="ARE YOU SURE?" data-content="You are about to DELETE this SCHEDULE"><span class="bi bi-trash-fill"></span></button> </a>
				<a href="holiday.php?id='.$hol_row["id"].'" class="btn btn-default btn-xs login"><span class="ionicons ion-edit"></span></a>
        			<a href="scheduling.php?hol_id='.$hol_row["id"].'" class="btn btn-default btn-xs login"><span class="bi bi-clock"></span></a>
    			</div></li>';
			//following variable set to 0 on start for array index. 
			$sch_time_index = '0';
			//$query = "SELECT time_id, time_status, `start`, `end`, tz_id, tz_status, zone_id, index_id, zone_name, temperature, max(temperature) as max_c FROM schedule_daily_time_zone_view group by time_id ORDER BY start asc";
			$query = "SELECT time_id, time_status, `start`, `end`, WeekDays,tz_id, tz_status, zone_id, index_id, zone_name, temperature, FORMAT(max(temperature),2) as max_c,
				sch_name, sensor_type_id
				FROM schedule_daily_time_zone_view
				WHERE holidays_id = {$hol_row["id"]} group by time_id ORDER BY start, sch_name asc";
			$results = $conn->query($query);
			if ($rowcount=mysqli_num_rows($results) > 0) {
				$hol_params = [];
				while ($row = mysqli_fetch_assoc($results)) {
				        if($row["WeekDays"]  & (1 << 0)){ $Sunday_status_icon="ion-checkmark-circled"; $Sunday_status_color="orangefa"; }else{ $Sunday_status_icon="ion-close-circled"; $Sunday_status_color="bluefa"; }
				        if($row["WeekDays"]  & (1 << 1)){ $Monday_status_icon="ion-checkmark-circled"; $Monday_status_color="orangefa"; }else{ $Monday_status_icon="ion-close-circled"; $Monday_status_color="bluefa"; }
				        if($row["WeekDays"]  & (1 << 2)){ $Tuesday_status_icon="ion-checkmark-circled"; $Tuesday_status_color="orangefa"; }else{ $Tuesday_status_icon="ion-close-circled"; $Tuesday_status_color="bluefa"; }
				        if($row["WeekDays"]  & (1 << 3)){ $Wednesday_status_icon="ion-checkmark-circled"; $Wednesday_status_color="orangefa"; }else{ $Wednesday_status_icon="ion-close-circled"; $Wednesday_status_color="bluefa"; }
			        	if($row["WeekDays"]  & (1 << 4)){ $Thursday_status_icon="ion-checkmark-circled"; $Thursday_status_color="orangefa"; }else{ $Thursday_status_icon="ion-close-circled"; $Thursday_status_color="bluefa"; }
				        if($row["WeekDays"]  & (1 << 5)){ $Friday_status_icon="ion-checkmark-circled"; $Friday_status_color="orangefa"; }else{ $Friday_status_icon="ion-close-circled"; $Friday_status_color="bluefa"; }
				        if($row["WeekDays"]  & (1 << 6)){ $Saturday_status_icon="ion-checkmark-circled"; $Saturday_status_color="orangefa"; }else{ $Saturday_status_icon="ion-close-circled"; $Saturday_status_color="bluefa"; }

					if($row["time_status"]=="0"){ $shactive="bluesch"; }else{ $shactive="orangesch"; }
					$time = strtotime(date("G:i:s"));
					$start_time = strtotime($row['start']);
					$end_time = strtotime($row['end']);
					if($row["WeekDays"]  & (1 << idate('w'))){if ($time >$start_time && $time <$end_time && $row["time_status"]=="1"){$shactive="redsch";}}

					//time shchedule listing
					$unit = SensorUnits($conn,$row['sensor_type_id']);
					echo '
					<div class="header">
						<li class="left clearfix scheduleli animated fadeIn">
						<a href="javascript:active_schedule('.$row["time_id"].');">
							<span class="chat-img pull-left" id="hol_status_'.$row["time_id"].'">
								<div class="circle '. $shactive.'">';
		                                                        if($row["category"] <> 2 && $row["sensor_type_id"] <> 3 && $row["tz_status"] == 1) {
                		                                                if ($row["tz_status"] == 1 || ($row["tz_status"] == 0 && $row["time_status"] == 1)) {
                                		                                        $unit = SensorUnits($conn,$row['sensor_type_id']);
                                                			                echo '<p class="schdegree">' . DispSensor($conn, number_format($row["max_c"], 1), $row["sensor_type_id"]) . $unit . '</p>';
                                                                		}
                                                        		}
								echo '</div>
							</span>
						</a>
						<a style="color: #333; cursor: pointer; text-decoration: none;" data-toggle="collapse" data-parent="#accordion" href="#collapse'.$row['tz_id'].'">
						<div class="chat-body clearfix">
							<div class="header text-info">&nbsp;&nbsp; <span class="label label-info">' . $row['sch_name'] . '</span><br>&nbsp;&nbsp; '. $row['start'] . ' - ' . $row['end'] . ' &nbsp;&nbsp;
								<small class="pull-right pull-right-days">
								&nbsp;&nbsp;&nbsp;&nbsp;S&nbsp;&nbsp;&nbsp;M&nbsp;&nbsp;&nbsp;T&nbsp;&nbsp;W&nbsp;&nbsp;&nbsp;T&nbsp;&nbsp;&nbsp;F&nbsp;&nbsp;&nbsp;S<br>
								&nbsp;&nbsp;&nbsp;
								<i class="ionicons '.$Sunday_status_icon.' icon-lg '.$Sunday_status_color.'"></i>
								<i class="ionicons '.$Monday_status_icon.' icon-lg '.$Monday_status_color.'"></i>
								<i class="ionicons '.$Tuesday_status_icon.' icon-lg '.$Tuesday_status_color.'"></i>
								<i class="ionicons '.$Wednesday_status_icon.' icon-lg '.$Wednesday_status_color.'"></i>
								<i class="ionicons '.$Thursday_status_icon.' icon-lg '.$Thursday_status_color.'"></i>
								<i class="ionicons '.$Friday_status_icon.' icon-lg '.$Friday_status_color.'"></i>
								<i class="ionicons '.$Saturday_status_icon.' icon-lg '.$Saturday_status_color.'"></i>

								</small>
							</div>
						</div></a>
						<div id="collapse'.$row["tz_id"].'" class="panel-collapse collapse">
							<br>';

							//zone listing of each time schedule
							$query="SELECT * FROM  schedule_daily_time_zone_view WHERE time_id = {$row['time_id']} order by index_id";
							$result = $conn->query($query);
							while ($datarw=mysqli_fetch_array($result)) {
								if($datarw["tz_status"]=="0"){ $status_icon="ion-close-circled"; $status_color="bluefa"; }else{ $status_icon="ion-checkmark-circled"; $status_color="orangefa"; }
								$unit = SensorUnits($conn,$datarw['sensor_type_id']);
								echo '
								<div class="list-group">
									<div class="list-group-item">
										<i class="ionicons '.$status_icon.' icon-lg '.$status_color.'"></i>  '.$datarw['zone_name'].'<span class="pull-right text-muted small"><em>'.number_format(DispSensor($conn,$datarw['temperature'],$datarw['sensor_type_id']),1). $unit .'</em></span>
									</div>';
								}

								//delete and edit button for each schedule
								echo '
								<div class="list-group-item">
									<a href="javascript:delete_schedule('.$row["time_id"].');"><button class="btn btn-danger btn-xs" data-toggle="confirmation" data-title="ARE YOU SURE?" data-content="You are about to DELETE this SCHEDULE"><span class="bi bi-trash-fill"></span></button> </a>	
									<a href="scheduling.php?id='.$row["time_id"].'&hol_id='.$hol_row["id"].'" class="btn btn-default btn-xs login"><span class="ionicons ion-edit"></span></a>
								</div>
							</div>
						<!-- /.panel-colapse -->
 						</div>
			 		</div>
					<!-- /.header -->
					</li>';
					//calculate total time of day schedule using array schedule_time with index as sch_time_index variable
					if($row["time_status"]=="1"){
						$total_time=$end_time-$start_time;
						$total_time=$total_time/60;
						//save all total_time variable value to schedule_time array and incriment array index (sch_time_index)
						$schedule_time[$sch_time_index] = $total_time;
						$sch_time_index = $sch_time_index+1;
					}
				} //end of schedule time while loop
			} // end if rowcount
			$hol_params[] = array('hol_id' =>$hol_row["id"]);
		} //end of while loop
		$js_hol_params = json_encode($hol_params);
		?>
		</ul>
	</div>
        <!-- /.panel-body -->
	<div class="panel-footer">
                <div class="btn-group" id="footer_weather">
                        <?php ShowWeather($conn); ?>
                </div>
                <div class="pull-right" id="footer_running_time">
				<?php
				echo '<i class="ionicons ion-ios-clock-outline"></i> Holiday Schedule: '.secondsToWords((array_sum($schedule_time)*60));
				?>
                        </div>
		</div>
	</div>
	<!-- /.panel-footer -->
</div>
<!-- /.panel-primary -->

<?php if(isset($conn)) { $conn->close();} ?>
<script>
$('[data-toggle=confirmation]').confirmation({
  rootSelector: '[data-toggle=confirmation]',
  container: 'body'
});

// update page data every x seconds
$(document).ready(function(){
  var delay = '<?php echo $page_refresh ?>';

  (function loop() {
    var data = '<?php echo $js_hol_params ?>';
    if (data.length > 0) {
            var obj = JSON.parse(data)
            //console.log(obj.length);

                for (var y = 0; y < obj.length; y++) {
                  $('#hol_status_' + obj[y].hol_id).load("ajax_fetch_data.php?id=" + obj[y].hol_id + "&type=19").fadeIn("slow");
                  //console.log(obj[y].hol_id);
                }
    }

    $('#holiday_date').load("ajax_fetch_data.php?id=0&type=13").fadeIn("slow");
    $('#footer_weather').load("ajax_fetch_data.php?id=0&type=14").fadeIn("slow");
    $('#footer_running_time').load("ajax_fetch_data.php?id=0&type=17").fadeIn("slow");
    setTimeout(loop, delay);
  })();
});
</script>
