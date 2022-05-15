<?php
$host = 'pihome.eu';
if($socket =@ fsockopen($host, 80, $errno, $errstr, 30)) {
	echo "online \n";
	fclose($socket);
} else {
	echo "offline \n";
}
?>

<?php
/**
 * PHP/cURL function to check a web site status. If HTTP status is not 200 or 302, or
 * the requests takes longer than 10 seconds, the website is unreachable.
 * 
 * Follow me on Twitter: @HertogJanR
 *
 * @param string $url URL that must be checked
 */
function url_test( $url ) {
  $timeout = 10;
  $ch = curl_init();
  curl_setopt ( $ch, CURLOPT_URL, $url );
  curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
  curl_setopt ( $ch, CURLOPT_TIMEOUT, $timeout );
  $http_respond = curl_exec($ch);
  $http_respond = trim( strip_tags( $http_respond ) );
  $http_code = curl_getinfo( $ch, CURLINFO_HTTP_CODE );
  if ( ( $http_code == "200" ) || ( $http_code == "302" ) ) {
    return true;
  } else {
    // return $http_code;, possible too
    return false;
  }
  curl_close( $ch );
}
 
$website = "www.pihome.eu";
if( !url_test( $website ) ) {
  echo "Offline \n";
}
else { echo "Online\n"; }
?>