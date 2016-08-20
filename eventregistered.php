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

require_once 'include/config.php';
require_once 'include/functions.php';
require_once 'include/download.php';

function displayRegisteredEntrants()
{
  require 'include/configGlobals.php';

  $eventDB = $_POST['regEventName'];
  if (isset($_POST['downloadEntries']))
  {
    downloadEntries($_POST['regEventKey']);
  }
  else if (isset($_POST['downloadMember']))
  {
    downloadMember($_POST['regEventKey']);
  }
  else if (isset($_POST['downloadPrintable']))
  {
    downloadPrintableList($_POST['regEventKey']);
  }
  else if (isset($_POST['downloadAllFiles'])) // sarahb 7/17/08 - added option to download all files
  {
    downloadAllFiles($_POST['regEventKey']);
  }
  else if (isset($_POST['showRegistration']))
  {
    echoFrameHeader();

    echo "<script type=\"text/javascript\">\n";
    echo "parent.main_enablePopupBackButton('eventList.php');\n";
    echo "</script>\n";
	
    // Display events
    $eventcheck = mysql_query("SELECT * FROM events WHERE eventDB='$eventDB'") or die(mysql_error());
    echo "<h2>Event:</h2>";
    echo "<table class=\"default\">";
    echo "<tr><td>Event Name</td><td>Event Location</td><td>Event Date</td><td>Pre-registered</tr>";
    while($eventinfo = mysql_fetch_assoc( $eventcheck ))
    {
      echo "<tr>";
      echo "<td>".$eventinfo['eventName']."</td>";
      echo "<td>".$eventinfo['eventLocation']."</td>";
      echo "<td>".$eventinfo['eventDate']."</td>";
      echo "<td>";

      $tempquery = "SELECT * FROM ".$eventDB;
      $tempEventcheck = mysql_query($tempquery) or die(mysql_error());
      $reg_count = 0;
      while ($tempEventInfo = mysql_fetch_assoc( $tempEventcheck ))
      {
        $reg_count = $reg_count + 1;
      }
      echo $reg_count;
      echo "</td>";
      echo "</tr>";
    }

    echo "</table><br>";
    echo "<hr><h2>Registered Users:</h2>";
    $regUsercheck = mysql_query("SELECT * FROM $eventDB ORDER BY `$eventDB`.`vehicleClass`")or die(mysql_error());
    echo "<table class=\"default\">";
    echo "<tr><td>".$club_Abbr." Class</td><td>Car Number</td><td>First Name</td><td>Vehicle</td><td>SCCA Class</td></tr>";
    while ($reguserinfo = mysql_fetch_assoc( $regUsercheck ))
    {
      $tempRegUser = $reguserinfo['registeredUser'];
      $tempRegQuery = "SELECT * FROM users WHERE username='$tempRegUser'";
      $tempusercheck = mysql_query($tempRegQuery);
      if($tempinfo = mysql_fetch_array( $tempusercheck ))
      {
        echo "<tr>";
        echo "<td>".$reguserinfo['vehicleClass']."</td>";
        echo "<td>".$reguserinfo['vehicleNumber']."</td>";
        echo "<td>".$tempinfo['fname']."</td>";

        $tempRegVeh = $reguserinfo['vehicleKey'];
        $tempRegQuery = "SELECT * FROM vehicles WHERE vehicleID='$tempRegVeh'";
        $tempvehcheck = mysql_query($tempRegQuery);
        if($tempvehinfo = mysql_fetch_array( $tempvehcheck ))
        {
          echo "<td>".$tempvehinfo['year']." ".$tempvehinfo['make']." ".$tempvehinfo['model']."</td>";
          echo "<td>".$tempvehinfo['scca_class']."</td>";
        }
        echo "</tr>";
      }
    }
    echo "</table>";
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

  displayRegisteredEntrants();

  echoFrameFooter();

  die(); // attempt to guard against any code insertion at the end of the file
}

?>