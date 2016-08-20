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

function displayEventPage()
{
  $hashUsername = getCookie('ID');

  $check = mysql_query("SELECT * FROM users WHERE sha256_user = '$hashUsername'")or die(mysql_error());
  while($info = mysql_fetch_array( $check ))
  {
    $username = $info['username'];

    $eventDB = $_POST['regEventName'];

    if (isset($_POST['setRegistration']))
    {
      $eventType = $_POST['regEventType'];

      $vehicle = $_POST['vehID'];
      $vehicleClass = $_POST['regVehClass'];
      $vehicleNumber = $_POST['regVehNumber'];
        
      if (!$vehicleNumber)
      {
		echoFrameHeader();
        die("Vehicle number not entered");
      }

      // Make sure the number is not already registered for the event.
      $numberQuery = "SELECT * FROM $eventDB";
      $numberCheck = mysql_query($numberQuery) or die(mysql_error());
      while ($numberInfo = mysql_fetch_assoc($numberCheck))
      {
        if ($numberInfo['vehicleNumber'] == $vehicleNumber)
        {
			echoFrameHeader();
          die("Vehicle number already registered for this event. Please go back, change your car number, and try again.</body></html>");
        }
      }

      $update = "INSERT INTO $eventDB (registeredUser, vehicleKey, vehicleClass, vehicleNumber) VALUES ('$username', '$vehicle', '$vehicleClass', '$vehicleNumber')";
//      echo "Vehicle ID: ".$vehicle."<br>";
//	echo "EventDB: ".$eventDB."<br>";
//	echo "User: ".$username."<br>";

	    if ($insertCheck = mysql_query($update))
      {
        header("Location: eventList.php");
      }
      else
      {
		echoFrameHeader();
        die("Event Registration Failed. Contact Administrator.</body></html>");
      }
    }
    else if (isset($_POST['modifyRegistration']))
    {
      $eventType = $_POST['regEventType'];

      $vehicle = $_POST['vehID'];
      $vehicleClass = $_POST['regVehClass'];
      $vehicleNumber = $_POST['regVehNumber'];
        
      if (!$vehicleNumber)
      {
		echoFrameHeader();
        die("Vehicle number not entered");
      }

      // Make sure the number is not already registered for the event.
      $numberQuery = "SELECT * FROM $eventDB WHERE registeredUser != '$username'";
      $numberCheck = mysql_query($numberQuery) or die(mysql_error());
      while ($numberInfo = mysql_fetch_assoc($numberCheck))
      {
        if ($numberInfo['vehicleNumber'] == $vehicleNumber)
        {
		  echoFrameHeader();
          die("Vehicle number already registered for this event. Please go back, change your car number, and try again.</body></html>");
        }
      }

      $update = "UPDATE $eventDB SET vehicleKey='$vehicle', vehicleClass='$vehicleClass', vehicleNumber='$vehicleNumber' WHERE registeredUser='$username'";
//      echo "Vehicle ID: ".$vehicle."<br>";
//	echo "EventDB: ".$eventDB."<br>";
//	echo "User: ".$username."<br>";

	    if ($updateCheck = mysql_query($update))
      {
        header("Location: eventList.php");
      }
      else
      {
        echoFrameHeader();
        die("Event Registration Failed. Contact Administrator.");
      }
    }
    else if (isset($_POST['register_x']) || isset($_POST['editregistration_x']))
    {
	  echoFrameHeader();

	  echo "<script type=\"text/javascript\">\n";
	  echo "parent.main_enablePopupBackButton('eventList.php');\n";
	  echo "</script>\n";

      // Display events
      $eventcheck = mysql_query("SELECT * FROM events WHERE eventDB='$eventDB'") or die(mysql_error());
      echo "<h2>Event:</h2>";
      echo "<table class=\"default\">";
      echo "<tr><td>Event Type</td><td>Event Name</td><td>Event Location</td><td>Event Date</td><td>Pre-registered</tr>";
      while($eventinfo = mysql_fetch_assoc( $eventcheck ))
      {
        $tempEventType = $eventinfo['eventType'];
        echo "<tr>";
        echo "<td>".$eventinfo['eventType']."</td>";
        echo "<td>".$eventinfo['eventName']."</td>";
        echo "<td>".$eventinfo['eventLocation']."</td>";
        echo "<td>".$eventinfo['eventDate']."</td>";
        echo "<td>";
    
        $tempquery = "SELECT * FROM ".$eventDB;
        $tempEventcheck = mysql_query($tempquery) or die(mysql_error());
        $reg_count = 0;
        $userRegistered = 0;
        while ($tempEventInfo = mysql_fetch_assoc( $tempEventcheck ))
        {
          $reg_count = $reg_count + 1;
          if ($tempEventInfo['registeredUser'] == $info['username'])
          {
            $userRegistered = 1;
          }
        }
        echo $reg_count;
        echo "</td>";
        echo "</tr>";
      }
      echo "</table>";
        
	// Display vehicles
      $vehcheck = mysql_query("SELECT * FROM vehicles WHERE userOwner = '$username'")or die(mysql_error());
      echo "<h2>Pick vehicle to register:</h2>";
      echo "<table class=\"default\">";
      echo "<tr><td>Year</td><td>Make</td><td>Model</td><td>Color</td><td>Treadware</td><td>SCCA Class</td><td>SCCNH Class</td><td>Requested Number</td></tr>";
      while ($vehinfo = mysql_fetch_assoc( $vehcheck ))
      {
        echo "<form action=\"events.php\" method=\"POST\">";
        echo "<tr>";
        echo "<td>".$vehinfo['year']."</td>";
        echo "<td>".$vehinfo['make']."</td>";
        echo "<td>".$vehinfo['model']."</td>";
        echo "<td>".$vehinfo['color']."</td>";
        echo "<td>".$vehinfo['treadware']."</td>";
        echo "<td>".$vehinfo['scca_class']."</td>";
        echo "<td>".$vehinfo['sccnh_class']."</td>";
	      echo "<td>".$vehinfo['number']."</td>";
	      echo "<td>";
	      echo "<input type=\"hidden\" name=\"vehID\" value=\"".$vehinfo['vehicleID']."\">";
        echo "<input type=\"hidden\" name=\"regVehNumber\" value=\"".$vehinfo['number']."\">";
        echo "<input type=\"hidden\" name=\"regEventName\" value=\"".$eventDB."\">";
        echo "<input type=\"hidden\" name=\"regEventType\" value=\"".$tempEventType."\">";
        echo "<input type=\"hidden\" name=\"regVehClass\" value=\"".$vehinfo['sccnh_class']."\">";
        if (isset($_POST['register_x']))
        {
          echo "<input type=\"submit\" name=\"setRegistration\" value=\"Register Vehicle\">";
        }
        else if (isset($_POST['editregistration_x']))
        {
          echo "<input type=\"submit\" name=\"modifyRegistration\" value=\"Change To This Vehicle\">";
        }
	      echo "</td></tr>";
	      echo "</form>";
      }
      echo "</table><br>";
      echo "<hr><h2>Registered Users:</h2>";
      $regUsercheck = mysql_query("SELECT * FROM $eventDB ORDER BY `$eventDB`.`vehicleClass`")or die(mysql_error());
      echo "<table class=\"default\">";
      echo "<tr><td>SCCNH Class</td><td>Car Number</td><td>First Name</td><td>Vehicle</td><td>SCCA Class</td></tr>";
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
//          echo "<td>".$tempRegUser."</td>";
          echo "<td>".$tempinfo['fname']."</td>";
            
          $tempRegVeh = $reguserinfo['vehicleKey'];
          $tempRegQuery = "SELECT * FROM vehicles WHERE vehicleID='$tempRegVeh'";
          $tempvehcheck = mysql_query($tempRegQuery);
          if($tempvehinfo = mysql_fetch_array( $tempvehcheck ))
          {
            echo "<td>".$tempvehinfo['year']." ".$tempvehinfo['make']." ".$tempvehinfo['model']."</td>";
            echo "<td>".$tempvehinfo['scca_class']."</td>";
//            echo "<td>".$tempvehinfo['sccnh_class']."</td>";
          }
          echo "</tr>";
        }
      }
      echo "</table>";
    }	
    elseif (isset($_POST['unregister_x']))
    {
      $unreg = "DELETE FROM $eventDB WHERE registeredUser='$username'";
      $querycheck = mysql_query($unreg);
      header("Location: eventList.php");
    }
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

  displayEventPage();

  echoFrameFooter();

  die(); // attempt to guard against any code insertion at the end of the file
}
?>