<?php
// members.php
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

function displayVehicles()
{
  require 'include/configGlobals.php';
  echo "<script type=\"text/javascript\">\n";
  echo "parent.main_disablePopupBackButton();\n";
  echo "</script>\n";

  $query = "SELECT username FROM users WHERE sha256_user = '".getCookie('ID')."'";
  $check = mysql_query($query)or die(mysql_error());
  $info = mysql_fetch_array($check);
  
  $username = $info['username'];

  // Display vehicles
  $vehcheck = mysql_query("SELECT * FROM vehicles WHERE userOwner = '$username'")or die(mysql_error());

  echo "<table class=\"default\" width=\"100%\">\n";
  echo "<th colspan=\"9\">Your Vehicles</th>\n";
  echo "<tr>\n";
  echo "<td>";
  echo "<form action=\"vehicle.php\" method=\"POST\">\n";
  echo "<input type=\"hidden\" name=\"vehID\" value=\"\">\n";
  echo "<input type=\"image\" src=\"images/classy-icons-set/png/32x32/process_add.png\" name=\"addVehicle\" value=\"Add\" alt=\"Add Vehicle\" title=\"Add Vehicle\"> Add\n";
  echo "</form>\n";
  echo "</td>\n";
  echo "<td>Car #</td><td>Year</td><td>Make</td><td>Model</td><td>Color</td><td>Treadware</td>";
  echo "<td>SCCA Class</td><td>".$club_Abbr." Class</td>\n";
  echo "</tr>\n";

  while ($vehinfo = mysql_fetch_assoc( $vehcheck ))
  {
    echo "<tr>";
    echo "<td><form action=\"vehicle.php\" method=\"POST\">";
    echo "<input type=\"hidden\" name=\"vehID\" value=\"".$vehinfo['vehicleID']."\">\n";
    echo "<input type=\"image\" src=\"images/classy-icons-set/png/32x32/process_info.png\" name=\"submit\" value=\"Edit\" alt=\"Edit Vehicle\" title=\"Edit Vehicle\"> Edit\n";
    echo "</td></form>";
    echo "<td>".$vehinfo['number']."</td>";
    echo "<td>".$vehinfo['year']."</td>";
    echo "<td>".$vehinfo['make']."</td>";
    echo "<td>".$vehinfo['model']."</td>";
    echo "<td>".$vehinfo['color']."</td>";
    echo "<td>".$vehinfo['treadware']."</td>";
    echo "<td>".$vehinfo['scca_class']."</td>";
    echo "<td>".$vehinfo['sccnh_class']."</td>";
    echo "</tr>";
  }
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

  echoFrameHeader();
  displayVehicles();
  echoFrameFooter();

  die(); // attempt to guard against any code insertion at the end of the file
}
?>