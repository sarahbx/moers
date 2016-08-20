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

function displayUsersPage()
{
  $hashUsername = getCookie('ID');
  $sessionId = getCookie('Session_ID');

  $check = mysql_query("SELECT * FROM users WHERE sha256_user = '$hashUsername'")or die(mysql_error());
  $info = mysql_fetch_array( $check );

  if ($info['admin'] != 1)
    header("Location: logout.php");

  if (isset($_POST['emailUsers']))
  {
    $emailList = "";
    $userCheck = mysql_query("SELECT * FROM users")or die(mysql_error());
    while($userInfo = mysql_fetch_array( $userCheck ))
    {
      $emailList .= $userInfo['email'];
    }
  }

  echo file_get_contents("admin_header.html");
  echo "<br />\n";
  echo date('c')."<br />\n";
  echo "<table class=\"default\"><tr>\n";
  echo "<td>Username</td>\n";
  echo "<td>Full Name</td>\n";
  echo "<td>Email</td>\n";
  echo "<td>Member Type</td>\n";
  echo "<td>Member of</td>\n";
  echo "<td>Creation</td>\n";
  echo "<td>Last Login</td>\n";
  echo "</tr>\n";
  $userCount = 0;

  $userCheck = mysql_query("SELECT * FROM users ORDER BY lastLogon DESC")or die(mysql_error());
  while($userInfo = mysql_fetch_array( $userCheck ))
  {
    $userCount += 1;
    echo "<tr>\n";

    if ($userInfo['admin'] != 1)
      echo "<td><a href=\"#\" onClick=\"parent.main_openPopupWindow('admin_edituserinfo.php?USER=".$userInfo['username']."')\">".$userInfo['username']."</a></td>\n";
    else
      echo "<td>".$userInfo['username']."</td>\n";

    echo "<td>".$userInfo['fname']." ".$userInfo['lname']."</td>\n";
    echo "<td>".$userInfo['email']."</td>\n";

    if ($userInfo['member'] == 0)
    {
      echo "<td>Non-Member</td>\n";
    }
    else if ($userInfo['member'] == 1)
    {
      echo "<td>Member-Online</td>\n";
    }
    else if ($userInfo['member'] == 2)
    {
      echo "<td>Member-Offline</td>\n";
    }
    else if ($userInfo['member'] == 3)
    {
      echo "<td>Partner-Member</td>\n";
    }
    else
    {
      echo "<td>ERROR</td>\n";
    }

    echo "<td>".$userInfo['club']."</td>\n";
    echo "<td>".$userInfo['creation']."</td>\n";
    echo "<td>".$userInfo['lastLogon']."</td>\n";

    echo "</tr>\n";
  }
  echo "</table>\n";
  echo "<br /><br />Total Number of Users: ".$userCount."<br />\n";
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

  displayUsersPage();

  die(); // attempt to guard against any code insertion at the end of the file
}
?>