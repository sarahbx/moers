<?php
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

///////////////////////////////////////////////////////////////////////////////////////
//This code runs if the form has been submitted
function registerFormSubmitted()
{
  require 'include/configGlobals.php';

  connectDatabase();
  slashAllInputs();

  //This makes sure they did not leave any fields blank
  if (!$_POST['username'] | !$_POST['email'] | !$_POST['firstName'] | !$_POST['lastName']) {
    die('You did not complete all of the required fields');
  }

  if (!isUsernameValid($_POST['username']))
  {
    die('Sorry, that username is invalid. Please go back and try again.');
  }

  // checks if the username is in use
  $usercheck = $_POST['username'];
  $check = mysql_query("SELECT username FROM users WHERE username = '$usercheck'") or die(mysql_error());
  $check2 = mysql_num_rows($check);

  //if the name exists it gives an error
  if ($check2 != 0)
  {
    die('Sorry, the username '.$_POST['username'].' is already in use. Please go back and try again.');
  }

  $emailcheck = $_POST['email'];
  $check = mysql_query("SELECT email FROM users WHERE email = '$emailcheck'") or die(mysql_error());
  $check2 = mysql_num_rows($check);

  //if the email exists it gives an error
  if ($check2 != 0) 
  {
    die('Sorry, the email '.$_POST['email'].' has already been registered. Please go back and try again.');
  }

  $tempPassword = rand_string(16);
  
  // here we encrypt the password and add slashes if needed
  $hashPassword = md5($tempPassword);
  $hashUsername = md5($_POST['username']);
  $hash256Password = bin2hex(mhash(MHASH_SHA256, $tempPassword));
  $hash256Username = bin2hex(mhash(MHASH_SHA256, $_POST['username']));
  
  $creationDate = date('Y-m-d');
  // now we insert it into the database
  $insert = "INSERT INTO users (username, pass, sha256_user, sha256_pass, fname, lname, addr1, addr2, city, state, zip, hphone, cphone, email, econtact, econtact_phone, econtact_rel, creation) VALUES (
           '".$_POST['username']."',
           '".$hashPassword."',
		   '".$hash256Username."',
		   '".$hash256Password."',
           '".$_POST['firstName']."',
           '".$_POST['lastName']."',
           '".$_POST['address1']."',
           '".$_POST['address2']."',
           '".$_POST['city']."',
           '".$_POST['state']."',
           '".$_POST['zipCode']."',
           '".$_POST['homePhone']."',
           '".$_POST['cellPhone']."',
           '".$_POST['email']."',
           '".$_POST['econtact']."',
           '".$_POST['econtactPhone']."',
           '".$_POST['econtactRel']."',
           '".$creationDate."'
           )";

  $add_member = mysql_query($insert);

  $to      = $_POST['email'];
  $from    = $email_Administrator;
  $subject = 'Registered on '.$club_Abbr.' Online Registration Site';
  $message = "--$mime_boundary\n";
  $message .= "Content-Type: text/plain; charset=UTF-8\r\n";
  $message .= "Content-Transfer-Encoding: 8bit\r\n";
  $message .= 'Thank you for registering on the '.$club_Abbr.' Online Registration site.'."\n".
           "\n".
           'Your username is: [ '.$usercheck." ]\n".
           'Your temporary password is: [ '.$tempPassword." ]\n".
           "\n".
           'Login at '.$http_Logout.' to change your password and register for events.'."\n".
           "\n".
           'Thank you!'."\n".
           '- '.$club_Abbr.' Administration'."\n";
  $message .= "--$mime_boundary--\n\n";

  if (sendEmail($to, $from, $subject, $message) != false)
  {
    echo "<h1>Registered</h1>\n";
    echo "Thank you, you have registered. An email has been sent to ".$to." \n";
    echo "with your username and temporary password. Depending on internal server traffic, this may take some time.<br><br>\n";
    echo "When you receive your temporary password you may <a href=\"index.php\">login</a> to continue.\n";
  }
  else
  {
    echo "<h1>Internal Email Error. Please contact administrator at ".$email_Administrator."</h1>\n";
  }
}

function displayRegisterForm()
{
  echo file_get_contents("html/register.html");
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
  echoMainHeader();
  if (isset($_POST['submit']))
  {
    registerFormSubmitted();
  }
  else
  {
    displayRegisterForm();
  }
  echoMainFooter();
  die();
}
?>