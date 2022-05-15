<?php 
#!/usr/bin/php
echo "\033[36m";
echo "\n";
echo "           __  __                             _         \n";
echo "          |  \/  |                    /\     (_)        \n";
echo "          | \  / |   __ _  __  __    /  \     _   _ __  \n";
echo "          | |\/| |  / _` | \ \/ /   / /\ \   | | | '__| \n";
echo "          | |  | | | (_| |  >  <   / ____ \  | | | |    \n";
echo "          |_|  |_|  \__,_| /_/\_\ /_/    \_\ |_| |_|    \n";
echo " \033[0m \n";
echo "                \033[45m S M A R T   T H E R M O S T A T \033[0m \n";
echo "\033[31m";
echo "***************************************************************\n";
echo "*   MaxAir Datase Script Version 0.01 Build Date 20/12/2020   *\n";
echo "*   Last Modified on 20/12/2020                               *\n";
echo "*                                      Have Fun - PiHome.eu   *\n";
echo "***************************************************************\n";
echo "\033[0m";
echo "\033[36m".date('Y-m-d H:i:s'). "\033[0m - MaxAir Database Install Script Started \n"; 
$line = "--------------------------------------------------------------- \n";

//Set php script execution time in seconds
ini_set('max_execution_time', 400); 
$date_time = date('Y-m-d H:i:s');
echo $line;
//Check php version before doing anything else 
$version = explode('.', PHP_VERSION);
if ($version[0] > 7){
	echo "\033[36m".date('Y-m-d H:i:s'). "\033[0m - MaxAir Supported on php version 5.x or above you are running version \033[41m".phpversion()."\033[0m \n"; 
	echo "\033[36m".date('Y-m-d H:i:s'). "\033[0m - Please visit http://www.pihome.eu/2017/10/11/apache-php-mysql-raspberry-pi-lamp/ to install correction version. \n";
	exit();
}else {
	echo "\033[36m".date('Y-m-d H:i:s'). "\033[0m - php version \033[41m".phpversion()."\033[0m looks OK \n";
}

$settings = parse_ini_file(__DIR__.'/../st_inc/db_config.ini');
foreach ($settings as $key => $setting) {
    // Notice the double $$, this tells php to create a variable with the same name as key
    $$key = $setting;
}

echo "\033[32mMake Sure you have correct MySQL/MariaDB credentials as following \033[0m\n";
echo "Hostname:     ".$hostname."\n";
echo "User Name:    ".$dbname."\n";
echo "Password:     ".$dbpassword."\n";
echo "\033[36m".date('Y-m-d H:i:s'). "\033[0m - Are you sure you want to do this?  Type 'yes' to continue? \n Exiting Database will be Deleted. \n";
echo $line;

$handle = fopen ("php://stdin","r");
$response  = fgets($handle);
if(trim($response ) != 'yes'){
	echo "\033[36m".date('Y-m-d H:i:s'). "\033[0m - ABORTING!!! : Run script again and Type yes to continue\n";
    exit;
}
fclose($handle);

//Test Connection to MySQL Server with Given Username & Password 
echo "\033[36m".date('Y-m-d H:i:s'). "\033[0m - Testing Connection to MySQL/MariaDB Server. \n";
$conn = new mysqli($hostname, $dbusername, $dbpassword);
if ($conn->connect_error){
	die('Database Connecction Failed with Error: '.$conn->connect_error);
}else {
	echo "\033[36m".date('Y-m-d H:i:s'). "\033[0m - Database Server Connection Successfull \n";
}

echo "\033[36m".date('Y-m-d H:i:s'). "\033[0m - Checking if Database Already Exits \n";
$db_selected = mysqli_select_db($conn, $dbname);
if (!$db_selected) {
	echo "\033[36m".date('Y-m-d H:i:s'). "\033[0m - DataBase \033[41m".$dbname."\033[0m Does not Exist \n"; 
	$query = "CREATE DATABASE {$dbname};";
	$result = $conn->query($query);
	if ($result) {
		echo "\033[36m".date('Y-m-d H:i:s'). "\033[0m - DataBase \033[41m".$dbname."\033[0m Created Successfully!!! \n"; 
	}else {
		echo "\033[36m".date('Y-m-d H:i:s'). "\033[0m - DataBase Error Creating Database \n"; 
		mysqli_error($conn). "\n";
	}
}else {
	echo "\033[36m".date('Y-m-d H:i:s'). "\033[0m - DataBase \033[41m".$dbname."\033[0m Already Exist. \n";
	echo "\033[36m".date('Y-m-d H:i:s'). "\033[0m - Creating Dump File for Exiting Database. \n";
	//dump all mysql database and save as sql file
	$dumpfname = $dbname . "_" . date("Y-m-d_H-i-s").".sql";
	$command = "mysqldump --ignore-table=$dbname.backup --add-drop-table --host=$hostname --user=$dbusername ";
	if ($dbpassword)
        $command.= "--password=". $dbpassword ." ";
		$command.= $dbname;
		$command.= " > " . $dumpfname;
		system($command);
		// compress sql file and unlink (delete) sql file after creating zip file. 
		$zipfname = $dbname . "_" . date("Y-m-d_H-i-s").".zip";
		echo "\033[36m".date('Y-m-d H:i:s'). "\033[0m - Compressing Database Dump File \033[41m".$dumpfname."\033[0m \n";
		$zip = new ZipArchive();
		if($zip->open($zipfname,ZIPARCHIVE::CREATE)){
			$zip->addFile($dumpfname,$dumpfname);
			$zip->close();
			unlink($dumpfname);
			echo "\033[36m".date('Y-m-d H:i:s'). "\033[0m - Compressed Database Dump File \033[41m".$zipfname."\033[0m \n";
		}
		echo "\033[36m".date('Y-m-d H:i:s'). "\033[0m - Deleting Exiting Database \n"; 
		$query = "DROP DATABASE IF EXISTS {$dbname};";
		$conn->query($query);
		echo "\033[36m".date('Y-m-d H:i:s'). "\033[0m - Creating Database \n"; 
		$query = "CREATE DATABASE {$dbname};";
		$result = $conn->query($query);
		if ($result) {
			echo "\033[36m".date('Y-m-d H:i:s'). "\033[0m - DataBase \033[41m".$dbname."\033[0m Created Successfully!!! \n"; 
		}else {
			echo "\033[36m".date('Y-m-d H:i:s'). "\033[0m - DataBase Error Creating Database \n"; 
			mysqli_error($conn). "\n";
		}
}
	echo "\033[36m".date('Y-m-d H:i:s'). "\033[0m - Importing SQL File to Database, This could take few minuts.  \n";
	// Name of the file
	$filename = __DIR__.'/maxair_mysql_database.sql';
	// Select database
	mysqli_select_db($conn, $dbname) or die('Error Selecting MySQL Database: ' . mysqli_error($conn));
	// Temporary variable, used to store current query
	$templine = '';
	// Read in entire file
	$lines = file($filename);
	// Loop through each line
	foreach ($lines as $line){
	// Skip it if it's a comment
		if (substr($line, 0, 2) == '--' || $line == '')
			continue;
			// Add this line to the current segment
			$templine .= $line;
			// If it has a semicolon at the end, it's the end of the query
			if (substr(trim($line), -1, 1) == ';'){
				// Perform the query
				$conn->query($templine) or print("MySQL Database Error with Query ".$templine.":". mysqli_error($conn)."\n");
				//mysqli_query($templine) or print("MySQL Database Error with Query ".$templine.":". mysqli_error($conn)."\n");
				// Reset temp variable to empty
				$templine = '';
			}
	}
	echo "\033[36m".date('Y-m-d H:i:s'). "\033[0m - DataBase File \033[41m".$filename."\033[0m Imported Successfully \n";
	//Table View 
	echo "\033[36m".date('Y-m-d H:i:s'). "\033[0m - Creating Table View \n";
	echo "\033[36m".date('Y-m-d H:i:s'). "\033[0m - Importing SQL Table View File to Database.  \n";
	// Name of the file
	$tableviewfilename = __DIR__.'/MySQL_View.sql';
	// Select database
	mysqli_select_db($conn, $dbname) or die('Error Selecting MySQL Database: ' . mysqli_error($conn));
	// Temporary variable, used to store current query
	$viewtempline = '';
	// Read in entire file
	$viewlines = file($tableviewfilename);
	// Loop through each line
	foreach ($viewlines as $viewline){
	// Skip it if it's a comment
		if (substr($viewline, 0, 2) == '--' || $viewline == '')
			continue;
			// Add this line to the current segment
			$viewtempline .= $viewline;
			// If it has a semicolon at the end, it's the end of the query
			if (substr(trim($viewline), -1, 1) == ';'){
				// Perform the query
				$conn->query($viewtempline) or print("MySQL Database Error with Query ".$viewtempline.":". mysqli_error($conn)."\n");
				//mysqli_query($viewtempline) or print("MySQL Database Error with Query ".$viewtempline.":". mysqli_error($conn)."\n");
				// Reset temp variable to empty
				$viewtempline = '';
			}
	}
	echo "\033[36m".date('Y-m-d H:i:s'). "\033[0m - DataBase File \033[41m".$tableviewfilename."\033[0m Imported Successfully \n";

		// Add User and System table data 
        echo "\033[36m".date('Y-m-d H:i:s'). "\033[0m - Creating User Table.  \n";
        $query_user = "REPLACE INTO `user` (`id`, `account_enable`, `fullname`, `username`, `email`, `password`, `cpdate`, `account_date`, `admin_account`) VALUES(1, 1, 'Administrator', 'admin', '', '0f5f9ba0136d5a8588b3fc70ec752869', 'date1', 'date2', 1);";
        $query_user = str_replace("date1",$date_time,$query_user);
        $query_user = str_replace("date2",$date_time,$query_user);
        $results = $conn->query($query_user);
        if ($results) {
                echo  "\033[36m".date('Y-m-d H:i:s'). "\033[0m - DataBase Add \033[41mUser\033[0m Data  Succeeded \n";
        } else {
                echo "\033[36m".date('Y-m-d H:i:s'). "\033[0m - DataBase Add \033[41mUser\033[0m Data Failed \n";
        }

        echo "\033[36m".date('Y-m-d H:i:s'). "\033[0m - Creating System Table.  \n";
        $query_system = "REPLACE INTO `system` (`id`, `sync`, `purge`, `name`, `version`, `build`, `update_location`, `update_file`, `update_alias`, `country`, `language`, `city`, `zip`, `openweather_api`, `backup_email`, `ping_home`, `timezone`, `shutdown`, `reboot`, `c_f`, `mode`) VALUES (2, 1, 0, 'MaxAir - Smart Thermostat', 'version_val', 'build_val', 'http://www.pihome.eu/updates/', 'current-release-versions.php', 'pihome', 'IE', 'en', 'Portlaoise', NULL, 'aa22d10d34b1e6cb32bd6a5f2cb3fb46', '', b'1', 'Europe/Dublin', 0, 0, 0, 0);";
        $query_system = str_replace("version_val",$version,$query_system);
        $query_system = str_replace("build_val",$build,$query_system);
        $results = $conn->query($query_system);
        if ($results) {
                echo  "\033[36m".date('Y-m-d H:i:s'). "\033[0m - DataBase Add \033[41mSystem\033[0m Data Succeeded \n";
        } else {
                echo "\033[36m".date('Y-m-d H:i:s'). "\033[0m - DataBase Add \033[41mSystem\033[0m Data Failed \n";
        }
		
		//Adding Away Record
		echo "\033[36m".date('Y-m-d H:i:s'). "\033[0m - Adding Away Status Record\n";
		$datetime = date('Y-m-d H:i:s');
		$query_system = "insert INTO `away` (`sync`, `purge`, `status`, start_datetime, `end_datetime`, `away_button_id`, `away_button_child_id`) VALUES (0, 0, 0, '$datetime', '$datetime', 0, 0);";
		$query_system = str_replace("version_val",$version,$query_system);
		$query_system = str_replace("build_val",$build,$query_system);
		$results = $conn->query($query_system);
		if ($results) {
				echo  "\033[36m".date('Y-m-d H:i:s'). "\033[0m - Away Status Record Added \033[41mAway\033[0m Data Succeeded \n";
		} else {
				echo "\033[36m".date('Y-m-d H:i:s'). "\033[0m - Away Status \033[41mAway\033[0m Data Failed \n";
		}

		//Adding GPIO
		echo "\033[36m".date('Y-m-d H:i:s'). "\033[0m - Adding GPIO Definition\n";
		$datetime = date('Y-m-d H:i:s');
		$query_system = "insert INTO `nodes` (`sync`, `purge`, `type`, node_id, `max_child_id`, `name`, `last_seen`, `notice_interval`, `min_value`, `status`, `ms_version`, `sketch_version`, `repeater`) VALUES (0, 0, 'GPIO', 0, 0, 'GPIO Controller', '$datetime', 0, '0', 'Active', 0, 0, 0);";
		$query_system = str_replace("version_val",$version,$query_system);
		$query_system = str_replace("build_val",$build,$query_system);
		$results = $conn->query($query_system);
		if ($results) {
			echo  "\033[36m".date('Y-m-d H:i:s'). "\033[0m - GPIO Definition Added \033[41mGPIO\033[0m Data Succeeded \n";
			$node_id = $conn->insert_id;
		} else {
			echo "\033[36m".date('Y-m-d H:i:s'). "\033[0m - GPIO Definition \033[41mGPIO\033[0m Data Failed \n";
		}

		//Adding System Controller Record
		echo "\033[36m".date('Y-m-d H:i:s'). "\033[0m - Adding System Controller Record\n";
		$datetime = date('Y-m-d H:i:s');
		$query_system = "insert INTO `system_controller` (`sync`, `purge`, `mode`, `status`, `active_status`, `name`, `hysteresis_time`, `max_operation_time`, `overrun`, `datetime`, `sc_mode`, `sc_mode_prev`, `heat_relay_id`, `cool_relay_id`, `fan_relay_id`) VALUES (0, 0, 0, 1, 0, 'Gas Boiler', 3, 60, 2, '$datetime', 0, 0, 0, 0, 0);";
		$results = $conn->query($query_system);
		if ($results) {
				echo  "\033[36m".date('Y-m-d H:i:s'). "\033[0m - System Controller Record Added \033[41mSystem Controller\033[0m Data Succeeded \n";
		} else {
				echo "\033[36m".date('Y-m-d H:i:s'). "\033[0m - System Controller Record \033[41mSystem Controller\033[0m Data Failed \n";
		}

		//Adding job scheduling records
		echo "\033[36m".date('Y-m-d H:i:s'). "\033[0m - Adding Job Scheduling Recors\n";
		$query_job_scheduling = "INSERT INTO `jobs`(`job_name`, `script`, `enabled`, `log_it`, `time`, `output`) ";
		$query_job_scheduling .= "VALUES ('controller','/var/www/cron/controller.php',1,0,'60',''),";
		$query_job_scheduling .= "('db_cleanup','/var/www/cron/db_cleanup.php',1,0,'02:00',''),";
		$query_job_scheduling .= "('check_gw','/var/www/cron/check_gw.php',1,0,'60',''),";
		$query_job_scheduling .= "('system_c','/var/www/cron/system_c.php',1,0,'300',''),";
		$query_job_scheduling .= "('weather_update','/var/www/cron/weather_update.php',1,0,'1800',''),";
		$query_job_scheduling .= "('reboot_wifi','/var/www/cron/reboot_wifi.sh',1,0,'120',''),";
		$query_job_scheduling .= "('gpio_ds18b20','/var/www/cron/gpio_ds18b20.py',0,0,'60',''),";
		$query_job_scheduling .= "('sw_install','/var/www/cron/sw_install.py',1,0,'10','');";
		$results = $conn->query($query_job_scheduling);
		if ($results) {
                		echo  "\033[36m".date('Y-m-d H:i:s'). "\033[0m - Job Scheduling Records Added \033[41mJobs\033[0m Data  Succeeded \n";
		} else {
                		echo "\033[36m".date('Y-m-d H:i:s'). "\033[0m - Job Scheduling Records \033[41mJobs\033[0m Data Failed \n";
		}

		//Adding Zone Type Records 
		echo "\033[36m".date('Y-m-d H:i:s'). "\033[0m - Adding Zone Type\n";
		$datetime = date('Y-m-d H:i:s');
		$query_zone_type = "insert INTO `zone_type` (`purge`, `sync`, `type`, `category`) VALUES (0, 0, 'Heating', 0), (0, 0, 'Water', 0), (0, 0, 'Immersion', 1), (0, 0, 'Lamp', 2);";
		$results = $conn->query($query_zone_type);
		if ($results) {
				echo  "\033[36m".date('Y-m-d H:i:s'). "\033[0m - Zone Type Records Added \033[41mZone Type\033[0m Data Succeeded \n";
		} else {
				echo "\033[36m".date('Y-m-d H:i:s'). "\033[0m - Zone Type Records \033[41mZone Type\033[0m Data Failed \n";
		}

		//Adding sensor type records
		echo "\033[36m".date('Y-m-d H:i:s'). "\033[0m - Adding Sensor Type Records\n";
		$query_sensor_type = "INSERT INTO `sensor_type`(`id`, `sync`, `purge`, `type`) ";
		$query_sensor_type .= "VALUES (1,0,0,'Temperature'),";
		$query_sensor_type .= "(2,0,0,'Humidity');";
		$results = $conn->query($query_sensor_type);
		if ($results) {
                	echo  "\033[36m".date('Y-m-d H:i:s'). "\033[0m - Sensor Type Records Added \033[41mSensor Types\033[0m Data  Succeeded \n";
		} else {
                	echo "\033[36m".date('Y-m-d H:i:s'). "\033[0m - Sensor Type Records \033[41mSensor Types\033[0m Data Failed \n";
		}

echo "---------------------------------------------------------------------------------------- \n";
echo "\033[36m".date('Y-m-d H:i:s'). "\033[0m - MySQL DataBase Table View Script Ended \n"; 
echo "\033[32m****************************************************************************************\033[0m  \n";
?>
