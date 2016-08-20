<?php
// forgot.php
/************************************************************************************
    Copyright � 2008-2010 xhub.com

    Sarah Bennert
    sarah@xhub.com

    This file is part of the Motorsports Online Event Registration System (MOERS).

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

require_once 'include/config.php';
require_once 'include/functions.php';

function forgotFormSubmitted()
{
  require 'include/configGlobals.php';

  // Connects to your Database
  connectDatabase();
  slashAllInputs();

  //This makes sure they did not leave any fields blank
  if (!$_POST['email']) {
    die('You did not complete all of the required fields');
  }

  // checks if the email is in use
  $emailcheck = $_POST['email'];
  $check = mysql_query("SELECT username FROM users WHERE email = '$emailcheck'") or die(mysql_error());
  $check2 = mysql_num_rows($check);

  //if the email doesn't exists it gives an error
  if ($check2 == 0) {
    die('Sorry, no user with email '.$emailcheck.' is registered in the database. Please try again.');
  }

  while($info = mysql_fetch_array( $check ))
  {
    $usercheck = $info['username'];
  }

  $tempPassword = rand_string(16);
  
  // here we encrypt the password
  $sha256_pass = bin2hex(mhash(MHASH_SHA256, $tempPassword));
  // now we insert it into the database
  $update_member = mysql_query("UPDATE users SET sha256_pass='$sha256_pass' WHERE username='$usercheck'");
  $sha256_pass = rand_string(128);
  // clear md5 hash
  $update_member = mysql_query("UPDATE users SET pass='' WHERE username='$usercheck'");

  $to      = $emailcheck;
  $from    = $email_Administrator;
  $subject = 'Reset Info for '.$club_Abbr.' Online Registration Site';
  $message = "--$mime_boundary\n";
  $message .= "Content-Type: text/plain; charset=UTF-8\r\n";
  $message .= "Content-Transfer-Encoding: 8bit\r\n";
  $message .= 'Your password has been reset on the '.$club_Abbr.' Online Registration site at your request.'."\n".
           "\n".
           'Your username is: [ '.$usercheck." ]\n".
           'Your temporary password is: [ '.$tempPassword." ]\n".
           "\n".
           'Login at '.$http_Logout.' to change your password and register for events.'."\n".
           "\n".
           'Thank you!'."\n".
           '- '.$club_Abbr.' Administration'."\n";
  $message .="--$mime_boundary--\n\n";
  sendEmail($to, $from, $subject, $message);

  $tempPassword = rand_string(16); // clear variable data

  echoMainHeader();

  echo "<h1>Email Sent.</h1>\n";
  echo "Thank you, you have registered. An email has been sent to ".$_POST['email']." \n";
  echo "with your username and temporary password. Depending on internal server traffic, this may take some time.<br><br>\n";
  echo "When you receive your temporary password you may <a href=\"".$http_Logout."\">login</a> to continue.\n";

  echoMainFooter();
}


function displayForgotForm()
{
  echoMainHeader();

  echo "You forgot your password?!<br>\n";
  echo "<br>\n";
  echo "<form action=\"forgot.php\" method=\"post\">\n";
  echo "<table border=\"0\">\n";
  echo "<tr><td>Email:</td><td>\n";
  echo "<input type=\"text\" name=\"email\" maxlength=\"60\">\n";
  echo "</td></tr>\n";
  echo "<tr><th colspan=\"2\"><input type=\"submit\" name=\"submit\" value=\"Submit\"></th></tr> </table>\n";
  echo "</form>\n";
  echo "<font size=\"5\"><b><u>The email address you registered with must be entered. Your username and a temporary password will be emailed to you</u></b></font><br>\n";

  echoMainFooter();
}

//////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////
////////////BEGIN SCRIPT EXECUTION BELOW//////////////////////
//////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////

if (!isSSL())
{
  header("Location: logout.php");
}
else
{
  if (isset($_POST['submit']))
  {
    forgotFormSubmitted();
  }
  else
  {
    displayForgotForm();
  }

  die();
}
?>