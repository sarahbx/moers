<?php
// eventList.php
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

require_once 'include/config.php';
require_once 'include/functions.php';
require_once 'include/paypal.php';
require_once 'include/download.php';

function displayEvents($sqlArray)
{
  require 'include/configGlobals.php';
  echo "<script type=\"text/javascript\">\n";
  echo "parent.main_disablePopupBackButton();\n";
  echo "</script>\n";

  // Display events
  $eventcheck = mysql_query("SELECT * FROM events ORDER BY `events`.`eventDate`, `events`.`eventDB` ASC") or die(mysql_error());
  echo "<table class=\"default\" width=\"100%\">\n";
  echo "<th colspan=\"6\">Events</th>\n";
  echo "<tr><td>Event Type</td><td>Event Name</td><td>Event Location</td><td>Event Date</td><td>Pre-registered</td><td>Pre-Registration</td></tr>\n";
  while ($eventinfo = mysql_fetch_assoc( $eventcheck ))
  {
    $paymentStatus = "";
    $today = date('Y-m-d');
		
    if ($eventinfo['eventType'] == "Autocross")
    {
      if ($eventinfo['eventName'] == "All ".date('Y')." Autocross Regular Events")
      {
        $allAutoXEvents = 0x1;
        $regBegin = date('Y-m-d', mktime(0,0,0,
                            substr($eventinfo['eventDate'], 5, 2),
                            substr($eventinfo['eventDate'],8,2)-90, // 3mo entry start
                            substr($eventinfo['eventDate'],0,4)));
      }
      else
      {
        $regBegin = date('Y-m-d', mktime(0,0,0,
                            substr($eventinfo['eventDate'], 5, 2),
                            substr($eventinfo['eventDate'],8,2)-30, // 1mo entry start
                            substr($eventinfo['eventDate'],0,4)));
      }
			$regCutoff = date('Y-m-d', mktime(0,0,0,
                          substr($eventinfo['eventDate'], 5, 2),
                          substr($eventinfo['eventDate'],8,2)-2, // 3 day cutoff
                          substr($eventinfo['eventDate'],0,4)));
		}
		else if ($eventinfo['eventType'] == "Hillclimb")
		{
			$regCutoff = date('Y-m-d', mktime(0,0,0,
                          substr($eventinfo['eventDate'], 5, 2),
                          substr($eventinfo['eventDate'],8,2)-7, // 7 day cutoff
                          substr($eventinfo['eventDate'],0,4)));
		
			$regBegin = date('Y-m-d', mktime(0,0,0,
                          substr($eventinfo['eventDate'], 5, 2),
                          substr($eventinfo['eventDate'],8,2)-60, // 2mo entry start
                          substr($eventinfo['eventDate'],0,4)));
						  
			$regLateCutoff = date('Y-m-d', mktime(0,0,0,05,14,2008));
		}

		

    echo "<tr>\n";
    echo "<td>".$eventinfo['eventType']."</td>";
    echo "<td>".$eventinfo['eventName']."</td>";
    echo "<td>".$eventinfo['eventLocation']."</td>";
    echo "<td>".$eventinfo['eventDate']."</td>";
    echo "<td>";

    $tempquery = $eventinfo['eventDB'];
    $tempEventcheck = mysql_query("SELECT * FROM $tempquery") or die(mysql_error());
    $reg_count = 0;
    $userRegistered = 0;
    while ($tempEventInfo = mysql_fetch_assoc( $tempEventcheck ))
    {
      $reg_count = $reg_count + 1;
      if ($tempEventInfo['registeredUser'] == $sqlArray['username'])
      {
        $userRegistered = 1;
      }
    }
    if ($reg_count > 0)
    {
      if ($today <= $eventinfo['eventDate'])
      {
        echo "<form action=\"eventregistered.php\" method=\"POST\">\n";
        echo "[ ".$reg_count." ] ";
        echo "<input type=\"hidden\" name=\"regEventName\" value=\"".$eventinfo['eventDB']."\" />";
        echo " <input type=\"submit\" name=\"showRegistration\" value=\"Show\" />\n";
        if ($sqlArray['admin'] != 0)
        {
          echo "<input type=\"hidden\" name=\"regEventKey\" value=\"".$eventinfo['eventID']."\" />\n";
//          echo "<br /><input type=\"submit\" name=\"downloadMember\" value=\"Download Member File\" />";
//          echo "<br /><input type=\"submit\" name=\"downloadEntries\" value=\"Download Entries File\" />";
//          echo "<br /><input type=\"submit\" name=\"downloadPrintable\" value=\"Download Printable File\" />";
          echo "<br /><input type=\"submit\" name=\"downloadAllFiles\" value=\"Download All Files\" />\n";
        }
        echo "</form>\n";
      }
      else
      {
        echo "-";
      }
    }
    else
    {
      echo "None";
    }
    echo "</td>\n";
    echo "<td>\n";
    echo "<table class=\"blank\"><tr><td>\n";
    if (!isUserInfoComplete($sqlArray))
    {
      echo "User Info Incomplete\n";
      echo "<form action=\"events.php\" method=\"POST\">\n";
    }
    else if ($userRegistered && ($today < $regCutoff))
    {
      $paymentStatus = displayPaypalEvent($sqlArray, $eventinfo);
      echo "<form action=\"events.php\" method=\"POST\">\n";
    }
    elseif ($reg_count < 50)
    {
      echo "<form action=\"events.php\" method=\"POST\">\n";
      if ($today < $regBegin)
      {
        echo "Not Open Yet \n";
      }
      else if ($today < $regCutoff)
      {
        if ($eventinfo['eventType'] != "Hillclimb" && 
            $eventinfo['eventName'] != "4th Annual North Country Rumble" &&
            $allAutoXEvents != 0x1)
        {
          echo "<input type=\"image\" src=\"images/classy-icons-set/png/32x32/folder_add.png\" name=\"register\" value=\"Register\" alt=\"Register\" title=\"Register For This Event\"onClick=\"alert('After registering for this event online you will still need to check-in with registration at the event.')\" />\n";
        }
        if($allAutoXEvents == 0x1)
        {
	      echo "Not Available \n";
        }
      }
      else
      {
        echo "Closed \n";
      }
    }
    else
    {
      echo "Full \n";
    }
    echo "</td><td>\n";
    echo "<input type=\"hidden\" name=\"regEventName\" value=\"".$eventinfo['eventDB']."\">\n";

    if ($userRegistered && ($today >= $regBegin && $today < $regCutoff))
    {
      echo "<input type=\"image\" src=\"images/classy-icons-set/png/32x32/folder_edit.png\" name=\"editregistration\" value=\"EditRegistration\" alt=\"Edit Registration\" title=\"Edit Registration\" />\n";
    }

    if ($userRegistered && ($paymentStatus == "" ||
		  	($paymentStatus != "Completed" &&
	 	     $paymentStatus != "Pending" &&
		     $paymentStatus != "Processed" &&
  			 $paymentStatus != "In-Progress")))
    {
      // User is registered, but payment hasn't been completed. Allow them to unregister.
      if (($today >= $regBegin) && ($today < $regCutoff))
      {
        echo "<input type=\"image\" src=\"images/classy-icons-set/png/32x32/folder_remove.png\" name=\"unregister\" value=\"Unregister\" alt=\"Unregister\" title=\"Unregister\" />\n";
      }
    }
    else if ($eventinfo['eventType'] == "Autocross")
    {
      $allAutoXEvents |= 0x2;
    }

    echo "</td></tr></table>\n";
		
    if ($eventinfo['eventType'] == "Hillclimb" &&
        $today < $regCutoff && $today >= $regBegin)
    {
	  if (isUserAdmin())
	  {
      $itemName = $club_Abbr." ".$eventinfo['eventType']." ".$eventinfo['eventName']." ".$eventinfo['eventDate'];
      $hashinput = $itemName . $sqlArray['username'];
      $itemNumber = hash('md5', $hashinput);
      echo "<a href=\"#\" onclick=\"parent.main_openPopupWindow('entryHillclimb.php?".$itemNumber."')\">Register (testing)</a>\n";
      }
//      echo "<a target=\"_blank\" href=\"http://www.hillclimb.org/events/ascutney/ascutney.htm\">Register</a>\n";
      echo "<a target=\"_blank\" href=\"http://www.sccnh.org/hillclimbreg.html\">Register at sccnh.org</a>\n";
    }
    else if ($eventinfo['eventName'] == "4th Annual North Country Rumble")
    {
      echo "Rumble registration not fully completed<br>\n";
      echo "Use <a target=\"_blank\" href=\"http://sccnh.xhub.com/SCCNH%20Rumble%20Registration%20Form%202008.pdf\">Official Entry Form [pdf]</a>\n";
      echo "for now.\n";
    }

    echo "</td></form>\n";
    echo "</tr>\n";
  }
  echo "</table>\n";
}

function displayAutoXInfo()
{
      // Display Info
      echo "<table class=\"default\" width=\"100%\">\n";
      echo "<tr><th>AutoX Info:</th></tr>\n";
      echo "<tr><td>\n";
      echo file_get_contents("html/autoxinfo.html");
      echo "</td></tr>\n";
      echo "</table>\n";
}

function displayHillclimbInfo()
{
      // Display Info
      echo "<table class=\"default\" width=\"100%\">\n";
      echo "<tr><th>Hillclimb Info:</th></tr>\n";
      echo "<tr><td>\n";
      echo file_get_contents("html/hillclimbinfo.html");
      echo "</td></tr>\n";
      echo "</table>\n";
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

  $query = "SELECT * FROM users WHERE sha256_user = '".getCookie('ID')."'";
  $check = mysql_query($query)or die(mysql_error());
  $info = mysql_fetch_array($check);

  echoFrameHeader();

//  displayHillclimbInfo();
//  displayAutoXInfo();

  echo "<br /><br />";

  displayEvents($info);

  echoFrameFooter();

  die(); // attempt to guard against any code insertion at the end of the file
}

?>
