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
?>
<?php
//Error reporting on php ON
error_reporting(E_ALL);
//Error reporting on php OFF
//error_reporting(0);

require_once(__DIR__.'/st_inc/session.php');
if (logged_in()) {
	header("Location: home.php");
	exit;
}
require_once(__DIR__.'/st_inc/connection.php');
require_once(__DIR__.'/st_inc/functions.php');

//$lang = settings($conn, 'language');
//setcookie("PiHomeLanguage", $lang, time()+(3600*24*90));
//require_once (__DIR__.'/languages/'.$_COOKIE['PiHomeLanguage'].'.php');

//check if NetworkManager is running
if(strpos(service_status("NetworkManager.service"), 'active (running)') !== false) {
	$network_manager = 1;
} else {
        $network_manager = 0;
}

if (file_exists("/etc/systemd/system/autohotspot.service") == 1) {
	$no_ap = 1;
	//check id wlan0 interface is flagged as working in AP mode
	$query = "SELECT ap_mode FROM network_settings WHERE interface_type = 'wlan0';";
	$result_set = $conn->query($query);
	if (mysqli_num_rows($result_set) == 1) {
		$found = mysqli_fetch_array($result_set);
		$ap_mode = $found['ap_mode'];
	} else {
        	$ap_mode = 0;
	}
	//check is associated with a local wifi network
        if ($network_manager == 0) {
		//check using iwconfig
		$localSSID = exec("/sbin/iwconfig wlan0 | grep 'ESSID'  ");
		if(strpos($localSSID, 'ESSID:') !== false) {
        		$wifi_connected = 1;
		} else {
        		$wifi_connected = 0;
		}
	} else {
		//check using NetworkManager
		$localSSID = exec("nmcli con show --active | grep wlan0 | awk '{print $1}'");
		if (strlen($localSSID) > 0 && strpos($localSSID, 'HotSpot') === false) {
                        $wifi_connected = 1;
                } else {
                        $wifi_connected = 0;
                }
	}
	//check if ethernet connection is available
	$eth_found = exec("sudo /sbin/ifconfig eth0 | grep 'inet '");
	if(strpos($eth_found, 'inet ') !== false) {
        	$eth_connected = 1;
	} else {
        	$eth_connected = 0;
	}
} else {
	$no_ap = 0;
}
//$wifi_connected = 0;
// start process if data is passed from url  http://192.168.99.9/index.php?user=username&pass=password
// check session id cookie exists
if(isset($_COOKIE["maxair_login"])) $s_id = $_COOKIE["maxair_login"]; else $s_id="";
if ($s_id != "") {
	// check if a user has logged on with this session id
        $query = "SELECT username, s_id FROM userhistory ORDER BY id DESC;";
        $results = $conn->query($query);
        if (mysqli_num_rows($results) > 0) {
	        while ($row = mysqli_fetch_assoc($results)) {
			if (password_verify($s_id, $row['s_id'])) { // check if this session exists in the userhistory table
				$query = "SELECT id, username, admin_account, persist FROM user WHERE username = '{$row['username']}' AND persist = 1 LIMIT 1;";
			        $result_set = $conn->query($query);
        			if (mysqli_num_rows($result_set) == 1) {
        				// user account found, restore session
		                	$found_user = mysqli_fetch_array($result_set);
	        		        // Set session variables
        	        		$_SESSION['user_id'] = $found_user['id'];
	        	        	$_SESSION['username'] = $found_user['username'];
	        		        $_SESSION['admin'] = $found_user['admin_account'];
        	        		$_SESSION['persist'] = $found_user['persist'];
					header('Location:home.php');
					exit;
				}
			}
		}
	}
}

if(($no_ap == 0 || $wifi_connected == 1 || $eth_connected == 1 || $ap_mode == 1) && isset($_GET['user']) && isset($_GET['password'])) {
	$username = $_GET['user'];
	$password = $_GET['password'];
	// perform validations on the form data
	if( (((!isset($_GET['user'])) || (empty($_GET['user']))) && (((!isset($_GET['password'])) || (empty($_GET['password'])))) )){
		$error_message = $lang['user_pass_empty'];
	} elseif ((!isset($_GET['user'])) || (empty($_GET['user']))) {
		$error_message = $lang['user_empty'];
	} elseif((!isset($_GET['password'])) || (empty($_GET['password']))) {
		$error_message = $lang['pass_empty'];
	}

	$username = mysqli_real_escape_string($conn, $_POST['user']);
	$password = mysqli_real_escape_string($conn,(md5($_POST['password'])));
	if ( !isset($error_message) ) {
		// Check database to see if username and the hashed password exist there.
		$query = "SELECT id, username, admin_account, persist FROM user WHERE username = '{$username}' AND password = '{$password}' AND account_enable = 1 LIMIT 1;";
		$result_set = $conn->query($query);
		if (mysqli_num_rows($result_set) == 1) {
			// username/password authenticated
			$found_user = mysqli_fetch_array($result_set);
			// Set username session variable
			$_SESSION['user_id'] = $found_user['id'];
			$_SESSION['username'] = $found_user['username'];
                        $_SESSION['admin'] = $found_user['admin_account'];
                        $_SESSION['persist'] = $found_user['persist'];

			if(!empty($_POST["remember"])) {
				setcookie ("user_login",$_POST["username"],time()+ (10 * 365 * 24 * 60 * 60));
				setcookie ("pass_login",$_POST["password"],time()+ (10 * 365 * 24 * 60 * 60));
			} else {
				if(isset($_COOKIE["user_login"])) {
					// set the expiration date to one hour ago
					setcookie("user_login", "", time() - 3600);
					setcookie("pass_login", "", time() - 3600);
				}
			}
			//$_SESSION['url'] = $_SERVER['REQUEST_URI']; // i.e. "about.php"
			$lastlogin= date("Y-m-d H:i:s");
			$query = "UPDATE userhistory SET lastlogin = '{$lastlogin}' WHERE username = '{$username}' LIMIT 1";
			$result = $conn->query($query);
			// redirect to home page after successfull login
			//redirect_to('home.php');
			if(isset($_SESSION['url'])) {
				$url = $_SESSION['url']; // holds url for last page visited.
			}else {
				$url = "index.php"; // default page for
			}
		redirect_to($url);
		}
	}
}

if (isset($_POST['submit'])) {
	if ($no_ap == 0 || $wifi_connected == 1 || $eth_connected == 1 || $ap_mode == 1) {
		if( (((!isset($_POST['username'])) || (empty($_POST['username']))) && (((!isset($_POST['password'])) || (empty($_POST['password'])))) )){
			$error_message = $lang['user_pass_empty'];
		} elseif ((!isset($_POST['username'])) || (empty($_POST['username']))) {
			$error_message = $lang['user_empty'];
		} elseif((!isset($_POST['password'])) || (empty($_POST['password']))) {
			$error_message = $lang['pass_empty'];
		}

		$username = mysqli_real_escape_string($conn, $_POST['username']);
		$password = mysqli_real_escape_string($conn,(md5($_POST['password'])));

		//get client ip address
		if (!empty($_SERVER["HTTP_CLIENT_IP"])){
			//check for ip from share internet
			$ip = $_SERVER["HTTP_CLIENT_IP"];
		}elseif (!empty($_SERVER["HTTP_X_FORWARDED_FOR"])){
			// Check for the Proxy User
			$ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
		}else{
			$ip = $_SERVER["REMOTE_ADDR"];
		}
		//set date and time
		$lastlogin= date("Y-m-d H:i:s");

		if ( !isset($error_message) ) {
			// Check database to see if username and the hashed password exist there.
			$query = "SELECT id, username, admin_account, persist FROM user WHERE username = '{$username}' AND password = '{$password}' AND account_enable = 1 LIMIT 1;";
			$result_set = $conn->query($query);
			if (mysqli_num_rows($result_set) == 1) {
				// username/password authenticated
				$found_user = mysqli_fetch_array($result_set);
				// Set username session variable
				$_SESSION['user_id'] = $found_user['id'];
       				$_SESSION['username'] = $found_user['username'];
                               	$_SESSION['admin'] = $found_user['admin_account'];
                               	$_SESSION['persist'] = $found_user['persist'];

				if(!empty($_POST["remember"])) {
					setcookie ("user_login",$_POST["username"],time()+ (10 * 365 * 24 * 60 * 60));
					setcookie ("pass_login",$_POST["password"],time()+ (10 * 365 * 24 * 60 * 60));
                                        setcookie ("maxair_login",session_id(),time()+ (10 * 365 * 24 * 60 * 60));
				} else {
					if(isset($_COOKIE["user_login"])) {
						// set the expiration date to one hour ago
						setcookie("user_login", "", time() - 3600);
						setcookie("pass_login", "", time() - 3600);
					}
				}

				// add entry to database if login is success
				$s_id = password_hash(session_id(), PASSWORD_DEFAULT);
				$query = "INSERT INTO userhistory(username, password, date, audit, ipaddress, s_id) VALUES ('{$username}', '{$password}', '{$lastlogin}', 'Successful', '{$ip}', '{$s_id}')";
				$conn->query($query);
				// Set Language cookie if doesn't exist
				if(!isset($_COOKIE['PiHomeLanguage'])) {
					$query = "SELECT language FROM system;";
					$result = $conn->query($query);
					$row = mysqli_fetch_assoc($result);
					if (mysqli_num_rows($result) == 1) {
						$lang = $row['language'];
						setcookie("PiHomeLanguage", $lang, time()+(3600*24*90));
						header("Location: " . $_SERVER['HTTP_REFERER']);
					}
				}

        			// Jump to secured page
				if(isset($_SESSION['url'])) {
					$url = $_SESSION['url']; // holds url for last page visited.
				}else {
					$url = "index.php"; // default page for
				}
				redirect_to($url);
			} else {
				// add entry to database if login is success
				$query = "INSERT INTO userhistory(username, password, date, audit, ipaddress) VALUES ('{$username}', '{$password}', '{$lastlogin}', 'Failed', '{$ip}')";
				$result = $conn->query($query);
				// username/password was not found in the database
				$error_message = $lang['user_pass_error'];
			}
		}
	} else {
		if(empty($_POST["ap_mode"])) { //set the ssid and password if not working in AP mode
                        if( (((!isset($_POST['ssid'])) || (empty($_POST['ssid']))) && (((!isset($_POST['password'])) || (empty($_POST['password'])))) )){
       	                        $error_message = $lang['ssid_pass_empty'];
               	        } elseif ((!isset($_POST['ssid'])) || (empty($_POST['ssid']))) {
                       	        $error_message = $lang['ssid_empty'];
                        } elseif((!isset($_POST['password'])) || (empty($_POST['password']))) {
       	                        $error_message = $lang['pass_empty'];
               	        }
			$ssid = mysqli_real_escape_string($conn, $_POST['ssid']);
                        $password = mysqli_real_escape_string($conn, $_POST['password']);

			if ($network_manager == 0) { //not using NetworkManager
				$wpa_conf='/etc/wpa_supplicant/wpa_supplicant.conf';
				exec("sudo cat ".$wpa_conf.">myfile1.tmp");
				$reading = fopen('myfile1.tmp', 'r');
				$writing = fopen('myfile2.tmp', 'w');
    				$replaced = false;
    				while (!feof($reading)) {
					$line = fgets($reading);
					if (stristr($line,'ssid="')) {
       						$line = '    ssid="'.$ssid.'"';
       						$line = $line."\n";
        					$replaced = true;
      					}
       	        	                if (stristr($line,'psk="')) {
               	        	                $line = '    psk="'.$password.'"';
                       	        	        $line = $line."\n";
                               	        	$replaced = true;
                                	}
      					fputs($writing, $line);
				}
				fclose($reading); fclose($writing);
    				// might as well not overwrite the file if we didn't replace anything
				if ($replaced) {
					exec("sudo mv myfile2.tmp ".$wpa_conf);
					exec("sudo rm myfile*.tmp");
    				} else {
      					exec("rm myfile*.tmp");
				}
        			exec("sudo reboot");
			} else { //using NetworkManager
				$profile = "/var/www/add_on/Autohotspot/profile.txt";
				$writing = fopen($profile, "w");
				$line = $ssid."\n".$password."\n";
				fputs($writing, $line);
				fclose($writing);
				exec("sudo reboot");
			}
		} else {
			//working in Ap mode set the ap_mode flag in the network settings table
			$query = "SELECT ap_mode FROM network_settings WHERE interface_type = 'wlan0';";
			$result_set = $conn->query($query);
			if (mysqli_num_rows($result_set) == 1) {
        			$found = mysqli_fetch_array($result_set);
	       			if ($found['ap_mode'] == 0) {
					$query = "UPDATE network_settings SET ap_mode = 1 WHERE interface_type = 'wlan0';";
	       	                        $result = $conn->query($query);
				}
			} else {
                                $query = "SELECT MAX( interface_num ) AS max_interface_num FROM `network_settings`;";
                                $result = $conn->query($query);
                                $row = mysqli_fetch_array($result);
                                $max_interface_num = $row['max_interface_num'] + 1;
                                $query = "INSERT INTO `network_settings`(`sync`, `purge`, `primary_interface`, `ap_mode`, `interface_num`, `interface_type`, `mac_address`, `hostname`, `ip_address`, `gateway_address`, `net_mask`, `dns1_address`, `dns2_address`) VALUES ('0', '0', '0', '1', '{$max_interface_num}', 'wlan0', '', '', '', '', '', '', '');";
                                $result = $conn->query($query);
			}
			redirect_to('index.php');
		}
	}
} else { // Form has not been submitted.
	if (isset($_GET['logout']) && $_GET['logout'] == 1) {
		$info_message = $lang['user_logout'];
	}
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
<meta name="description" content="">
<meta name="author" content="">
<title><?php  echo settings($conn, 'name') ;?></title>
<meta name="apple-mobile-web-app-capable" content="yes" />
<link rel="shortcut icon" href="images/favicon.ico">
<link rel="apple-touch-icon" href="images/apple-touch-icon.png"/>
<META HTTP-EQUIV="CACHE-CONTROL" CONTENT="NO-CACHE">
<META HTTP-EQUIV="PRAGMA" CONTENT="NO-CACHE">

<!-- Bootstrap Core CSS -->
<link href="css/bootstrap.min.css" rel="stylesheet">

<!-- MetisMenu CSS -->
<link href="css/plugins/metisMenu/metisMenu.min.css" rel="stylesheet">

<!-- Custom CSS -->
<link href="css/sb-admin-2.css" rel="stylesheet">

<!-- Morris Charts CSS -->
<link href="css/plugins/morris.css" rel="stylesheet">

<!-- Custom Fonts -->
<link href="fonts/fontawesome-free-6.1.1-web/css/all.css" rel="stylesheet">

<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
<!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
<![endif]-->
<script type='text/javascript' src='http://code.jquery.com/jquery-1.7.2.min.js'></script>
<script>
    if(("standalone" in window.navigator) && window.navigator.standalone){
	// If you want to prevent remote links in standalone web apps opening Mobile Safari, change 'remotes' to true
	var noddy, remotes = true;
	document.addEventListener('click', function(event) {
		noddy = event.target;
		// Bubble up until we hit link or top HTML element. Warning: BODY element is not compulsory so better to stop on HTML
		while(noddy.nodeName !== "A" && noddy.nodeName !== "HTML") {
	        noddy = noddy.parentNode;
	    }
		if('href' in noddy && noddy.href.indexOf('http') !== -1 && (noddy.href.indexOf(document.location.host) !== -1 || remotes))
		{
			event.preventDefault();
			document.location.href = noddy.href;
		}
	},false);
}
</script> 
</head>
<style type="text/css" >
html {
    height: 100%;
}
</style>
<body class="bg-primary">
	<div class="container-fluid text-dark bg-light vh-100">
        	<div class="row justify-content-center">
			<br><br>
			<h6 class="text-center"><img src="images/maxair_logo.png" height="28"> <br></h6>
            		<div class="col-lg-4">
                		<div class="card shadow-lg border-0 rounded-lg mt-5">
                    			<?php 
					if ($no_ap == 0 || $wifi_connected == 1 || $eth_connected == 1) {
						echo '<div class="card-header"><h3 class="text-center font-weight-light my-4">'.$lang['sign_in'].'</h3></div>';
					} else {
                                                echo '<div class="card-header"><h3 class="text-center font-weight-light my-4">'.$lang['wifi_connect'].'</h3></div>';
					}
           				echo '<div class="card-body">
						<div class="row">
                        				<form method="post" action="'.$_SERVER['PHP_SELF'].'" role="form">';
								include("notice.php");
								echo '<br>
                            					<fieldset>
                                					<div class="form-group">';
										if ($no_ap == 0 || $wifi_connected == 1 || $eth_connected == 1 || $ap_mode == 1) {
											echo '<input class="form-control" placeholder="User Name" name="username" type="input" value="';
											if(isset($_COOKIE["user_login"])) { echo $_COOKIE["user_login"]; }
											echo '" autofocus>';
										} else {
											$output = array();
											echo '<select class="form-control input-sm" type="text" id="ssid" name="ssid" >';
											if ($network_manager == 0) { //not using NetworkManager
												$command= "sudo /sbin/iwlist wlan0 scan | grep ESSID";
											} else {
												$command= "cat /var/www/add_on/Autohotspot/ssid.txt";
											}
											exec("$command 2>&1 &", $output);
											$arrayLength = count($output);
        										$i = 0;
        										while ($i < $arrayLength) {
												if ($network_manager == 0) {
                											preg_match('/"([^"]+)"/', trim($output[$i]), $result);
													echo '<option value="'.$result[1].'">'.$result[1].'</option>';
												} else {
													echo '<option value="'.trim($output[$i]).'">'.trim($output[$i]).'</option>';
												}
                										$i++;
        										}
											echo '</select>';
										}
									echo '</div>

                                					<div class="form-group">
                                						<input class="form-control" placeholder="Password" name="password" type="password" value="';
										if(isset($_COOKIE["pass_login"])) { echo $_COOKIE["pass_login"]; }
                                					echo '"></div>';
                                        				if ($no_ap == 0 || $wifi_connected == 1 || $eth_connected == 1 || $ap_mode == 1) {
										echo '<div class="field-group">
											<div class="checkbox checkbox-dark form-check-circle">
												<input id="checkbox1" class="styled" type="checkbox" name="remember" ';
												if(isset($_COOKIE["user_login"])) { echo 'checked >'; } else {  echo '>'; }
													echo '<label for="checkbox1"> Remember me</label>';
											echo '</div>
										</div>
										<input type="submit" name="submit" value="'.$lang['login'].'" class="btn btn-block btn-default btn-block login"/>';
									} else {
                                                                                echo '<div class="field-group">
                                                                                        <div class="checkbox checkbox-dark form-check-circle">
                                                                                                <input id="checkbox2" class="styled" type="checkbox" name="ap_mode" >';
                                                                                                        echo '<label for="checkbox2"> AP Mode</label>';
                                                                                        echo '</div>
                                                                                </div>
                                                                                <input type="submit" name="submit" value="'.$lang['set_reboot'].'" class="btn btn-block btn-default btn-block login"/>';
									}
                            					echo '</fieldset>
                        				</form><br>'; ?>
                    				</div>
						<!--<div class="row">   -->
					</div>
                                        <!--<div class="card-body">   -->
					<!--<div class="card-footer">	</div> -->
				</div>
				<!--<div class="card shadow-lg border-0 rounded-lg mt-5">	-->
			</div>
			<!--<div class="col-lg-5">   -->
			<div class="login-panel-foother justify-content-center">
			<?php 
				echo '<h3>
						<small>';
							$languages = ListLanguages(settings($conn, 'language'));
							for ($x = 0; $x <= count($languages) - 1; $x++) {
								echo '<a class="text-info" style="text-decoration: none;" href="languages.php?lang='.$languages[$x][0].'" title="'.$languages[$x][1].'">'.$languages[$x][1].'</a>';
								if ($x <= count($languages) - 2) { echo '&nbsp;&nbsp;&nbsp;&nbsp;-&nbsp;&nbsp;&nbsp;&nbsp;'; }
							} ?>
						</small>
						</h3><br><br>
				<h6><?php echo settings($conn, 'name').' '.settings($conn, 'version')."&nbsp;&nbsp;&nbsp;-&nbsp;&nbsp;&nbsp;".$lang['build']." ".settings($conn, 'build'); ?></h6>
				<br><br>
                               	<h6><?php echo "&copy;&nbsp;".$lang['copyright']; ?></h6>
			</div>
			<!--<div class="login-panel-foother">   -->
		</div>
		<!--<div class="<div class="row justify-content-center">   -->
	</div>
	<!--<div class="container-fluid text-dark bg-light">   -->
    <!-- jQuery -->
    <script src="js/jquery.js"></script>

    <!-- Bootstrap Core JavaScript -->
    <script src="js/bootstrap.min.js"></script>

    <!-- Custom Theme JavaScript -->
    <script src="js/sb-admin-2.js"></script>
<script>
//Automatically close alert message  after 5 seconds
window.setTimeout(function() {
    $(".alert").fadeTo(1500, 0).slideUp(500, function(){
        $(this).remove(); 
    });
}, 10000);
</script>
</body>
</html>
<?php if(isset($conn)) { $conn->close();} ?>
