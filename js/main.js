// js/main.js
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

var browserWidth;
var browserHeight;

function main_run()
{
  var mainInterval;
  var elementsAquired;
  var element_hash = [];
  var elements = ["div_content", "img_headLogo"];
//   if (/main.php(#)?$/.test(window.location))
  if (/main.php/.test(window.location))
  {
    elements.push("div_body", "iframe_body");
  }
  do {
    elementsAquired = main_aquireElements(elements, element_hash);
  } while (!elementsAquired);

  mainInterval = setInterval(function() { main_adjustLayout(element_hash); }, 250);
}

function main_aquireElements(elements, element_hash)
{
  for (key in elements)
    element_hash[elements[key]] = document.getElementById(elements[key]);
  for (key in element_hash)
  {
    if (!element_hash[key])
      return false;
  }
  return true;
}

function main_adjustLayout(element_hash)
{
  // modified from: http://brondsema.net/blog/index.php/2007/06/06/100_height_iframe
  var ieElement;
  var height = browserHeight;
  var width = browserWidth;
  var delta = false;
  
  delta = main_checkBrowserSize();

  height = browserHeight;
  width = browserWidth;

  try {
    height -= element_hash["div_body"].offsetTop;
    element_hash["iframe_body"].style.height = height +"px";
    positionElement("id_rackspace", "BottomRight", 5, 5, -3);
  }
  catch(err) {
    height -= element_hash["div_content"].offsetTop;
    positionElement("id_rackspace", "BottomRight", 5, 5, 3);
  }

  if (width < 750)
    element_hash["img_headLogo"].style.width = width + "px";
  else
    element_hash["img_headLogo"].style.width = "750px";

  if (delta)
    main_popupWindowLoaded();
}

function main_checkBrowserSize()
{
  // modified from: http://www.howtocreate.co.uk/tutorials/javascript/browserwindow
  var delta = false;
  var myWidth = 0, myHeight = 0;
  if( typeof( window.innerWidth ) == 'number' ) {
    //Non-IE
    myWidth = window.innerWidth;
    myHeight = window.innerHeight;
  } else if( document.documentElement && ( document.documentElement.clientWidth || document.documentElement.clientHeight ) ) {
    //IE 6+ in 'standards compliant mode'
    myWidth = document.documentElement.clientWidth;
    myHeight = document.documentElement.clientHeight;
  } else if( document.body && ( document.body.clientWidth || document.body.clientHeight ) ) {
    //IE 4 compatible
    myWidth = document.body.clientWidth;
    myHeight = document.body.clientHeight;
  }
  if (myHeight != browserHeight || myWidth != browserWidth)
    delta = true;
  browserHeight = myHeight;
  browserWidth = myWidth;
  return delta;
}

function main_setBodyFrame(filename)
{
  document.getElementById("iframe_body").src = filename;
}

function main_showLoader()
{
  document.getElementById("div_loading").style.zIndex = 10;
}

function main_hideLoader()
{
  document.getElementById("div_loading").style.zIndex = -2;
}

function main_popupWindowLoaded()
{
  var x = 0;
  var y = 0;
  var pFrame;
  var pFrameWidth;
  var pFrameHeight;
  var pFrameSrc;
    
  pFrame = document.getElementById("iframe_popup");

  try {
    pFrameWidth = pFrame.contentWindow.document.body.scrollWidth;
  }
  catch (err) {
    main_popupWindowLoaded();
    return;
  }

  pFrameSrc = document.getElementById("iframe_popup").src;

  if ((/about.php$/.test(pFrameSrc)))
    pMaxWidth = browserWidth*0.4;
  else if ((/password.php$/.test(pFrameSrc)) || (/feedback.php$/.test(pFrameSrc)))
    pMaxWidth = browserWidth*0.5;
  else if ((/userinfo.php$/.test(pFrameSrc)) || (/vehicles.php$/.test(pFrameSrc)))
    pMaxWidth = browserWidth*0.6;
  else
    pMaxWidth = browserWidth*0.9;

  pFrame.width = 100;
  do {
    pFrameWidth = pFrame.contentWindow.document.body.scrollWidth;
    if (pFrameWidth > pFrame.contentWindow.document.body.clientWidth)
    {
      if (pFrameWidth > browserWidth*0.9)
      {
	    pFrame.width = browserWidth*0.9;
	    break;
      }
      pFrame.width += 25
    }
    else if (pFrameWidth > pMaxWidth)
    {
      pFrameWidth = pMaxWidth;
      break;
    }
    else
    {
      pFrame.width += 25;
    }
  }  
  while (pFrameWidth < pMaxWidth);

  pFrameHeight = pFrame.contentWindow.document.body.scrollHeight;
  if (pFrameHeight > browserHeight*0.9)
    pFrameHeight = browserHeight*0.9;

  x = (browserWidth/2) - (pFrameWidth/2);
  y = (browserHeight/2) - (pFrameHeight/2);
  pFrame.height = pFrameHeight;
  pFrame.width = pFrameWidth;

  pClose = document.getElementById("img_popup_close");

  document.getElementById("div_popup").style.left = x.toString()+"px";
  document.getElementById("div_popup").style.top = y.toString()+"px";
  x -= pClose.width / 2;
  y -= pClose.height / 2;
  document.getElementById("div_popup_close").style.left = (x-5).toString()+"px";
  document.getElementById("div_popup_close").style.top = (y-5).toString()+"px";
  document.getElementById("div_popup_back").style.left = ((x-5)+(pClose.width*2)).toString()+"px";
  document.getElementById("div_popup_back").style.top = (y-5).toString()+"px";

  if (!(/blank.html$/).test(document.getElementById("iframe_popup").src))
  {
    document.getElementById("div_popup").style.zIndex = 9;
    document.getElementById("div_popup_close").style.zIndex = 10;
  }
}

function main_enableVehiclesButton(boolValue)
{
  document.getElementById('iframe_body').contentWindow.body_enableVehiclesButton(boolValue);
}

function main_enableRegisterButton(boolValue)
{
  document.getElementById('iframe_body').contentWindow.body_enableRegisterButton(boolValue);
}

function main_openPopupWindow(filename)
{
  if (filename != '')
  {
	document.getElementById("iframe_popup").width = browserWidth*0.8;
    document.getElementById("div_loading").style.zIndex = 10;
  
    document.getElementById("div_fullscreen").style.zIndex = 8;
    document.body.style.overflow = 'hidden';

    document.getElementById("iframe_popup").src=filename;

    main_disablePopupBackButton();
  }
}

function main_popupWindowCancel()
{
  main_disablePopupBackButton();
  document.getElementById("div_popup_close").style.zIndex = -2;
  document.getElementById("div_loading").style.zIndex = -2;

  document.getElementById("div_popup").style.zIndex = -2;
  document.getElementById("iframe_popup").src="blank.html";

  document.getElementById("div_fullscreen").style.zIndex = -1;
  document.body.style.overflow = 'visible';

  document.getElementById("iframe_popup").height = 100;
  document.getElementById("iframe_popup").Width = 100;
}

function main_disablePopupBackButton()
{
   document.getElementById("div_popup_back").style.zIndex = -2;
   document.getElementById("img_popup_back").onclick = "";
}

function main_enablePopupBackButton(filename)
{
   document.getElementById("div_popup_back").style.zIndex = 10;
   document.getElementById("img_popup_back").onclick = function(){main_openPopupWindow(filename)};
}

function main_enablePopupBackButtonHistory()
{
   document.getElementById("div_popup_back").style.zIndex = 10;
   document.getElementById("img_popup_back").onclick = function(){history.go(-1)};
}

function main_userLogout()
{
  var r=confirm("To logout, press OK. Otherwise press Cancel to continue.");
  if (r==true)
  {
     window.location.replace("logout.php");
  }
}
