<?php
// feedback.php
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

require_once 'include/config.php';
require_once 'include/functions.php';

function displayFeedbackPage()
{
    $browser = $_SERVER['HTTP_USER_AGENT'];

    $user_hash = getCookie('ID');
    $check = mysql_query("SELECT * FROM users WHERE sha256_user = '$user_hash'")or die(mysql_error());
    while($info = mysql_fetch_array( $check ))
    {
      echo "<h2>Feedback form:</h2><br />\n";
      echo "I do appreciate all feedback, please just make sure it's constructive. Thank you.<br /><hr /><br />\n";
      echo "<form action=\"feedback.php\" method=\"post\">\n";
      echo "<input type=\"hidden\" name=\"Name\" value=\"".$info['fname']." ".$info['lname']."\">\n";
      echo "<input type=\"hidden\" name=\"Username\" value=\"".$info['username']."\">\n";
      echo "<input type=\"hidden\" name=\"Email\" value=\"".$info['email']."\">\n";
      echo "<input type=\"hidden\" name=\"BrowserData\" value=\"$browser\">\n";
      echo "<table class=\"transparent\">\n";
      echo "<tr><td>Username:</td><td>".$info['username']."</td></tr>\n";
      echo "<tr><td>Email:</td><td>".$info['email']."</td></tr>\n";
      echo "<tr><td>Operating System:</td><td>\n";
      echo "<select name=\"OperatingSystem\">\n";
      echo "<option value=\"NotSelected\">SELECT</option>\n";
      echo "<option value=\"MacOS\">Mac OS</option>\n";
      echo "<option value=\"Linux\">Linux Variant</option>\n";
      echo "<option value=\"BSD\">BSD Variant</option>\n";
      echo "<option value=\"Windows\">MS Windows</option>\n";
      echo "<option value=\"OtherOS\">Other (Specify)</option>\n";
      echo "</select>\n";
      echo "<input type=\"text\" name=\"OtherOS:\" />\n";
      echo "</td></tr>\n";
      echo "<tr><td>Web Browser:</td><td>\n";
      echo "<select name=\"Browser\">\n";
      echo "<option value=\"NotSelected\">SELECT</option>\n";
      echo "<option value=\"Firefox\">Firefox</option>\n";
      echo "<option value=\"Chrome\">Chrome</option>\n";
      echo "<option value=\"IE\">Internet Explorer</option>\n";
      echo "<option value=\"Safari\">Safari</option>\n";
      echo "<option value=\"Opera\">Opera</option>\n";
      echo "</select>\n";
      echo "</td></tr>\n";
      echo "<tr><td>Feedback Type:</td><td>\n";
      echo "<select name=\"FeedbackType\">\n";
      echo "<option value=\"NotSelected\">SELECT</option>\n";
      echo "<option value=\"BugReport\">Bug Report</option>\n";
      echo "<option value=\"FeatureRequest\">Feature Request</option>\n";
      echo "<option value=\"GeneralComment\">General Comments</option>\n";
      echo "</select>\n";
      echo "</td></tr>\n";
      echo "<tr><td>Feedback:</td><td><textarea name=\"feedback\" rows=\"3\" cols=\"50\"></textarea></td></tr>\n";
      echo "<tr><td></td><td><input type=\"submit\" name=\"submit\" value=\"Submit Feedback\"></td></tr>\n";
      echo "</table>\n";
      echo "</form>\n";
    }
}

function sendFeedback()
{
  require 'include/configGlobals.php';
  //This makes sure they did not leave any fields blank
  if (!$_POST['feedback']) {
    die('You did not complete all of the required fields');
  }

  $to = $email_Administrator;
  $from = $_POST['Email'];
  $subject = 'moers feedback';

  $message = "--$mime_boundary\n";
  $message .= "Content-Type: text/plain; charset=UTF-8\r\n";
  $message .= "Content-Transfer-Encoding: 8bit\r\n";

  foreach ($_POST as $key => $value)
  {
	if ($value == "")
	  $value = "[]";
	if ($key != "submit")
      $message .= $key." - ".$value."\n";
  }

  $message .= "\n--$mime_boundary\n";
  $message .= "Content-Type: text/html; charset=UTF-8\r\n";
  $message .= "Content-Transfer-Encoding: 8bit\r\n";
  $message .= "<html><body>\n";
  foreach ($_POST as $key => $value)
  {
	if ($value == "")
	  $value = "[]";
	if ($key != "submit")
      $message .= $key." - ".$value."<br />\n";
  }
  $message .= "</body></html>\n";
  $message .= "--$mime_boundary--\n\n";
  
  sendEmail($to, $from, $subject, $message);
  echo "<html><body><script type=\"text/javascript\">parent.main_popupWindowCancel();</script></body></html>";
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
  validateSession();
  
  if (isset($_POST['submit']))
  {
    sendFeedback();
  }
  else
  {
	echoFrameHeader();
    displayFeedbackPage();
    echoFrameFooter();
  }
  die();
}
?>