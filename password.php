<?php
/************************************************************************************
    Copyright © 2008-2010 xhub.com

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

require_once 'include/functions.php';

//This code runs if the form has been submitted
function passwordFormSubmitted()
{
  if ($_POST['pass1'] != $_POST['pass2'])
  {
    dieError("ERROR: Passwords do not match"); // has javascript checking, we should never hit this error.
  }

  if (!isValidUserPassword(getUsername(), $_POST['pass0']))
  {
    echo "<h2>Incorrect Old password entered. Please try again.</h2>\n";
    echo file_get_contents("html/password.html");
    die(' ');
  }

  // now we insert it into the database
  if (setUserPassword($_POST['pass1']))
  {
    echo "<h1>Password Changed</h1>\n";
    echo "<form><input type=\"button\" name=\"continue\" value=\"Continue\" onClick=\"parent.main_popupWindowCancel()\"></form>\n";
  }
  else
  {
    dieError("Password Change Failed. Please contact the administrator");
  }
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

  echoFrameHeader();

  if (isset($_POST['submitChange']))
  {
    passwordFormSubmitted();
  }
  else
  {
	echo file_get_contents("html/password.html");
  }

  echoFrameFooter();

  die();
}
?>