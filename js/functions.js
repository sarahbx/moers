// js/functions.js
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

function forceSSL()
{
  if (document.location.protocol != "https:")
  {
    document.location.href = "https://" + document.location.hostname + document.location.pathname;
  }
}

function positionElement(elementId, position, xoffset, yoffset, zIndex)
{
  var element = document.getElementById(elementId);
  if (position == "TopLeft")
  {
    element.style.top = yoffset+"px";
    element.style.left = xoffset+"px";
  }
  else if (position == "TopRight")
  {
    element.style.top = yoffset+"px";
    element.style.left = browserWidth-element.clientWidth-xoffset+"px";
  }
  else if (position == "BottomLeft")
  {
    element.style.top = browserHeight-element.clientHeight-yoffset+"px";
    element.style.left = xoffset+"px";
  }
  else if (position == "BottomRight")
  {
    element.style.top = browserHeight-element.clientHeight-yoffset+"px";
    element.style.left = browserWidth-element.clientWidth-xoffset+"px";
  }
  else if (position == "Center")
  {
    element.style.top = (browserHeight/2)-(element.clientHeight/2)+"px";
    element.style.left = (browserWidth/2)-(element.clientWidth/2)+"px";
  }
  else if (position == "HorizontalCenter")
  {
    element.style.left = (browserWidth/2)-(element.clientWidth/2)+"px";
  }
  else if (position == "VerticalCenter")
  {
    element.style.top = (browserHeight/2)-(element.clientHeight/2)+"px";
  }
  else if (position == "TopCenter")
  {
    element.style.top = yoffset+"px";
    element.style.left = (browserWidth/2)-(element.clientWidth/2)+"px";
  }
  else if (position == "BottomCenter")
  {
    element.style.top = browserHeight-element.clientHeight-yoffset+"px";
    element.style.left = (browserWidth/2)-(element.clientWidth/2)+"px";
  }
  else if (position == "LeftCenter")
  {
    element.style.top = (browserHeight/2)-(element.clientHeight/2)+"px";
    element.style.left = xoffset+"px";
  }
  else if (position == "RightCenter")
  {
    element.style.top = (browserHeight/2)-(element.clientHeight/2)+"px";
    element.style.left = browserWidth-element.clientWidth-xoffset+"px";
  }
  else
  {}
  if (zIndex != "null") // notice it is a string, not actually 'null'. null would equate to 0.
  {
    element.style.zIndex = zIndex;
  }
}
