<?php
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

require_once 'include/config.php';
require_once 'include/functions.php';
require_once 'include/paypal.php';

function displayUserInfoForm($info)
{
  require 'include/configGlobals.php';
  echo "<script type=\"text/javascript\" src=\"js/userinfo.js\"></script>\n";

  // Join Club:
  if (isUserInfoComplete($info))
    displayPaypalMembership($info);
  else
    echo "*** Complete required user info to become a member and register for events. ***<br><br>";

  echo "<form action=\"userinfo.php\" method=\"POST\">";
  echo "<h3>Edit ".$info['username']."'s Info:</h3><br>";  
  echo "<table class=\"transparent\">";

  echo "<tr><td>First Name:</td><td><input name=\"firstName\" id=\"info0\" value=\"".$info['fname']."\" ";
  echo "onkeypress=\"inputChanged()\" onchange=\"inputChanged()\" onfocus=\"inputChanged()\" onclick=\"inputChanged()\">*</td>";
  echo "<td>Home Phone:</td><td><input name=\"homePhone\" id=\"info1\" value=\"".$info['hphone']."\" ";
  echo "onkeypress=\"inputChanged()\" onchange=\"inputChanged()\" onfocus=\"inputChanged()\" onclick=\"inputChanged()\">*</td></tr>";

  echo "<tr><td>Last Name:</td><td><input name=\"lastName\" id=\"info2\" value=\"".$info['lname']."\" ";
  echo "onkeypress=\"inputChanged()\" onchange=\"inputChanged()\" onfocus=\"inputChanged()\" onclick=\"inputChanged()\">*</td>";
  echo "<td>Cell Phone:</td><td><input name=\"cellPhone\" value=\"".$info['cphone']."\"></td></tr>";

  echo "<tr><td>Address 1:</td><td><input name=\"address1\" id=\"info3\" value=\"".$info['addr1']."\" ";
  echo "onkeypress=\"inputChanged()\" onchange=\"inputChanged()\" onfocus=\"inputChanged()\" onclick=\"inputChanged()\">*</td>";
  echo "<td>Email:</td><td><input name=\"email\" id=\"info4\" value=\"".$info['email']."\" ";
  echo "onkeypress=\"inputChanged()\" onchange=\"inputChanged()\" onfocus=\"inputChanged()\" onclick=\"inputChanged()\">*</td></tr>";

  echo "<tr><td>Address 2:</td><td><input name=\"address2\" value=\"".$info['addr2']."\"></td>";
  echo "<td>Emergency Contact:</td><td><input name=\"eContact\" id=\"info5\" value=\"".$info['econtact']."\" ";
  echo "onkeypress=\"inputChanged()\" onchange=\"inputChanged()\" onfocus=\"inputChanged()\" onclick=\"inputChanged()\">*</td></tr>";

  echo "<tr><td>City:</td><td><input name=\"city\" id=\"info6\" value=\"".$info['city']."\" ";
  echo "onkeypress=\"inputChanged()\" onchange=\"inputChanged()\" onfocus=\"inputChanged()\" onclick=\"inputChanged()\">*</td>";
  echo "<td>E-Contact Phone:</td><td><input name=\"eContactPhone\" id=\"info7\" value=\"".$info['econtact_phone']."\" ";
  echo "onkeypress=\"inputChanged()\" onchange=\"inputChanged()\" onfocus=\"inputChanged()\" onclick=\"inputChanged()\">*</td></tr>";

  echo "<tr><td>State:</td><td><input name=\"state\" id=\"info8\" value=\"".$info['state']."\" ";
  echo "onkeypress=\"inputChanged()\" onchange=\"inputChanged()\" onfocus=\"inputChanged()\" onclick=\"inputChanged()\">*</td>";
  echo "<td>E-Contact Relationship:</td><td><input name=\"eContactRel\" id=\"info9\" value=\"".$info['econtact_rel']."\" ";
  echo "onkeypress=\"inputChanged()\" onchange=\"inputChanged()\" onfocus=\"inputChanged()\" onclick=\"inputChanged()\">*</td></tr>";

  echo "<tr><td>Zip Code:</td><td><input name=\"zipCode\" id=\"info10\" value=\"".$info['zip']."\" ";
  echo "onkeypress=\"inputChanged()\" onchange=\"inputChanged()\" onfocus=\"inputChanged()\" onclick=\"inputChanged()\">*</td>\n";

  echo "<td>Member of:</td>\n";
  echo "<td><select name=\"club\">\n";

  echo "<option value=\"None\"";
  if ($info['club'] == "" || $info['club'] == "None")
    echo "SELECTED";
  echo ">None</option>\n";

  echo "<option value=\"".$club_Abbr."\"";
  if ($info['club'] == $club_Abbr)
    echo "SELECTED";
  echo ">".$club_Abbr."</option>\n";

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

  echo "*** Members of the listed partner clubs are allowed the same discount on entry fees that ".$club_Abbr." members have.<br />\n";
  echo "*** However, you must show proof of membership at the event or pay the full non-member fee.<br /><br />\n";

  echo "<input type=\"submit\" name=\"submitEdit\" id=\"submitButton\" value=\"Save\">";
  echo "</form><br>";
  echo "<b>Note: All fields marked with '*' must be completed to register for events.</b><br>\n";
  echo "<b>Any information deliberately entered wrong invalidates your pre-registration at the event.</b><br>\n";

}


function displayUserInfoPage()
{
  require 'include/configGlobals.php';
  $hashUsername = getCookie('ID');

  $check = mysql_query("SELECT * FROM users WHERE sha256_user = '$hashUsername'")or die(mysql_error());
  while($info = mysql_fetch_array( $check ))
  {
    $username = $info['username'];

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
        if ($postClub == $club_Abbr)
          $postMemberType = 2; // Club member (registered off-line)
        else if ($postClub == "None")
          $postMemberType = 0; // Not a member
        else
          $postMemberType = 3; // Partner-member
      }
      else if ($storedMemberType == 1) // they paid online. make sure we don't change that.
      {
        $postClub = $club_Abbr;
        $postMemberType = 1; // Club member (registered on-line)
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
       echo "<script type=\"text/javascript\">\n";
       echo "parent.main_enablePopupBackButtonHistory();\n";
       echo "</script>\n";
       die("Required user info not complete. Please go back to continue.</body></html>");
     }
     else // update complete. close window
     {
       ignore_user_abort(true);
       updateMemberStatus();
       echo "<html><body>\n";
       echo "<script language=\"javascript\" type=\"text/javascript\">\n";
       echo "parent.main_enableVehiclesButton(true)\n";
       if (doesUserHaveVehicles())
         echo "parent.main_enableRegisterButton(true);\n";
       else
         echo "parent.main_enableRegisterButton(false);\n";
       echo "parent.main_popupWindowCancel();\n";
       echo "</script></body></html>";
     }
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

  displayUserInfoPage();

  echoFrameFooter();

  die(); // attempt to guard against any code insertion at the end of the file
}
?>