<?php
// main.php
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

function displayMainPage()
{
  $query = "SELECT username, admin FROM users WHERE sha256_user = '".getCookie('ID')."'";

  $check = mysql_query($query)or die(mysql_error());
  $info = mysql_fetch_array($check);

  echoMainHeader();

  echo "<div>\n";
  echo "Welcome <b>'".$info['username']."'</b> ";
  if ($info['admin'] == 1)
  {
    echo " | <a href=\"#\" onClick=\"main_setBodyFrame('body.php')\">Home Page</a>\n";
    echo " | <a href=\"#\" onClick=\"main_setBodyFrame('admin.php')\">Admin Page</a>\n";
  }
  echo "</div>\n";

  echo "<div class=\"class_body\" id=\"div_body\">\n";
  echo "<script language=\"javascript\" type=\"text/javascript\">main_showLoader();</script>\n";
  echo "<iframe class=\"class_iframe_body\" id=\"iframe_body\" src=\"body.php\"></iframe>\n";
  echo "</div>\n";
  
  echoMainFooter();
}

if (!isSSL())
{
  header("Location: logout.php");
}
else
{
  validateSession();

  displayMainPage();

  die(); // attempt to guard against any code insertion at the end of the file
}
?>