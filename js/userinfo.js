// js/userinfo.js
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

function inputChanged()
{
  var i;
  var t;
  var disableButton;

  disableButton = 0;

  for (i=0; i < 11; i++)
  {
	if (document.getElementById("info"+i).value == "")
	{
	  disableButton = 1;
	  document.getElementById("info"+i).style.background = "red";			
	}
	else
	{
	  document.getElementById("info"+i).style.background = "white";			
	}
  }

  if (disableButton == 1)
  {
    document.getElementById("submitButton").disabled=true;
  }
  else
  {
    document.getElementById("submitButton").disabled=false;
  }
  t=setTimeout("inputChanged()",500);
}