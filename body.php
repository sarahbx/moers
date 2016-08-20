<?php
// body.php
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
require_once 'include/paypal.php';
require_once 'include/download.php';

function displayBodyPage()
{
//  header("X-Frame-Options: SAMEORIGIN"); 
  echo "<html>\n";
  echo "<head>\n";
  echo "<link rel=stylesheet href=\"style.css\" type=\"text/css\" media=screen>\n";
  echo "<script type=\"text/javascript\" src=\"js/functions.js\"></script>\n";
  echo "<script type=\"text/javascript\" src=\"js/body.js\"></script>\n";

  echo "<script type=\"text/javascript\">\n";

  echo "function bodyPageLoaded()\n";
  echo "{\n";
  if (isCurrentUserInfoComplete())
  {
    echo "  body_enableVehiclesButton(true);\n";
    if (doesUserHaveVehicles())
      echo "  body_enableRegisterButton(true);\n";
    else
      echo "  body_enableRegisterButton(false);\n";
  }
  else
  {
    echo "  body_enableVehiclesButton(false);\n";
    echo "  body_enableRegisterButton(false);\n";
  }
  echo "  parent.main_hideLoader();\n";
  echo "  body_run();\n";
  echo "}\n";

  echo "</script>\n";

  echo "</head>\n<body>\n";

  echo "<div class=\"class_content\">\n</div>\n";

  echo "<div id=\"div_editInfo\" class=\"class_bodyButton\">\n";
  echo "Step 1.<br />Edit My Info & Membership<br />\n";
  echo "<img src=\"images/classy-icons-set/png/128x128/user_info.png\" id=\"img_editInfo\" alt=\"Edit Info\" ";
  echo "onclick=\"body_buttonMouseOut('img_editInfo');parent.main_openPopupWindow('userinfo.php')\" ";
  echo "onmouseover=\"body_buttonMouseOver('img_editInfo')\" ";
  echo "onmouseout=\"body_buttonMouseOut('img_editInfo')\" ";
  echo "/>\n";
  echo "</div>\n";

  echo "<div id=\"div_addVehicles\" class=\"class_bodyButton\">\n";
  echo "Step 2.<br />Edit My Vehicles<br />\n";
  echo "<img src=\"images/classy-icons-set/png/128x128/process_add.png\" id=\"img_addVehicles\" alt=\"Edit Info\" ";
  echo "onmouseover=\"body_buttonMouseOver('img_addVehicles')\" ";
  echo "onmouseout=\"body_buttonMouseOut('img_addVehicles')\" ";
  echo "/>\n";
  echo "</div>\n";

  echo "<div id=\"div_findEvent\" class=\"class_bodyButton\">\n";
  echo "Step 3.<br />Find Event & Register<br />\n";
  echo "<img src=\"images/classy-icons-set/png/128x128/folder_search.png\" id=\"img_findEvent\" alt=\"Find Event\" ";
  echo "onmouseover=\"body_buttonMouseOver('img_findEvent')\" ";
  echo "onmouseout=\"body_buttonMouseOut('img_findEvent')\" ";
  echo "/>\n";
  echo "</div>\n";

  echo "<div id=\"div_changePassword\" class=\"class_bodyButton\">Change Password<br />\n";
  echo "<img src=\"images/classy-icons-set/png/128x128/notebook_lock.png\" id=\"img_changePassword\" alt=\"Change Password\" ";
  echo "onclick=\"body_buttonMouseOut('img_changePassword');parent.main_openPopupWindow('password.php')\" ";
  echo "onmouseover=\"body_buttonMouseOver('img_changePassword')\" ";
  echo "onmouseout=\"body_buttonMouseOut('img_changePassword')\" ";
  echo "/>";
  echo "</div>\n";

  echo "<div id=\"div_feedback\" class=\"class_bodyButton\">Send Site Feedback<br />\n";
  echo "<img src=\"images/classy-icons-set/png/128x128/mail_edit.png\" id=\"img_feedback\" alt=\"Find Event\" ";
  echo "onclick=\"body_buttonMouseOut('img_feedback');parent.main_openPopupWindow('feedback.php')\" ";
  echo "onmouseover=\"body_buttonMouseOver('img_feedback')\" ";
  echo "onmouseout=\"body_buttonMouseOut('img_feedback')\" ";
  echo "/>\n";
  echo "</div>\n";

  echo "<div id=\"div_logout\" class=\"class_bodyButton\">Logout<br />\n";
  echo "<img src=\"images/classy-icons-set/png/128x128/computer_delete.png\" id=\"img_logout\" alt=\"Find Event\" ";
  echo "onclick=\"body_buttonMouseOut('img_logout');parent.main_userLogout()\" ";
  echo "onmouseover=\"body_buttonMouseOver('img_logout')\" ";
  echo "onmouseout=\"body_buttonMouseOut('img_logout')\" ";
  echo "/>\n";
  echo "</div>\n";

  echo "<script type=\"text/javascript\">\n";
  echo "window.onload = bodyPageLoaded;\n";
  echo "</script>\n";
  echo "</body>\n</html>\n";
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

  displayBodyPage();

  ignore_user_abort(true);
  updateMemberStatus();

  die(); // attempt to guard against any code insertion at the end of the file
}
?>