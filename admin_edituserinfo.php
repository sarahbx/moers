<?php
// admin_edituserinfo.php
/************************************************************************************
    Copyright © 2008-2010 xhub.com

    Sarah Bennert
    sarah@xhub.com

    This file is part of the Motorsports Online Event Registration System (moers).

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
    must be removed from this file before distribution.

************************************************************************************/

require_once 'include/functions.php';

function displayUserInfoForm($info)
{
  echo "<script type=\"text/javascript\">\n";
  echo "function confirmDelete() {\n";
  echo "var r=confirm(\"Are you sure you want to delete user: [".$info['username']."]? This cannot be undone.\");\n";
  echo "return r;";
  echo "}\n";
  echo "</script>\n";

  echo "<form name=\"formEdit\" action=\"admin_edituserinfo.php?USER=".$info['username']."\" method=\"POST\">";
  echo "<font size=\"5\">Edit ".$info['username']."'s Info:";
  echo "</font><br>";  
  echo "<table border=\"0\">";

  echo "<tr><td>First Name:</td><td><input name=\"firstName\" value=\"".$info['fname']."\">*</td>";
  echo "<td>Home Phone:</td><td><input name=\"homePhone\" value=\"".$info['hphone']."\">*</td></tr>";

  echo "<tr><td>Last Name:</td><td><input name=\"lastName\" value=\"".$info['lname']."\">*</td>";
  echo "<td>Cell Phone:</td><td><input name=\"cellPhone\" value=\"".$info['cphone']."\"></td></tr>";

  echo "<tr><td>Address 1:</td><td><input name=\"address1\" value=\"".$info['addr1']."\">*</td>";
  echo "<td>Email:</td><td><input name=\"email\" value=\"".$info['email']."\">*</td></tr>";

  echo "<tr><td>Address 2:</td><td><input name=\"address2\" value=\"".$info['addr2']."\"></td>";
  echo "<td>Emergency Contact:</td><td><input name=\"eContact\" value=\"".$info['econtact']."\">*</td></tr>";

  echo "<tr><td>City:</td><td><input name=\"city\" value=\"".$info['city']."\">*</td>";
  echo "<td>E-Contact Phone:</td><td><input name=\"eContactPhone\" value=\"".$info['econtact_phone']."\">*</td></tr>";

  echo "<tr><td>State:</td><td><input name=\"state\" value=\"".$info['state']."\">*</td>";
  echo "<td>E-Contact Relationship:</td><td><input name=\"eContactRel\" value=\"".$info['econtact_rel']."\">*</td></tr>";

  echo "<tr><td>Zip Code:</td><td><input name=\"zipCode\" value=\"".$info['zip']."\">*</td>\n";

  echo "<td>Member of:</td>\n";
  echo "<td><select name=\"club\">\n";

  echo "<option value=\"None\"";
  if ($info['club'] == "" || $info['club'] == "None")
    echo "SELECTED";
  echo ">None</option>\n";

  echo "<option value=\"SCCNH\"";
  if ($info['club'] == "SCCNH")
    echo "SELECTED";
  echo ">SCCNH</option>\n";

  echo "<option value=\"SCCV\"";
  if ($info['club'] == "SCCV")
    echo "SELECTED";
  echo ">SCCV</option>\n";

  echo "<option value=\"CMC\"";
  if ($info['club'] == "CMC")
    echo "SELECTED";
  echo ">CMC</option>\n";

  echo "<option value=\"CART\"";
  if ($info['club'] == "CART")
    echo "SELECTED";
  echo ">CART</option>\n";

  echo "<option value=\"KSCC\"";
  if ($info['club'] == "KSCC")
    echo "SELECTED";
  echo ">KSCC</option>\n";

  echo "</select> ***\n";
  echo "</tr>\n";

  echo "</table><br />\n";

  echo "*** Members of the listed partner clubs are allowed the same discount on entry fees that SCCNH members have.<br />\n";
  echo "*** However, you must show proof of membership at the event or pay the full non-member fee.<br /><br />\n";

  echo "<input type=\"button\" name=\"cancel\" value=\"Cancel\" onClick=\"parent.main_popupWindowCancel()\">";
  echo "<input type=\"submit\" name=\"submitEdit\" value=\"Save\">";
  echo "</form><br>";

  echo "<form name=\"formDelete\" action=\"admin_edituserinfo.php?USER=".$info['username']."\" method=\"POST\" onsubmit=\"return confirmDelete()\">";
  echo "<input type=\"submit\" name=\"submitDelete\" value=\"Delete\" />\n";
  echo "</form><br>\n";
  echo "<b>Note: All fields marked with '*' must be completed to register for events.</b><br>\n";
  echo "<b>Any information deliberately entered wrong invalidates your pre-registration at the event.</b><br>\n";
}


function adminDisplayUserInfoPage()
{
  $hashUsername = getCookie('ID');

  $check = mysql_query("SELECT * FROM users WHERE sha256_user = '$hashUsername'")or die(mysql_error());
  $info = mysql_fetch_array( $check );

  if ($info['admin'] != 1)
    die("ERROR: You are not an admin.");

  $username = $_GET['USER'];

  $check = mysql_query("SELECT * FROM users WHERE username = '$username'")or die(mysql_error());
  while($info = mysql_fetch_array( $check ))
  {
    if ($info['admin'] == 1)
    {
      die("ERROR: Not allowed to edit admin info");
    }

    if (isset($_POST['submitEdit']))
    {

      $storedMemberType = $info['member'];

      $postFname = addslashes($_POST['firstName']);
      $postLname = addslashes($_POST['lastName']);
      $postAddr1 = addslashes($_POST['address1']);
      $postAddr2 = addslashes($_POST['address2']);
      $postCity = addslashes($_POST['city']);
      $postState = addslashes($_POST['state']);
      $postZip = addslashes($_POST['zipCode']);
      $postHphone = addslashes($_POST['homePhone']);
      $postCphone = addslashes($_POST['cellPhone']);
      $postEmail = addslashes($_POST['email']);
      $postEcontact = addslashes($_POST['eContact']);
      $postEcPhone = addslashes($_POST['eContactPhone']);
      $postEcRel = addslashes($_POST['eContactRel']);
      $postClub = addslashes($_POST['club']);


      if ($storedMemberType == 0 || $storedMemberType == 2 || $storedMemberType == 3)
      {
        if ($postClub == "SCCNH")
          $postMemberType = 2; // SCCNH member (registered off-line)
        else if ($postClub == "None")
          $postMemberType = 0; // Not a member
        else
          $postMemberType = 3; // Partner-member
      }
      else if ($storedMemberType == 1) // they paid online. make sure we don't change that.
      {
        $postClub = "SCCNH";
        $postMemberType = 1; // SCCNH member (registered on-line)
      }

      // now we insert it into the database
      $update = "UPDATE users SET 
                        fname='$postFname', 
                        lname='$postLname', 
                        addr1='$postAddr1', 
                        addr2='$postAddr2', 
                        city='$postCity', 
                        state='$postState', 
                        zip='$postZip', 
                        hphone='$postHphone',
                        cphone='$postCphone',
                        email='$postEmail',
                        econtact='$postEcontact',
                        econtact_phone='$postEcPhone',
                        econtact_rel='$postEcRel',
                        member='$postMemberType',
                        club='$postClub'
                 WHERE username='$username'";
      mysql_query($update);

     $check2 = mysql_query("SELECT * FROM users WHERE username = '$username'")or die(mysql_error());
     $info2 = mysql_fetch_array( $check2 );
     if ($info2 && !isUserInfoComplete($info2))
     {
       mysql_close();
       die("Required user info not complete. Please <a href=\"userinfo.php\">go back</a> to continue.</html>");
     }
     else // update complete. close window
     {
        mysql_close();
        //  echo "Saved?".$update."!";

        // below lines must be html commented when working outside of php system or it will be interpreted and executed
        // reload the admin/user screen.
        echo "<script type=\"text/javascript\">parent.main_setBodyFrame('admin_users.php');\n";
        // return to the user screen.
        echo "parent.main_popupWindowCancel();</script></body></html>";
     }
    }
    else if (isset($_POST['submitDelete']))
    {
      // check for vehicles first...
      $vehcheck = mysql_query("SELECT * FROM vehicles WHERE userOwner = '$username'")or die(mysql_error());
      while ($vehinfo = mysql_fetch_assoc( $vehcheck ))
      {
        $qVehID = $vehinfo['vehicleID'];
        // first delete the owners vehicles from any events
        deleteVehicleFromEvents($qVehID);
        // then delete the vehicle.
        mysql_query("DELETE FROM vehicles WHERE vehicleID = '$qVehID'");
      }
      // now delete the user
      mysql_query("DELETE FROM users WHERE username = '$username'");
      mysql_close();

      // below lines must be html commented when working outside of php system or it will be interpreted and executed
      // reload the admin/user screen.
      echo "<script type=\"text/javascript\">parent.main_setBodyFrame('admin_users.php');\n";
      // return to the user screen.
      echo "parent.main_popupWindowCancel();</script></body></html>";
    }
    else
    {
      displayUserInfoForm($info);
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
  echoFrameHeader();
  adminDisplayUserInfoPage();
  echoFrameFooter();
  die(); // attempt to guard against any code insertion at the end of the file
}
?>