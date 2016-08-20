<?php
/************************************************************************************
    Copyright © 2008-2010 xhub.com

    Sarah Bennert
    sarah@xhub.com

    This file is part of the Motorsports Online Event Registration System (MOERS).

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.

    Any system sensitive data such as IP addresses, usernames, and passwords 
    must be removed from any files before distribution.

************************************************************************************/

require_once 'include/functions.php';

function displayLogin()
{
  include 'include/login_backup.php';
}

function displaySystemOffline()
{
  echoMainHeader();

  echo "<h2>System offline for updates.</h2>\n";
  echo "<h3>Please check back later.</h3>\n";
  echo "<h4>Thank you. -Sarah</h4>\n";

  echoMainFooter();
  die();
}

function displaySystemOfflineYear()
{
  echoMainHeader();

  echo "<h2>System offline for updates.</h2>\n";
  echo "<h3>Will be online again by April 5, 2010 12:00am EST</h3>\n";
  echo "<h4>Thank you for your continued patience.<br />Working on making it better for you. :)<br />-Sarah</h4>\n";

  echoMainFooter();
  die();
}

function loginUser()
{
  $username = $_POST['username'];

  // checks it against the database
  $query = "SELECT * FROM users WHERE username = '".$username."'";
  $check = mysql_query($query)or die(mysql_error());

  if($info = mysql_fetch_array( $check ))
  {
    $sessionId = rand_string(32);

    // update lastLogon & session id
    $now = date('c');
    $update = "UPDATE users SET lastLogon='$now', session_id='$sessionId' WHERE username='$username'";
    $result = mysql_query($update) or die(mysql_error());

    $hashUsername = $info['sha256_user'];

    // if login is ok then we add a cookie
    setCookies($hashUsername, $sessionId);

    $sessionId = rand_string(32);
    logLogin($hashUsername);

    //then redirect them to the members area
    header('Location: main.php');
  }
  else
  {
    dieError("ERROR: Cannot find user record in database. Please contact the administrator");
  }
}

//////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////
////////////BEGIN SCRIPT EXECUTION BELOW//////////////////////
//////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////

//displaySystemOffline();
//displaySystemOfflineYear();

//Checks if there is a login cookie
//if there is, it logs you in and directes you to the members page
if (!isset($_POST['submit']) && !getCookie('ID'))
{
  // if they are not logged in
  echoMainHeader();
  echo "<div align=\"center\" valign=\"center\">";
  displayLogin();
  echo "</div>";
  echoMainFooter();
}
else if (!isset($_POST['submit']) && getCookie('ID'))
{
  validateSession();
  header('Location: main.php');
}
else if (isset($_POST['submit']))
{
  // Clean arrays to prevent injection attacks
  slashAllInputs();
  // Connects to your Database
  connectDatabase();
  
    // makes sure they filled it in
  if(!$_POST['username'] || !$_POST['password'])
  {
    echoMainHeader();
    echo "<h2>You did not fill in a required field.</h2>\n";
    displayLogin();
  }
  else
  {
    //Gives error if user dosen't exist
    if (!doesUserExist($_POST['username']))
    {
      echoMainHeader();
      echo "<h2>That user does not exist in our database.</h2>\n";
      displayLogin();
    }
    else if (isValidUserPassword($_POST['username'], $_POST['password']))
    {
      loginUser();
    }
    else
    {
      echoMainHeader();
      echo "Incorrect password, please try again.\n";
    }
  }
  echoMainFooter();
}
die();
?>