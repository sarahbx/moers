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

require_once 'include/config.php';
require_once 'include/functions.php';

// Connects to your Database
connectDatabase();
slashAllInputs();

//checks cookies to make sure they are logged in
if(getCookie('ID'))
{
  $hashUsername = getCookie('ID');
  $sessionId = getCookie('Session_ID');

  $check = mysql_query("SELECT * FROM users WHERE sha256_user = '$hashUsername'")or die(mysql_error());
  while($info = mysql_fetch_array( $check ))
  {
    $username = $info['username'];
    //if the cookie has the wrong sessionId, they are taken to the login page
    if ($sessionId != $info['session_id'])
    { header("Location: logout.php");
    }

    //otherwise they are shown the admin area
    elseif ($info['admin'])
    {
      changeCookie(); // keep the session id changing

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



      echo "<table class=\"default\">\n";
      echo "<tr><td>Username</td>\n";
      echo "<td>Car Number</td><td>Year</td><td>Make</td><td>Model</td><td>color</td><td>Treadwear</td>\n";
      echo "<td>SCCA Class</td><td>SCCNH Class</td><td>NEHA Class</td><td>Hillclimb Class</td></tr>\n";

//      $userCheck = mysql_query("SELECT * FROM users WHERE username != '$username'")or die(mysql_error());
      $userCheck = mysql_query("SELECT * FROM users")or die(mysql_error());
      while($userInfo = mysql_fetch_array( $userCheck ))
      {

        $tempUser = $userInfo['username'];
        $vehCheck = mysql_query("SELECT * FROM vehicles WHERE userOwner = '$tempUser'") or die(mysql_error());
        while($vehInfo = mysql_fetch_array( $vehCheck ))
        {
          echo "<tr><td>".$userInfo['username']."</td>\n";
          echo "<td>".$vehInfo['number']."</td>\n";
          echo "<td>".$vehInfo['year']."</td>\n";
          echo "<td>".$vehInfo['make']."</td>\n";
          echo "<td>".$vehInfo['model']."</td>\n";
          echo "<td>".$vehInfo['color']."</td>\n";
          echo "<td>".$vehInfo['treadware']."</td>\n";
          echo "<td>".$vehInfo['scca_class']."</td>\n";
          echo "<td>".$vehInfo['sccnh_class']."</td>\n";
          echo "<td>".$vehInfo['neha_class']."</td>\n";
	  echo "<td>".$vehInfo['hillclimb_class']."</td>\n";
          echo "</tr>\n";

          $vehID = $vehInfo['vehicleID'];
/*
          $eventCheck = mysql_query("SELECT * FROM events") or die(mysql_error());
          while($eventInfo = mysql_fetch_array( $eventCheck ))
          {
            $eventDB = $eventInfo['eventDB'];
            $tempEventCheck = mysql_query("SELECT * FROM $eventDB WHERE registeredUser = '$tempUser'") or die(mysql_error());
            while ($tempEventInfo = mysql_fetch_array( $tempEventCheck))
            {
              if ($tempEventInfo['vehicleKey'] == $vehID)
              {
                echo "<tr><td>Registered Event: ".$eventInfo['eventName']."-".$eventInfo['eventLocation']."-".$eventInfo['eventDate']."</td></tr>\n";
              }
            }
          }
*/
        }
        
      }
        echo "</table>\n";
        echo "<br /><br />\n";
    echo file_get_contents("html/footer.html");
    }
  }
}
else
//if the cookie does not exist, they are taken to the login screen
{
  header("Location: logout.php");
}
?>