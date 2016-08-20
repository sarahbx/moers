// js/password.js
/************************************************************************************
    Copyright © 2008-2009 xhub.com

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
  var t;
  var jPass0 = document.getElementById("pass0").value;
  var jPass1 = document.getElementById("pass1").value;
  var jPass2 = document.getElementById("pass2").value;
  
  if ((jPass0 != "" && jPass1 != "" && jPass2 != "" && jPass1 == jPass2) &&
      (isValid(jPass0) && isValid(jPass1) && isValid(jPass2))
     )
  {
    document.getElementById("span_pass1").innerHTML = "Valid";
    while (document.getElementById("span_pass2").innerHTML != "Valid")
      document.getElementById("span_pass2").innerHTML = "Valid";
    enableSubmitButton();
  }
  else
  {
    if (jPass1 != "" && !isValid(jPass1))
      document.getElementById("span_pass1").innerHTML = "Invalid";
    else if (jPass1 != "" && isValid(jPass1))
      document.getElementById("span_pass1").innerHTML = "Valid";
    
    if (jPass2 != "" && !isValid(jPass2))
      document.getElementById("span_pass2").innerHTML = "Invalid";
    else if (jPass2 != "" && isValid(jPass2) && jPass1 == jPass2)
      document.getElementById("span_pass2").innerHTML = "Valid";
    else if (jPass2 != "" && isValid(jPass2) && jPass1 != jPass2)
      document.getElementById("span_pass2").innerHTML = "Does not match";

    disableSubmitButton();
  }
  t=setTimeout("inputChanged()",500);
}

function disableSubmitButton()
{
  document.getElementById("submitButton").disabled=true;
}

function enableSubmitButton()
{
  document.getElementById("submitButton").disabled=false;
}

function isValid(tPass)
{
  if (tPass.length >= 8 && tPass.search(/[a-z]/) != -1 &&
      tPass.search(/[A-Z]/) != -1 && tPass.search(/[0-9]/) != -1)
    return true;
  else
    return false;
}