<?php
/************************************************************************************
    Copyright � 2008-2010 xhub.com

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

function displayVehiclePage()
{
    echo "<script type=\"text/javascript\">\n";
    echo "parent.main_enablePopupBackButton('vehicles.php');\n";
    echo "</script>\n";

    $_POST['vehID'] = $_POST['vehID'];

    $hashUsername = getCookie('ID');
    $sessionID = getCookie('Session_ID');

    $check = mysql_query("SELECT * FROM users WHERE sha256_user = '$hashUsername'")or die(mysql_error());
    while($info = mysql_fetch_array( $check ))
    {
        $sccnhClassArray = array("Stock", "Sticky Stock", "Street Prepared", "Prepared", "Race");
        $sccaClassArray = array("Unknown", 
                                "SS", "AS", "BS", "CS", "DS", "ES", "FS", "GS", "HS",
                                "ASP", "BSP", "CSP", "DSP", "ESP", "FSP",
                                "ST", "STS", "STR", "STX", "STU",
                                "XP", "BP", "CP", "DP", "EP", "FP", "GP",
                                "SM", "SMF", "SSM",
								"AM", "BM", "CM", "DM", "EM", "FM",
                                "FSAE", "F125", "FJA", "FJB", "FJC");

		$hillclimbClassArray = array("Regular", "Rally", "Drift");

		$nehaClassArray = array("Unknown", "FL", "F2", "P1", "P2", "P3", "P4", "U1", "U2", "U3", "U4", "SP1", "SP2", "SP3", "SP4");


        if(!isset($_POST['addVehicle']) && !isset($_POST['addVehicle_x']))
        {
          $vehicleID = $_POST['vehID'];
          $vehcheck = mysql_query("SELECT * FROM vehicles WHERE vehicleID = '$vehicleID'")or die(mysql_error());
        }

        echo "<form name=\"theForm\" action=\"vehicle.php\" method=\"POST\">";
        echo "<font size=\"5\">Add/Edit vehicle:</font><br>";

        echo "<table border=\"0\">\n";
        echo "<tr><td>\n";

        echo "<table class='default'>";
        if(!isset($_POST['addVehicle']) && !isset($_POST['addVehicle_x']))
        {
          while ($vehinfo = mysql_fetch_array( $vehcheck ))  
          {
            echo "<tr><td>Year:</td>\n";
            echo "<td>\n";
            echo "<select name=\"year\">\n";
            for ($i=1893; $i<=((int)date('Y')+1); $i+=1)
            {
              if ($vehinfo['year'] == (string)$i)
                echo "<option value=\"".$i."\" SELECTED>".$i."</option>\n";
              else
                echo "<option value=\"".$i."\">".$i."</option>\n";
            }
            echo "</select>\n";
            echo "</td></tr>";

            echo "<tr><td>Make:</td><td><input name=\"make\" value=\"".$vehinfo['make']."\"></td></tr>";
            echo "<tr><td>Model:</td><td><input name=\"model\" value=\"".$vehinfo['model']."\"></td></tr>";
            echo "<tr><td>Color:</td><td><input name=\"color\" value=\"".$vehinfo['color']."\"></td></tr>";

            echo "<tr><td>Treadwear: <a href=\"http://en.wikipedia.org/wiki/Treadwear_rating\" target=\"_blank\">[wiki]</a></td>\n";
            echo "<td>\n";
            echo "<select name=\"treadware\">\n";
            if ($vehinfo['treadware'] == "Unknown")
              echo "<option value=\"Unknown\" SELECTED>Unknown</option>\n";
            else
              echo "<option value=\"Unknown\">Unknown</option>\n";
            for ($i=0; $i<1000; $i+=10)
            {
              if ($vehinfo['treadware'] == (string)$i)
                echo "<option value=\"".$i."\" SELECTED>".$i."</option>\n";
              else
                echo "<option value=\"".$i."\">".$i."</option>\n";
            }
            echo "</select>\n";
            echo "</td></tr>";

            echo "<tr><td>Requested Number:</td><td><input name=\"number\" value=\"".$vehinfo['number']."\" maxlength=\"3\"></td></tr>";

            ///////////////////////////////////////////////////////////////////////////////
            // Build SCCA class list, select correct class stored. this part sucks
            echo "<tr><td>SCCA AutoX Class:</td><td>\n";

            echo "<select name=\"sccaClass\">\n";

            foreach ($sccaClassArray as $tempClass)
            {
              if ($vehinfo['scca_class'] == $tempClass)
                echo "<option value=\"".$tempClass."\" SELECTED>".$tempClass."</option>\n";
              else
                echo "<option value=\"".$tempClass."\">".$tempClass."</option>\n";
            }

            echo "</select><br>\n";
            echo "<a target=\"_blank\" href=\"http://scca.com/contentpage.aspx?content=61\">Rules [link]</a>\n";

            echo "</td></tr>";


            ///////////////////////////////////////////////////////////////////////////////
            // Build SCCNH class list, select correct class stored
            echo "<tr><td>SCCNH AutoX Class:</td><td>\n";
            echo "<select name=\"sccnhClass\">\n";

            foreach ($sccnhClassArray as $tempClass)
            {
              if ($vehinfo['sccnh_class'] == $tempClass)
                echo "<option value=\"".$tempClass."\" SELECTED>".$tempClass."</option>\n";
              else
                echo "<option value=\"".$tempClass."\">".$tempClass."</option>\n";
            }

            echo "</select>\n";
			echo "</td></tr>\n";
			
            ///////////////////////////////////////////////////////////////////////////////
			// Build SCCNH hillclimb class list, select correct class stored
			echo "<tr><td>SCCNH Hillclimb Class:</td><td>\n";
			echo "<select name=\"hillclimbClass\">\n";
			
			foreach ($hillclimbClassArray as $tempClass)
			{
			  if ($vehinfo['hillclimb_class'] == $tempClass)
			    echo "<option value=\"".$tempClass."\" SELECTED>".$tempClass."</option>\n";
			  else
			    echo "<option value=\"".$tempClass."\">".$tempClass."</option>\n";
			}
			
          echo "</select><br>\n";
//		  echo "Regular/Drift: $140<br> Rally: $160\n";
          echo "</td></tr>\n";

            ///////////////////////////////////////////////////////////////////////////////
			// Build NEHA class list, select correct class stored
			echo "<tr><td>NEHA Hillclimb Class:</td><td>\n";
			echo "<select name=\"nehaClass\">\n";
			
			foreach ($nehaClassArray as $tempClass)
			{
			  if ($vehinfo['neha_class'] == $tempClass)
			    echo "<option value=\"".$tempClass."\" SELECTED>".$tempClass."</option>\n";
			  else
			    echo "<option value=\"".$tempClass."\">".$tempClass."</option>\n";
			}
			
			echo "</select><br>\n";
			echo "<a target=\"_blank\" href=\"http://hillclimb.org/rules_regs/rules_regs.htm\">Rules [link]</a>\n";
			
          }
          echo "</td></tr>\n";

          echo "</table><br>\n";
          echo "<input type=\"hidden\" name=\"vehicleID\" value=\"".$_POST['vehID']."\">";
        }
        else
        {
            echo "<tr><td>Year:</td>\n";
            echo "<td>\n";
            echo "<select name=\"year\">\n";
            echo "<option value=\"\"></option>\n";
            for ($i=((int)date('Y'))+1; $i >= 1893; $i-=1)
            {
                echo "<option value=\"".$i."\">".$i."</option>\n";
            }
            echo "</select>\n";
            echo "</td></tr>";

          echo "<tr><td>Make:</td><td><input name=\"make\" value=\"\"></td></tr>";
          echo "<tr><td>Model:</td><td><input name=\"model\" value=\"\"></td></tr>";
          echo "<tr><td>Color:</td><td><input name=\"color\" value=\"\"></td></tr>";
          echo "<tr><td>Treadwear: <a href=\"http://en.wikipedia.org/wiki/Treadwear_rating\" target=\"_blank\">[wiki]</a></td>\n";

          echo "<td>\n";
          echo "<select name=\"treadware\">\n";
          echo "<option value=\"Unknown\">Unknown</option>\n";
          for ($i=0; $i<1000; $i+=10)
          {
            echo "<option value=\"".$i."\">".$i."</option>\n";
          }
          echo "</select>\n";
          echo "</td></tr>";

          echo "<tr><td>Requested Number:</td><td><input name=\"number\" value=\"\" maxlength=\"3\"></td></tr>";


          // SCCA Class list
          echo "<tr><td>SCCA AutoX Class:</td><td>\n";

          echo "<select name=\"sccaClass\">\n";

          foreach ($sccaClassArray as $tempClass)
          {
            if ($tempClass == "Unknown")
              echo "<option value=\"".$tempClass."\" SELECTED>".$tempClass."</option>\n";
            else
              echo "<option value=\"".$tempClass."\">".$tempClass."</option>\n";
          }

          echo "</select><br>\n";
          echo "<a target=\"_blank\" href=\"http://scca.com/contentpage.aspx?content=61\">Rules [link]</a>\n";
          echo "</td></tr>";

          // SCCNH class list
          echo "<tr><td>SCCNH AutoX Class:</td><td>\n";
          echo "<select name=\"sccnhClass\">\n";

          foreach ($sccnhClassArray as $tempClass)
          {
            if ($tempClass == "Stock")
              echo "<option value=\"".$tempClass."\" SELECTED>".$tempClass."</option>\n";
            else
              echo "<option value=\"".$tempClass."\">".$tempClass."</option>\n";
          }

          echo "</select>\n";
          echo "</td></tr>\n";

          // SCCNH hillclimb class list
          echo "<tr><td>NEHA Hillclimb Class:</td><td>\n";
          echo "<select name=\"hillclimbClass\">\n";

          foreach ($hillclimbClassArray as $tempClass)
          {
            if ($tempClass == "Unknown")
              echo "<option value=\"".$tempClass."\" SELECTED>".$tempClass."</option>\n";
            else
              echo "<option value=\"".$tempClass."\">".$tempClass."</option>\n";
          }

          echo "</select><br>\n";
//		  echo "Regular/Drift: $140<br> Rally: $160\n";
          echo "</td></tr>\n";

          // NEHA class list
          echo "<tr><td>NEHA Hillclimb Class:</td><td>\n";
          echo "<select name=\"nehaClass\">\n";

          foreach ($nehaClassArray as $tempClass)
          {
            if ($tempClass == "Unknown")
              echo "<option value=\"".$tempClass."\" SELECTED>".$tempClass."</option>\n";
            else
              echo "<option value=\"".$tempClass."\">".$tempClass."</option>\n";
          }

          echo "</select><br>\n";
		  echo "<a target=\"_blank\" href=\"http://hillclimb.org/rules_regs/rules_regs.htm\">Rules [link]</a>\n";
          echo "</td></tr>\n";

          echo "</table><br>\n";
          echo "<input type=\"hidden\" name=\"vehicleID\" value=\"\">";
        }

        if(isset($_POST['addVehicle']) || isset($_POST['addVehicle_x']))
        {
          echo "<input type=\"submit\" name=\"submitAdd\" value=\"Add Vehicle\">";
        }
        else
        {
          if (!isVehicleRegistered($_POST['vehID']))
          {
            echo "<input type=\"submit\" name=\"submitEdit\" value=\"Save\">";
            echo "<input type=\"submit\" name=\"submitDelete\" value=\"Delete\">";
          }
          else
          {
            echo "<br><b><u>Vehicle Registered in one or more events, unable to edit or delete until after event.</u></b><br>\n";
          }
        }
        echo "</form><br><br>";

        echo "</td></tr></table><br>\n";
		echo file_get_contents("html/autoxClassTable.html");
	}
}

function addVehiclePOST()
{
    $hashUsername = getCookie('ID');
    $sessionID = getCookie('Session_ID');

    $check = mysql_query("SELECT * FROM users WHERE sha256_user = '$hashUsername'")or die(mysql_error());
    while($info = mysql_fetch_array( $check ))
    {
      $username = $info['username'];


        $postYear = $_POST['year'];
        $postMake = $_POST['make'];
        $postModel = $_POST['model'];
        $postColor = $_POST['color'];
        $postTreadware = $_POST['treadware'];
        $postNumber = $_POST['number'];
        $postSccaClass = $_POST['sccaClass'];
        $postSccnhClass = $_POST['sccnhClass'];
		$postNehaClass = $_POST['nehaClass'];
		$postHillclimbClass = $_POST['hillclimbClass'];
        $vehicleID = $_POST['vehicleID'];
        
	  echo "<script type=\"text/javascript\">\n";
	  echo "parent.main_enablePopupBackButtonHistory();\n";
	  echo "</script>\n";

        if (!is_numeric($postNumber))
        {
            die("Vehicle number entered is not a number. Please go back and try again.</body></html>");
        }

        if (!isNumberAvailable($username, $postNumber))
        {
            die("Vehicle number entered is already taken. Please go back and try again.</body></html>");
        }

        if ($postTreadware != "Unknown" && !is_numeric($postTreadware))
        {
            die("Vehicle treadwear entered is not valid. Please go back and try again.</body></html>");
        }

        if (!is_numeric($postYear))
        {
            die("Vehicle year entered is not valid. Please go back and try again.</body></html>");
        }

        // now we insert it into the database
        $update = "INSERT INTO vehicles (userOwner, number, year, make, model, color, treadware, scca_class, sccnh_class, neha_class, hillclimb_class) 
                       VALUES('$username', '$postNumber', '$postYear', '$postMake', '$postModel', '$postColor', '$postTreadware', '$postSccaClass', '$postSccnhClass', '$postNehaClass', '$postHillclimbClass')";
        if (!mysql_query($update))
        {
          die(mysql_error());
        }

        //  echo "Saved?".$update."!";
    }

  exitVehicleScript();
}

function editVehiclePOST()
{
  $hashUsername = getCookie('ID');

  $check = mysql_query("SELECT * FROM users WHERE sha256_user = '$hashUsername'")or die(mysql_error());
  while($info = mysql_fetch_array( $check ))
  {
    $username = $info['username'];

    $postYear = $_POST['year'];
    $postMake = $_POST['make'];
    $postModel = $_POST['model'];
    $postColor = $_POST['color'];
    $postTreadware = $_POST['treadware'];
    $postNumber = $_POST['number'];
    $postSccaClass = $_POST['sccaClass'];
    $postSccnhClass = $_POST['sccnhClass'];
    $postNehaClass = $_POST['nehaClass'];
    $postHillclimbClass = $_POST['hillclimbClass'];
    $vehicleID = $_POST['vehicleID'];

    echo "<script type=\"text/javascript\">\n";
    echo "parent.main_enablePopupBackButtonHistory();\n";
    echo "</script>\n";

    if (!is_numeric($postNumber))
    {
      die("Vehicle number entered is not a number. Please go back and try again.</body></html>");
    }

    if (!isNumberAvailable($username, $postNumber))
    {
      die("Vehicle number entered is already taken. Please go back and try again.</body></html>");
    }

    if ($postTreadware != "Unknown" && !is_numeric($postTreadware))
    {
      die("Vehicle treadwear entered is not a number. Please go back and try again.</body></html>");
    }

    if (!is_numeric($postYear))
    {
      die("Vehicle year entered is not valid. Please go back and try again.</body></html>");
    }

    // now we insert it into the database
    $update = "UPDATE vehicles SET number='$postNumber', year='$postYear', make='$postMake', model='$postModel', color='$postColor', treadware='$postTreadware', scca_class='$postSccaClass', sccnh_class='$postSccnhClass', neha_class='$postNehaClass', hillclimb_class='$postHillclimbClass' WHERE vehicleID='$vehicleID'";
    if (!mysql_query($update))
    {
      die(mysql_error());
    }
  }

  exitVehicleScript();
}

function deleteVehiclePOST()
{
  $vehicleID = $_POST['vehicleID'];
  deleteVehicleFromEvents($vehicleID);
  mysql_query("DELETE FROM vehicles WHERE vehicleID = '$vehicleID'");
  exitVehicleScript();
}

function exitVehicleScript()
{
  echo "<script type=\"text/javascript\">\n";

  if (doesUserHaveVehicles())
    echo "parent.main_enableRegisterButton(true);\n";
  else
    echo "parent.main_enableRegisterButton(false);\n";

  echo "window.location.replace(\"vehicles.php\");</script></body></html>";
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

  if (isset($_POST['submit']) || isset($_POST['submit_x']) ||
      isset($_POST['addVehicle']) || isset($_POST['addVehicle_x'])) 
  {
    displayVehiclePage();
    echoFrameFooter();
  }
  elseif (isset($_POST['submitAdd']) || isset($_POST['submitAdd_x'])) 
  {
    addVehiclePOST();
  }
  elseif (isset($_POST['submitEdit']) || isset($_POST['submitEdit_x']))
  {
    editVehiclePOST();
  }
  elseif (isset($_POST['submitDelete']) || isset($_POST['submitDelete_x'])) 
  {
    deleteVehiclePOST();
  }

  die(); // attempt to guard against any code insertion at the end of the file
}
?>