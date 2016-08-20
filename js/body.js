// js/body.js
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
    must be removed from this file before distribution.

************************************************************************************/

function body_run()
{
  var bodyInverval;
  var elementsAquired;
  var element_hash = [];

  do {
    elementsAquired = body_aquireElements(element_hash);
  } while (!elementsAquired);
  body_adjustLayout(element_hash);
  bodyInterval = setInterval(function() { body_adjustLayout(element_hash); }, 250);
}

function body_aquireElements(element_hash)
{
  element_hash["parent_div_body"] = parent.document.getElementById('div_body');
  element_hash["div_0"] = document.getElementById('div_editInfo');
  element_hash["img_0"] = document.getElementById('img_editInfo');
  element_hash["div_1"] = document.getElementById('div_addVehicles');
  element_hash["img_1"] = document.getElementById('img_addVehicles');
  element_hash["div_2"] = document.getElementById('div_findEvent');
  element_hash["img_2"] = document.getElementById('img_findEvent');
  element_hash["div_3"] = document.getElementById('div_changePassword');
  element_hash["img_3"] = document.getElementById('img_changePassword');
  element_hash["div_4"] = document.getElementById('div_feedback');
  element_hash["img_4"] = document.getElementById('img_feedback');
  element_hash["div_5"] = document.getElementById('div_logout');
  element_hash["img_5"] = document.getElementById('img_logout');

  // check for undefined elements
  for (key in element_hash)
  {
    if (!element_hash[key])
      return false;
  }
  return true;
}

function body_adjustLayout(element_hash)
{
  var i;
  var height = parent.browserHeight;
  var width = parent.browserWidth;
  var offset = element_hash["parent_div_body"].offsetTop;
  var imgHeight;
  var imgWidth;
  var imgHDiv2;
  var divWidth;
  var divHeight;
  var divFontSize;
  var divLeft = new Array(6);
  var divTop = new Array(6);

  // check for undefined variables
  if (!height || !width || !offset)
    return;

  height -= offset; // subtract the page header from the available space

  if (width >= height) // display layout 3 wide, 2 high.
  {
	divWidth = width / 3;
	divHeight = height / 2;
	
    imgHeight = height / 4;
    if (imgHeight > 128) // image size is only 128px. bigger looks bad.
      imgHeight = 128;

    imgHDiv2 = imgHeight / 2;

    divTop[0] = divTop[1] = divTop[2] = ((height/4)-imgHDiv2).toString() + "px";
    divTop[3] = divTop[4] = divTop[5] = (((height/4)*3)-imgHDiv2).toString() + "px";

    divLeft[0] = divLeft[3] = ((width/4)-imgHDiv2).toString() + "px";
    divLeft[1] = divLeft[4] = ((width/2)-imgHDiv2).toString() + "px";
    divLeft[2] = divLeft[5] = (((width/4)*3)-imgHDiv2).toString() + "px";
  }
  else // display layout 2 wide, 3 high
  {
	divWidth = width / 2;
	divHeight = height / 3;

    imgHeight = height / 5;
    if (imgHeight > 128) // image size is only 128px. bigger looks bad.
      imgHeight = 128;

    imgHDiv2 = imgHeight / 2;

    divTop[0] = divTop[1] = ((height/5)-imgHDiv2).toString() + "px";
    divTop[2] = divTop[3] = (((height/5)*2.5)-imgHDiv2).toString() + "px";
    divTop[4] = divTop[5] = (((height/5)*4)-imgHDiv2).toString() + "px";

    divLeft[0] = divLeft[2] = divLeft[4] = ((width/4)-imgHDiv2).toString() + "px";
    divLeft[1] = divLeft[3] = divLeft[5] = ((width/4)*2-imgHDiv2).toString() + "px";
  }

  divHeight += "px";
  divWidth = imgHeight + "px";
  divFontSize = (imgHeight/128).toString()+"em";

  imgHeight += "px";

  // set element properties
  for (key in element_hash)
  {
    if (/^div_/.test(key))
    {
      element_hash[key].style.position = "absolute";
      element_hash[key].style.fontSize = divFontSize;
      element_hash[key].style.width = divWidth;
//      element_hash[key].style.height = divHeight;
    }
    if (/^img_/.test(key))
      element_hash[key].style.height = imgHeight;
  }

  // position elements
  for (i=0; i < 6; i++)
  {
    element_hash["div_"+i.toString()].style.left = divLeft[i];
    element_hash["div_"+i.toString()].style.top = divTop[i];
    element_hash["div_"+i.toString()].style.zIndex = 1;
  }

  // technically we should not have to delete objects, should be garbage collected automatically,
  // however, since this function is run so often, not a bad practice
  delete divTop;
  delete divLeft;
}

function body_enableVehiclesButton(boolValue)
{
  body_enableButton(boolValue, "div_addVehicles", "img_addVehicles", 'vehicles.php');
}

function body_enableRegisterButton(boolValue)
{
  body_enableButton(boolValue, "div_findEvent", "img_findEvent", 'eventList.php');
}

function body_enableButton(boolValue, divId, imgId, filename)
{
  if (boolValue == true)
  {
    document.getElementById(divId).style.opacity = 1.0;
    document.getElementById(divId).style.filter = "alpha(opacity=100)";
    document.getElementById(imgId).onclick = function() { body_buttonMouseOut(imgId); parent.main_openPopupWindow(filename); };
  }
  else
  {
    document.getElementById(divId).style.opacity = 0.2;
    document.getElementById(divId).style.filter = "alpha(opacity=20)";
    document.getElementById(imgId).onclick = null;
  }
}

function body_buttonMouseOver(imgId)
{
  document.getElementById(imgId).style.backgroundColor = "#f5f5f5";
}

function body_buttonMouseOut(imgId)
{
  document.getElementById(imgId).style.backgroundColor = "white";
}