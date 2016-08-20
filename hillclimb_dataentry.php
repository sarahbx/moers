<?php
// hillclimb_dataentry.php
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

function processData()
{
  $filename = "data/hillclimb/".$_POST['itemNumber'].".txt";
  if (file_exists($filename))
  {
    unlink($filename);
  }
  $postData = $_POST;
  foreach($postData as $key => $value)
  {
    file_put_contents($filename, $key." = ".$value."\n", FILE_APPEND | LOCK_EX | FILE_TEXT);
  }
  unset($value);
 
  chmod($filename, 0600);

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

  processData();
  
  header("Location: entryHillclimbPaypal.php?".$_POST['itemNumber']);

  die(); // attempt to guard against any code insertion at the end of the file
}

?>