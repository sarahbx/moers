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
    must be removed from all files before distribution.

************************************************************************************/
require_once 'include/functions.php';

function logoutUser()
{
  header("Content-Type: text/html; charset=utf-8");

  logLogout(getCookie('ID'));

  if (getCookie('ID'))
  {
      connectDatabase();
      slashArray($_COOKIE);

      // reset session id
      $sessionId = rand_string(32);
      $update = "UPDATE users SET session_id='$sessionId' WHERE sha256_user='".getCookie('ID')."'";
      $result = mysql_query($update);
      $sessionId = rand_string(32);
  }

  //this deletes the cookies
  clearCookies();

  header("Location: index.php");
}

logoutUser(); //goodbye.
die();
?>