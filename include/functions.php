<?php
// functions.php
/************************************************************************************
    Copyright © 2008-2010 xhub.com

    Sarah Bennert
    sarah@xhub.com

    This file is part of the Motorsports Online Event Registration System (moers).

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.

    Any system sensitive data such as IP addresses, usernames, and passwords 
    must be removed from this file before distribution.

************************************************************************************/

if (PHP_VERSION_ID < 50500) {
  // The password hash function is not available until 5.5.0, require it
  throw new Exception('Must use PHP Version >= 5.5.0');
}

require_once 'include/nolicense_functions.php';
require_once 'include/config.php';

/////////////////////////////////////////////
// Function to check user info is filled out
function isUserInfoComplete($dbarray)
{
  if (!$dbarray['fname'] |
      !$dbarray['lname'] |
      !$dbarray['addr1'] |
      !$dbarray['city'] |
      !$dbarray['state'] |
      !$dbarray['zip'] |
      !$dbarray['hphone'] |
      !$dbarray['email'] |
      !$dbarray['econtact'] |
      !$dbarray['econtact_phone'] |
      !$dbarray['econtact_rel'])
  {
    return 0;
  }
  else
  {
    return 1;
  }
}

function isCurrentUserInfoComplete()
{
  $username = getUsername();
  $check2 = mysql_query("SELECT * FROM users WHERE username = '$username'")or die(mysql_error());
  $info2 = mysql_fetch_array( $check2 );
  if ($info2 && isUserInfoComplete($info2))
    return TRUE;
  else
    return FALSE;
}

////////////////////////////////////////////////////////////////////////
// This function will change the session_id everytime called.
// Purpose: to keep the session_id changing to help prevent spoofing
function changeCookie()
{
//	header("Content-Type: text/html; charset=utf-8");

	//Checks if there is a login cookie
	if(getCookie('ID'))
	{
		if (!get_magic_quotes_gpc())
		{
			$funcHash = addslashes(getCookie('ID'));
			$funcSession = addslashes(getCookie('Session_ID'));
		}
		else
		{
			$funcHash = getCookie('ID');
			$funcSession = getCookie('Session_ID');
		}

		$funcCheck = mysql_query("SELECT * FROM users WHERE sha256_user = '$funcHash'");
		if ($funcCheck)
		{
			while($funcInfo = mysql_fetch_array( $funcCheck ))
			{
				// this resets the stored session id
				$funcNewSession = rand_string(32);
				$funcUpdate = "UPDATE users SET session_id='$funcNewSession' WHERE sha256_user='$funcHash'";
				$funcResult = mysql_query($funcUpdate);				

				if ($funcSession != $funcInfo['session_id']) // Not a valid user, clear codes, log them out.
				{
					//this deletes the cookie
					clearCookies();

					// clear the variable values
					$funcHash = rand_string(32);
					$funcSession = rand_string(32);
					$funcNewSession = rand_string(32);
					$funcCheck = rand_string(32);
					$funcInfo = rand_string(32);

					// kick them out				
					header('Location: logout.php');
				}
				else
				{
					// reset the cookie
					setCookies($funcHash, $funcNewSession);
				}
			}
		}
		else
		{
			//this deletes the cookie
			clearCookies();
	
			// clear the variable values
			$funcHash = rand_string(32);
			$funcSession = rand_string(32);
			$funcNewSession = rand_string(32);
			$funcCheck = rand_string(32);
			$funcInfo = rand_string(32);

			// kick them out				
			header('Location: logout.php');
		}
	}
	else
	{
				//this deletes the cookie
				clearCookies();
				
				// clear the variable values
				$funcHash = rand_string(32);
				$funcSession = rand_string(32);
				$funcNewSession = rand_string(32);
				$funcCheck = rand_string(32);
				$funcInfo = rand_string(32);

				// kick them out				
				header('Location: logout.php');
	}

	// clear the variable values
	$funcHash = rand_string(32);
	$funcNewSession = rand_string(32);
	$funcSession = rand_string(32);
	$funcCheck = rand_string(32);
	$funcInfo = rand_string(32);
}

function clearCookies()
{
  //this deletes the cookies
  require 'include/configGlobals.php';
  setcookie($cookie_name."_ID", "", -1, $cookie_value_DIRECTORY, "", FALSE);
  setcookie($cookie_name."_Session_ID", "", -1, $cookie_value_DIRECTORY, "", FALSE);
}

function setCookies($cookieHash, $cookieSession)
{
  require 'include/configGlobals.php';
  setcookie($cookie_name.'_ID', $cookieHash, FALSE, $cookie_value_DIRECTORY, "", FALSE);
  $_COOKIE[$cookie_name.'_ID'] = $cookieHash;
  setcookie($cookie_name.'_Session_ID', $cookieSession, FALSE, $cookie_value_DIRECTORY, "", FALSE);
  $_COOKIE[$cookie_name.'_Session_ID'] = $cookieSession;
}

function getCookie($cookieType)
{
  require 'include/configGlobals.php';
  $cookie_name_type = $cookie_name.'_'.$cookieType;
  if (($cookieType != 'ID' && $cookieType != 'Session_ID') && !isset($_COOKIE[$cookie_name_type]))
  {
    return FALSE;
  }
  else
  {
    return $_COOKIE[$cookie_name_type];
  }
}

function isPasswordValid($funcInput)
{
  if (strlen($funcInput) >= 8 &&
      strpbrk($funcInput, 'abcdefghijklmnopqrstuvwxyz') != FALSE &&
      strpbrk($funcInput, 'ABCDEFGHIJKLMNOPQRSTUVWXYZ') != FALSE &&
      strpbrk($funcInput, '0123456789') != FALSE &&
      strpbrk($funcInput, '@.-_~!#%^&*()+`=[]{};:,<>/?|\\\'\$ ') == FALSE)
  {
    return TRUE;
  }
  else
  {
    return FALSE;
  }
}

function isUsernameValid($funcInput)
{
  $funcInput = strtolower($funcInput);
  if (strlen($funcInput) < 4 ||
      strpbrk($funcInput, '~!#%^&*()+`=[]{};:,<>/?|\\\'\$ ') != FALSE)
  {
    return FALSE;
  }
  else if ($funcInput == "admin" || $funcInput == "administrator" || $funcInput == "root")
  {
    return FALSE;
  }
  else
  {
    return TRUE;
  }
}

function slashArray(&$funcInput)
{
  if (is_array($funcInput))
  {
    foreach($funcInput as &$value)
    {
      if (!is_array($value))
      {
	    $value = trim($value);
        if (!get_magic_quotes_gpc())
        {
          $value = addslashes($value);
        }
      }
      else
      {
        slashArray($value);
      }
    }
    unset($value);
  }
}

// strips out any HTML tags from the given input array
function stripArrayTags(&$funcInput)
{
  if (is_array($funcInput))
  {
    foreach($funcInput as &$value)
    {
      if (!is_array($value))
      {
        $value = strip_tags(trim($value));
      }
      else
      {
        slashArray($value);
      }
    }
    unset($value);
  }
}

function stripArray(&$funcInput)
{
  if (is_array($func_input))
  {
    foreach($funcInput as &$value)
    {
      if (!is_array($value))
      {
	    $value = trim($value);
        if(!get_magic_quotes_gpc())
        {
          $value = stripslashes($value);
        }
      }
      else
      {
        stripArray($value);
      }
    }
    unset($value);
  }
}

function slashAllInputs()
{
  stripAllInputArrayTags();
  slashArray($_COOKIE);
  slashArray($_POST);
  slashArray($_REQUEST);
  slashArray($_GET);
  slashArray($_SERVER);
}

function stripAllInputArrayTags()
{
  stripArrayTags($_COOKIE);
  stripArrayTags($_POST);
  stripArrayTags($_REQUEST);
  stripArrayTags($_GET);
  stripArrayTags($_SERVER);
}

// Connect to database
function connectDatabase()
{
  require 'include/config_database.php';

  if (ini_set('sql.safe_mode', TRUE))
	dieError("mySQL Safe Mode Enabled. Please notify administrator.");

  $connect = mysql_connect($database_IP, $database_Username, $database_Password, FALSE, MYSQL_CLIENT_SSL) or 
    dieError("mySQL Connect error. Please notify administrator.");

  $db = mysql_select_db($database_Name, $connect) or 
    dieError("mySQL Select DB error. Please notify administrator.");

  return $db;
}

function getDatabaseName()
{
  require 'include/config_database.php';
  return $database_Name;
}

function dieError($error)
{
  echo file_get_contents("html/header.html");
  echo $error;
  echo file_get_contents("html/footer.html");
  die(' ');
}

// verifies user.
function isValidUser()
{
  $id = getCookie('ID');
  $query = "SELECT session_id FROM users WHERE sha256_user = '$id'";

  $check = mysql_query($query);

  if (!$check)
  {
    return FALSE;
  }
  while($info = mysql_fetch_array( $check ))
  {
    //if the cookie has the wrong sessionID, they are taken to the login page
    if (getCookie('Session_ID') != $info['session_id'])
    {
      return FALSE;
    }
    else
    {
      return TRUE;
    }
  }
  return FALSE;
}

function isUserAdmin()
{
  $id = getCookie('ID');
  $query = "SELECT admin FROM users WHERE sha256_user = '$id'";

  $check = mysql_query($query);

  if (!$check)
  {
    return FALSE;
  }
  $info = mysql_fetch_array( $check );
  if ($info)
  {
	return $info['admin'];
  }
  return FALSE;
}

// simple function to call in each script. if they are not valid, kick them out.
function validateUser()
{
  if (isValidUser())
  {
    logVerify(getCookie('ID'), "OK");
    changeCookie();
  }
  else
  {
    logVerify(getCookie('ID'), "INVALID");
    header("Location: logout.php");
  }
}

// This function will need to be modified for your evironment depending on the server variables available
function isSSL()
{
  // 3/6/2010 Current Server does not allow for Server side detection. Now using forceSSL() in functions.js
  return TRUE;

  if ($_SERVER['HTTPS'] == 1 || $_SERVER['HTTP_X_FORWARDED_SERVER'] == "ssl")
  {
    return TRUE;
  }
  else
  {
    return FALSE;
  }
}

// this must be run at the beginning of every script before it interacts with the database
function validateSession()
{
  // 3/6/2010 Current Server does not allow for Server side detection. Now using forceSSL() in functions.js
  // see function isSSL() above.
  if (!isSSL())
  {
	header("Location: logout.php");
  }

  slashAllInputs();

  connectDatabase();

  validateUser(); // if they are not valid, they don't come back from here.
}

function doesUserExist($funcUser)
{
  // checks it against the database
  $query = "SELECT * FROM users WHERE username = '".$funcUser."'";
  $check = mysql_query($query)or die(mysql_error());

  //Gives error if user dosen't exist
  if (mysql_num_rows($check))
  {
    return TRUE;
  }
  else
  {
	return FALSE;
  }
}

function isValidUserPassword($funcUser, $funcPass)
{
  $ret = 2;
  // checks it against the database
  $query = "SELECT sha256_pass, pass FROM users WHERE username = '".$funcUser."'";
  $check = mysql_query($query)or die(mysql_error());

  //Gives error if user dosen't exist
  if (mysql_num_rows($check) == 0)
  {
    $ret = FALSE;
  }
  else
  {
	if ($info = mysql_fetch_array( $check ))
	{
	  if ($info['sha256_pass'] != "") // see if has the newer hashed password set
	  {
		if ($info['hashed_pass'] == bin2hex(mhash(MHASH_SHA256, $funcPass)))
		{
		  $ret = TRUE;
		}
		else if (password_verify($funcPass, $info['hashed_pass']))
		{
		  $ret = TRUE;
		}
		else
		{
		  $ret = FALSE;
		}
	  }
	  else // has md5 hashed password
	  {
		if ($info['pass'] == md5($funcPass))
		{
		  // set password hash before returning true
		  $hashed_pass = password_hash($funcPass, PASSWORD_DEFAULT);
		  $update_member = mysql_query("UPDATE users SET sha256_pass='$hashed_pass' WHERE username='$funcUser'");
		  // clear md5 hash
		  $update_member = mysql_query("UPDATE users SET pass='' WHERE username='$funcUser'");
		  $ret = TRUE;
		}
		else
		{
		  $ret = FALSE;
		}
	  }
	}
	else
	{
	  $ret = FALSE;
	}
  }
  return $ret;
}

function setUserPassword($funcPass)
{
  $funcUser = getUsername();
  // checks it against the database
  $query = "SELECT * FROM users WHERE username = '".$funcUser."'";
  $check = mysql_query($query)or die(mysql_error());

  //Gives error if user dosen't exist
  if (mysql_num_rows($check) == 0)
  {
    return FALSE;
  }
  else
  {
	if ($info = mysql_fetch_array( $check ))
	{
	  // set password hash before returning true
	  $hashed_pass = password_hash($funcPass, PASSWORD_DEFAULT);
	  $update_member = mysql_query("UPDATE users SET sha256_pass='$hashed_pass' WHERE username='$funcUser'");
	  // clear md5 hash
	  if ($info['pass'] != '') {
	    $update_member = mysql_query("UPDATE users SET pass='' WHERE username='$funcUser'");
	  }
	  return TRUE;
	}
	else
	{
	  return FALSE;
	}
  }
}

// returns the stored hashed password
function getUserPasshash($userhash)
{
  $query = "SELECT sha256_pass FROM users WHERE sha256_user = '".$userhash."'";

  $check = mysql_query($query);

  if (!$check)
  {
    return "";
  }
  while($info = mysql_fetch_array( $check ))
  {
    return $info['sha256_pass'];
  }
  return "";
}

function getUsername()
{
  $query = "SELECT username FROM users WHERE sha256_user = '".getCookie('ID')."'";

  $check = mysql_query($query);

  if (!$check)
  {
    return "";
  }
  if($info = mysql_fetch_array( $check ))
  {
    return $info['username'];
  }
  return "";
}

function logLogin($user_hash)
{
  $filename = "logs/login_".date('Y')."_".date('m').".xml";
  if (!file_exists($filename))
  {
    file_put_contents($filename, "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?>\n", FILE_APPEND | LOCK_EX | FILE_TEXT);
    chmod($filename, 0600);
  }

  $entry = "<login date=\"".date('c')."\">\n";
  $entry .= "  <user_hash>".$user_hash."</user_hash>\n";
  foreach ($_SERVER as $key => $value)
  {
    $entry .= "  <".$key.">".$value."</".$key.">\n";
  }
  $entry .= "</login>\n";
  file_put_contents($filename, $entry, FILE_APPEND | LOCK_EX | FILE_TEXT);
}

function logLogout($user_hash)
{
  $filename = "logs/logout_".date('Y')."_".date('m').".xml";
  if (!file_exists($filename))
  {
    file_put_contents($filename, "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?>\n", FILE_APPEND | LOCK_EX | FILE_TEXT);
    chmod($filename, 0600);
  }

  $entry = "<logout date=\"".date('c')."\">\n";
  $entry .= "  <user_hash>".$user_hash."</user_hash>\n";
  foreach ($_SERVER as $key => $value)
  {
    $entry .= "  <".$key.">".$value."</".$key.">\n";
  }
  $entry .= "</logout>\n";
  file_put_contents($filename, $entry, FILE_APPEND | LOCK_EX | FILE_TEXT);
}

function logVerify($user_hash, $valid)
{
  $filename = "logs/verify_".date('Y')."_".date('m').".xml";
  if (!file_exists($filename))
  {
    file_put_contents($filename, "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?>\n", FILE_APPEND | LOCK_EX | FILE_TEXT);
    chmod($filename, 0600);
  }

  $entry = "<verify date=\"".date('c')."\">\n";
  $entry .= "  <isvalid>".$valid."</isvalid>\n";
  $entry .= "  <user_hash>".$user_hash."</user_hash>\n";

  if ($valid == "INVALID")
  {
    $entry = "  <SERVER>\n";
    foreach ($_SERVER as $key => $value)
    {
      $entry .= "    <".$key.">".$value."</".$key.">\n";
    }
    $entry .= "  </SERVER>\n";

    $entry .= "  <REQUEST>\n";
    foreach ($_REQUEST as $key => $value)
    {
      $entry .= "    <".$key.">".$value."</".$key.">\n";
    }
    $entry .= "  </REQUEST>\n";

    $entry .= "  <COOKIE>\n";
    foreach ($_COOKIE as $key => $value)
    {
      $entry .= "    <".$key.">".$value."</".$key.">\n";
    }
    $entry .= "  </COOKIE>\n";

    $entry .= "  <POST>\n";
    foreach ($_POST as $key => $value)
    {
      $entry .= "    <".$key.">".$value."</".$key.">\n";
    }
    $entry .= "  </POST>\n";

    $entry .= "  <GET>\n";
    foreach ($_GET as $key => $value)
    {
      $entry .= "    <".$key.">".$value."</".$key.">\n";
    }
    $entry .= "  </GET>\n";
  }
  $entry .= "</verify>\n";
  file_put_contents($filename, $entry, FILE_APPEND | LOCK_EX | FILE_TEXT);
}

function isNumberAvailable($username, $number)
{
    $check = mysql_query("SELECT * FROM vehicles");
    while($info = mysql_fetch_array( $check ))
    {
      if (($info['userOwner'] != $username) &&
          ($info['number'] == $number))
      {
        return FALSE;
      }
    }
    return TRUE;
}

function isVehicleRegistered($qVehID)
{
  $today = date('Y-m-d');
  $eventCheck = mysql_query("SELECT * FROM events") or die(mysql_error());
  while ($eventInfo = mysql_fetch_assoc($eventCheck))
  {
    $eventDBName = $eventInfo['eventDB'];
    $eventUserCheck = mysql_query("SELECT * FROM $eventDBName")or die(mysql_error());
    while ($eventUserInfo = mysql_fetch_assoc( $eventUserCheck ))
    {
      $eventTempVehID = $eventUserInfo['vehicleKey'];
      if ($eventTempVehID == $qVehID &&
	      $today <= $eventInfo['eventDate'])
      {
        return 1;
      }
    }
  }
  return 0;
}

function deleteVehicleFromEvents($qVehID)
{
  $eventCheck = mysql_query("SELECT * FROM events") or die(mysql_error());
  while ($eventInfo = mysql_fetch_assoc($eventCheck))
  {
    $eventDBName = $eventInfo['eventDB'];
    $eventUserCheck = mysql_query("SELECT * FROM $eventDBName")or die(mysql_error());
    while ($eventUserInfo = mysql_fetch_assoc( $eventUserCheck ))
    {
      $eventTempVehID = $eventUserInfo['vehicleKey'];
      if ($eventTempVehID == $qVehID)
      {
        $deleteQuery = "DELETE FROM $eventDBName WHERE vehicleKey = '$qVehID'";
		$delCheck = mysql_query($deleteQuery);
      }
    }
  }
}

function doesUserHaveVehicles()
{
  $username = getUsername();
  $query = "SELECT * FROM vehicles WHERE userOwner = '$username'";
  $check = mysql_query($query);
  if (!$check)
    return FALSE;
  else
  {
	$info = mysql_fetch_array( $check );
    if ($info)
      return TRUE;
    else
      return FALSE;
  }
}

function sendEmail($to, $from, $subject, $message)
{
  require 'include/configGlobals.php';
  $filename = "data/email/errors.txt";

  $headers = "From: ".$from."\r\n";
  $headers .= "Reply-To: ".$from."\r\n";
  $headers .= "MIME-Version: 1.0\n";
  $headers .= "Content-Type: multipart/alternative; boundary=\"$mime_boundary\"\n";

  $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
  $headers .= "Content-Transfer-Encoding: 8bit\r\n";

  $message = wordwrap($message, 70, "\n", 1);

  $ret = mail($to, $subject, $message, $headers);
  if ($ret == false)
  {
    $content = "ERROR OCCURED\n";
    $content .= date("r")."\n";
    $content .= "To: [".$to."]\n";
    $content .= "From: [".$from."]\n";
    $content .= "Subject: [".$subject."]\n\n";
    $content .= "Headers:\n";
    $content .= $headers."\n\n";
    $content .= "Message:\n";
    $content .= $message."\n";
    $content .= "END Message\n\n\n";
    file_put_contents($filename, $content, FILE_APPEND | LOCK_EX | FILE_TEXT);
    chmod($filename, 0600);
  }
  return $ret;
}

function echoMainHeader()
{
  require 'include/configGlobals.php';
//  header("X-Frame-Options: DENY"); 

  // HTML 4.01 Transitional
  echo "<!DOCTYPE HTML\n";
  echo "          PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\"\n";
  echo "          \"http://www.w3.org/TR/html4/loose.dtd\">\n";
  echo "<html>\n";
  // END HTML 4.01 Transitional

  echo "<head>\n";
  echo "  <meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\">\n";
  echo "  <title>".$club_Abbr." Online Registration</title>\n";

  echo file_get_contents("html/header.html");
}

function echoMainFooter()
{
  echo file_get_contents("html/footer.html");
}

function echoFrameHeader()
{
//  header("X-Frame-Options: SAMEORIGIN"); 
  echo file_get_contents("html/frameHeader.html");
}

function echoFrameFooter()
{
  echo file_get_contents("html/frameFooter.html");
}
?>
