<?php
$club_Name = "Motorsports Online Event Registration System";
$club_Abbr = 'MOERS'; // must use '' instead of ""
$club_Site = "http://www.xhub.com";
  
$email_Administrator = "contact@xhub.com";
$email_MembershipDirector = "contact@xhub.com";
  
$cookie_name = 'MOERS_'.$club_Abbr;
$cookie_value_SERVER = "xhub.com";
$cookie_value_DIRECTORY = '/moers/'; // replace 'moers' with club abbr

$http_Logout = "https://xhub.com/moers/";
$http_Login = "https://".$cookie_value_SERVER.$cookie_value_DIRECTORY;
$http_Login_backup = $http_Login."index_backup.php";

$mime_boundary = "----".$club_Abbr."----";

$paypal_Email = "contact@xhub.com";
$paypal_Return = $http_Logout."main.php";
$paypal_Cancel = $http_Logout."main.php";

?>
