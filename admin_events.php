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

function displayAdminEventsPage()
{
    //otherwise they are shown the admin area
    if (!isUserAdmin())
    {
      header("Location: logout.php");
	}

    $databaseName = getDatabaseName();

    if (isset($_POST['createEvent']))
    {
        $newEventTableName = "event".date('U');
        $newEventName = $_POST['newEventName'];
        $newEventLocation = $_POST['newEventLocation'];
        $newEventDate = $_POST['newEventDate'];
        $newEventType = $_POST['newEventType'];

        $createQuery = "CREATE TABLE `".$databaseName."`.`".$newEventTableName."` (".
                       "`registeredUser` VARCHAR( 60 ) NOT NULL ,".
                       "`vehicleKey` VARCHAR( 60 ) NOT NULL ,".
                       "`vehicleClass` VARCHAR( 60 ) NOT NULL ,".
                       "`vehicleNumber` VARCHAR( 60 ) NOT NULL ,".
                       "`paid` TINYINT( 1 ) NOT NULL DEFAULT '0'".
                       ") ENGINE = MYISAM";

        if (mysql_query($createQuery))
        {
          $insertQuery = "INSERT INTO `".$databaseName."`.`events` (`eventID`, `eventDB`, `eventName`, `eventLocation`, `eventDate`, `eventType`)".
                         "VALUES (NULL , '".$newEventTableName."', '".$newEventName."', '".$newEventLocation."', '".$newEventDate."', '".$newEventType."')";
          if (!mysql_query($insertQuery))
          {
            die("Created new table but unable to insert event into database. Please contact administrator.");
          }
        }
        else
        {
          die("Unable to create new table for event. Please contact administrator.");
        }
        header("Location: admin_events.php");
      }
      elseif (isset($_POST['deleteEvent']))
      {
        $eventDB = $_POST['eventDB'];
        $eventDBkey = $_POST['eventDBkey'];
        // Delete Event from DB
        $deleteQuery = "DELETE FROM events WHERE `events`.`eventID` = ".$eventDBkey." LIMIT 1";
        // Drop Table
        $dropQuery = "DROP TABLE `".$eventDB."`";
//        die($deleteQuery."\n".$dropQuery);

        if ($queryCheck1 = mysql_query($deleteQuery))
        {
          if ($queryCheck2 = mysql_query($dropQuery))
          {
            header("Location: admin_events.php");
          }
          die("Deleted event from list. Unable to delete Table.");
        }

        die("Unable to delete event.");
      }
      elseif (isset($_POST['emailUsers']))
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

      echo "<script type=\"text/javascript\">\n";
      echo "function confirmDelete() {\n";
      echo "var r=confirm(\"Are you sure you want to this event? This cannot be undone.\");\n";
      echo "return r;";
      echo "}\n";
      echo "</script>\n";

      echo "<form action=\"admin_events.php\" method=\"POST\">\n";
      echo "<table class=\"default\">\n";
      echo "<th colspan=\"5\">Create New Event:</th>\n";
      echo "<tr><td>Event Name</td><td>Event Location</td><td>EventDate<br>YYYY-MM-DD</td><td>Event Type</td></tr>\n";
      echo "<tr><td><input type=\"text\" name=\"newEventName\"></td>\n";
      echo "<td><input type=\"text\" name=\"newEventLocation\"></td>\n";
      echo "<td><input type=\"text\" name=\"newEventDate\"></td>\n";
      echo "<td><select name=\"newEventType\">\n";
      echo "<option value=\"Autocross\">Autocross</option>\n";
      echo "<option value=\"Hillclimb\">Hillclimb</option>\n";
      echo "</select></td>\n";
      echo "<td><input type=\"submit\" name=\"createEvent\" value=\"Create Event\"></td></tr>\n";
      echo "</table>\n";
      echo "</form>\n";
      echo "<br>\n";

      // Display events
      $eventcheck = mysql_query("SELECT * FROM events ORDER BY `events`.`eventDate`, `events`.`eventDB` ASC") or die(mysql_error());
      echo "<table class=\"default\">\n";
      echo "<th colspan=\"4\">Delete Events</th>\n";
      echo "<tr><td>Event Type</td><td>Event Name</td><td>Event Location</td><td>Event Date</td></tr>\n";
      while ($eventinfo = mysql_fetch_assoc( $eventcheck ))
      {
        echo "<tr>";
        echo "<td>".$eventinfo['eventType']."</td>";
        echo "<td>".$eventinfo['eventName']."</td>";
        echo "<td>".$eventinfo['eventLocation']."</td>";
        echo "<td>".$eventinfo['eventDate']."</td>";
        echo "<td>";

        $tempquery = $eventinfo['eventDB'];

        $tempEventcheck = mysql_query("SELECT * FROM $tempquery") or die(mysql_error());
        $reg_count = mysql_num_rows($tempEventcheck);

        echo "<form action=\"admin_events.php\" method=\"POST\" onsubmit=\"return confirmDelete()\">\n";
        echo "<input type=\"hidden\" name=\"eventDB\" value=\"".$eventinfo['eventDB']."\">\n";
        echo "<input type=\"hidden\" name=\"eventDBkey\" value=\"".$eventinfo['eventID']."\">\n";
        echo "<input type=\"submit\" name=\"deleteEvent\" value=\"Delete\">\n";
        echo "</form>\n";
        if ($reg_count != 0)
        {
          echo "Users Registered.";
        }
        echo "</td></tr>\n";
     }
     echo "</table>\n";
}

//////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////
////////////BEGIN SCRIPT EXECUTION BELOW//////////////////////
//////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////

validateSession();
displayAdminEventsPage();
die();
?>