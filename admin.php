<?php
/************************************************************************************
    Copyright © 2008-2009 xhub.com

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

function adminDisplayPage()
{
  $hashUsername = getCookie('ID');

  $check = mysql_query("SELECT * FROM users WHERE sha256_user = '$hashUsername'")or die(mysql_error());
  $info = mysql_fetch_array( $check );

  $username = $info['username'];

  if ($info['admin'] != 1)
    header("Location: logout.php");

  echo file_get_contents("admin_header.html");

//  $online = "online";
//  $systemCheck = mysql_query("SELECT * FROM system WHERE option = '$online'") or die(mysql_error());
//  $systemInfo = mysql_fetch_array( $systemCheck );

//  echo "<form action=\"admin.php\" method=\"POST\">\n";
//  echo "<input type=\"text\" name=\"offlineData\" value=\"".$systemInfo['data1']."\">\n";
//  if ($systemInfo['data0'] == "yes")
//  {
//    echo "<input type=\"submit\" name=\"offlineDataSubmit\" value=\"Set Offline\">\n";
//  }
//  else
//  {
//    echo "<input type=\"submit\" name=\"offlineDataSubmit\" value=\"Set Online\">\n";
//  }
//  echo "</form>\n";

}

function postSystemOnline()
{

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
  
  if (isset($_POST['offlineDataSubmit']))
  {
    postSystemOnline();
  }
  else
  {
    adminDisplayPage();
  }
  die(); // attempt to guard against any code insertion at the end of the file
}
?>