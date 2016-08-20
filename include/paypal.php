<?php
// include/paypal.php
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

function userPaidEvent($info, $eventinfo, &$amount, &$member, &$type, &$method)
{
    require 'include/configGlobals.php';
	$itemName = $club_Abbr." ".$eventinfo['eventType']." ".$eventinfo['eventName']." ".$eventinfo['eventDate'];
	$hashinput = $itemName . $info['username'];
	$itemNumber = hash('md5', $hashinput);

	$paymentStatus = "";
	$palcheck = mysql_query("SELECT * FROM paypal_payment_info WHERE itemnumber = '$itemNumber'") or die(mysql_error());
	while ($palinfo = mysql_fetch_assoc($palcheck))
	{
	  $paymentStatus = $palinfo['paymentstatus'];
	}

	  if ($info['member'] == 1)
          {
            $member = "Yes";
	    $type = "Online-".$info['club'];
          }
	  else if ($info['member'] == 2)
          {
            $member = "Yes";
	    $type = "Offline-".$info['club'];
          }
	  else if ($info['member'] == 3)
          {
            $member = "No";
	    $type = "Partner-".$info['club'];
          }
	  else
          {
            $member = "No";
	    $type = "Non-Member";
          }

	if ($paymentStatus == "Completed")
	{
          $paymentStatus = "Yes";
          $method = "Credit Card";

	  if ($eventinfo['eventType'] == "Autocross" && 
              ($info['member'] == 1 || $info['member'] == 2 || $info['member'] == 3))
          {
	    $amount = "40.00";
          }
	  else if ($eventinfo['eventType'] == "Autocross" && $info['member'] == 0)
	  {
            $amount = "50.00";
          }
	}
        else
        {
          $paymentStatus = "No";
          $amount = "";
        }
	return $paymentStatus;
}

function userPaidMembership($info, $eventinfo, &$amount, &$member, &$type, &$method)
{
    require 'include/configGlobals.php';
	$itemName = $club_Abbr." ".date('Y')." Membership";
	$hashinput = $itemName . $info['username'];
	$itemNumber = hash('md5', $hashinput);
	$itemAmount = "20.00";

	$paymentStatus = "";
	$palcheck = mysql_query("SELECT * FROM paypal_payment_info WHERE itemnumber = '$itemNumber'") or die(mysql_error());
	while ($palinfo = mysql_fetch_assoc($palcheck))
	{
	  $paymentStatus = $palinfo['paymentstatus'];
	}

	  if ($info['member'] == 1)
          {
            $member = "Yes";
	    $type = "Online-".$info['club'];
          }
	  else if ($info['member'] == 2)
          {
            $member = "Yes";
	    $type = "Offline-".$info['club'];
          }
	  else if ($info['member'] == 3)
          {
            $member = "No";
	    $type = "Partner-".$info['club'];
          }
	  else
          {
            $member = "No";
	    $type = "Non-Member";
          }

	if ($paymentStatus == "Completed")
	{
          $paymentStatus = "Yes";
          $method = "Credit Card";
          $amount = $itemAmount;
	}
        else
        {
          $paymentStatus = "No";
          $amount = "";
        }
	return $paymentStatus;
}

function displayPaypalEvent($info, $eventinfo)
{
    require 'include/configGlobals.php';
	$itemName = $club_Abbr." ".$eventinfo['eventType']." ".$eventinfo['eventName']." ".$eventinfo['eventDate'];
	$hashinput = $itemName . $info['username'];
	$itemNumber = hash('md5', $hashinput);

	$paymentStatus = "";
	$tempi = 1;
	$palcheck = mysql_query("SELECT * FROM paypal_payment_info WHERE itemnumber = '$itemNumber'") or die(mysql_error());
	while ($palinfo = mysql_fetch_assoc($palcheck))
	{
		$paymentStatus = $palinfo['paymentstatus'];
		
		if ($palinfo['paymentstatus'] == "Pending")
		{
			echo "Payment ".$tempi.": ".$palinfo['paymentstatus']." - ".$palinfo['pendingreason']."<br>\n";
		}
		else
		{
			echo "Payment ".$tempi.": ".$palinfo['paymentstatus']."<br>\n";
		}
		$tempi += 1;
	}
  
  // if no payment status, check to see if they paid for all regular autocross events
  $tempsearch = strpos($eventinfo['eventName'], "Event");
  if ($paymentStatus == "" && $eventinfo['eventType'] == "Autocross" && !($tempsearch === false))
  {
    $tempName = "All ".date('Y')." Autocross Regular Events";
    $tempDate = "2010-05-16";
    $tempItemName = $club_Abbr." ".$eventinfo['eventType']." ".$tempName." ".$tempDate;
    $tempHashinput = $tempItemName . $info['username'];
    $tempItemNumber = hash('md5', $tempHashinput);

    $tempi = 1;
    $palcheck = mysql_query("SELECT * FROM paypal_payment_info WHERE itemnumber = '$tempItemNumber'") or die(mysql_error());
    while ($palinfo = mysql_fetch_assoc($palcheck))
    {
      $paymentStatus = $palinfo['paymentstatus'];
		
      if ($palinfo['paymentstatus'] == "Pending")
      {
        echo "Payment ".$tempi.": ".$palinfo['paymentstatus']." - ".$palinfo['pendingreason']."<br>\n";
      }
      else
      {
        echo "Payment ".$tempi.": ".$palinfo['paymentstatus']."<br>\n";
      }
      $tempi += 1;
    }
  }
		
	if ($paymentStatus != "Completed" &&
		$paymentStatus != "Pending" &&
		$paymentStatus != "Processed" &&
		$paymentStatus != "In-Progress")
	{
		if ($info['member'] == 1 || $info['member'] == 2)
			$itemName = $itemName." - Member";
		else if ($info['member'] == 3)
			$itemName = $itemName." - Partner-Member";
		else
			$itemName = $itemName." - Non-Member";
				
		if ($eventinfo['eventType'] == "Autocross" &&
            ($info['member'] == 1 || $info['member'] == 2 || $info['member'] == 3))
    {
      if ($eventinfo['eventName'] == "All ".date('Y')." Autocross Regular Events")
      {
        $itemAmount = "240.00";
      }
      else
      {
        $itemAmount = "40.00";
      }
    }
		else if ($eventinfo['eventType'] == "Autocross" && $info['member'] == 0)
    {
      if ($eventinfo['eventName'] == "All ".date('Y')." Autocross Regular Events")
      {
        $itemAmount = "300.00";
      }
      else
      {
        $itemAmount = "50.00";
      }
    }
		else if ($eventinfo['eventType'] == "Hillclimb")
		{
			if ($today <= $regLateCutoff)
				$itemAmount = "160.00";
			else
				$itemAmount = "195.00";
		}
		echo "$".$itemAmount;

		echo "<form action=\"https://www.paypal.com/cgi-bin/webscr\" method=\"post\" target=\"_top\">\n";
		echo "<input type=\"hidden\" name=\"cmd\" value=\"_xclick\">\n";
		echo "<input type=\"hidden\" name=\"business\" value=\"".$paypal_Email."\">\n";
		echo "<input type=\"hidden\" name=\"item_name\" value=\"".$itemName."\">\n";
		echo "<input type=\"hidden\" name=\"item_number\" value=\"".$itemNumber."\">\n";
		echo "<input type=\"hidden\" name=\"amount\" value=\"".$itemAmount."\">\n";
		echo "<input type=\"hidden\" name=\"no_shipping\" value=\"1\">\n";
		echo "<input type=\"hidden\" name=\"return\" value=\"".$paypal_Return."\">\n";
		echo "<input type=\"hidden\" name=\"cancel_return\" value=\"".$paypal_Cancel."\">\n";
		echo "<input type=\"hidden\" name=\"no_note\" value=\"1\">\n";
		echo "<input type=\"hidden\" name=\"currency_code\" value=\"USD\">\n";
		echo "<input type=\"hidden\" name=\"lc\" value=\"US\">\n";
		echo "<input type=\"hidden\" name=\"bn\" value=\"PP-BuyNowBF\">\n";
		echo "<input type=\"image\" width=\"100\" src=\"https://www.paypal.com/en_US/i/btn/btn_paynow_LG.gif\" border=\"0\" name=\"submit\" alt=\"PayPal - The safer, easier way to pay online!\">\n";
		echo "<img alt=\"\" border=\"0\" src=\"https://www.paypal.com/en_US/i/scr/pixel.gif\" width=\"1\" height=\"1\">\n";
		echo "</form>\n";
			
	}
	return $paymentStatus;
}

function updateMemberStatus()
{
  require 'include/configGlobals.php';
  $query = "SELECT * FROM users";
  $check = mysql_query($query);
  while ($info = mysql_fetch_assoc($check))
  {
	$itemName = $club_Abbr." ".date('Y')." Membership";
	$hashinput = $itemName . $info['username'];
	$itemNumber = hash('md5', $hashinput);
	$itemAmount = "20.00";

	$paymentStatus = "";
	//$tempi = 1;
	$palcheck = mysql_query("SELECT paymentstatus FROM paypal_payment_info WHERE itemnumber = '$itemNumber'") or die(mysql_error());
	while ($palinfo = mysql_fetch_assoc($palcheck))
	{
		$paymentStatus = $palinfo['paymentstatus'];
	}
		
	if ($paymentStatus != "Completed" &&
	    $paymentStatus != "Pending" &&
	    $paymentStatus != "Processed" &&
	    $paymentStatus != "In-Progress" &&
	    $info['member'] != 2)
	{
		if ($info['member'] == 1)
		{
    			$info['member'] = 0;
			$update = "UPDATE users SET member='0' WHERE username='".$info['username']."'";
			mysql_query($update) or die(mysql_error());	
		}
	}
	else if ($paymentStatus == "Completed" || $info['member'] == 2)
	{
		if ($info['member'] == 0)
		{			
			$info['member'] = 1;
        		$info['club'] = $club_Abbr;
			$update = "UPDATE users SET member='1', club='$clubAbbr' WHERE username='".$info['username']."'";
			mysql_query($update) or die(mysql_error());
			sendMembershipPaidEmail($info);
		}
	}
  }
}

function displayPaypalMembership(&$info)
{
  require 'include/configGlobals.php';
  
  $username = $info['username'];

	$itemName = $club_Abbr." ".date('Y')." Membership";
	$hashinput = $itemName . $info['username'];
	$itemNumber = hash('md5', $hashinput);
	$itemAmount = "20.00";

	$paymentStatus = "";
	//$tempi = 1;
	$palcheck = mysql_query("SELECT * FROM paypal_payment_info WHERE itemnumber = '$itemNumber'") or die(mysql_error());
	if ($palinfo = mysql_fetch_assoc($palcheck))
	{
		$paymentStatus = $palinfo['paymentstatus'];
		
		if ($palinfo['paymentstatus'] == "Pending")
		{
			echo "Payment : ".$palinfo['paymentstatus']." - ".$palinfo['pendingreason']."<br>\n";
		}
		else
		{
			echo "Payment : ".$palinfo['paymentstatus']."<br>\n";
		}
		//$tempi += 1;
	}
		
  if ($paymentStatus != "Completed" &&
  		$paymentStatus != "Pending" &&
	  	$paymentStatus != "Processed" &&
		  $paymentStatus != "In-Progress" &&
		  $info['member'] != 2)
	{
		if ($info['member'] == 0)
	    {
			  echo "*** If you are a ".$club_Abbr." member or member of any of our \"Partner Clubs\" but see this message, please complete your user info and select your club before paying for any events online. ***<br><br>\n";
	    }
		else if ($info['member'] == 1)
		{
			$info['member'] = 0;
			$update = "UPDATE users SET member='0' WHERE username='$username'";
			mysql_query($update) or die(mysql_error());	
		}
		echo "Not a ".$club_Abbr." member? Sign-up today!<br>\n";
		echo "<form action=\"https://www.paypal.com/cgi-bin/webscr\" method=\"post\" target=\"_top\">\n";
		echo "<input type=\"hidden\" name=\"cmd\" value=\"_xclick\">\n";
		echo "<input type=\"hidden\" name=\"business\" value=\"".$paypal_Email."\">\n";
		echo "<input type=\"hidden\" name=\"item_name\" value=\"".$itemName."\">\n";
		echo "<input type=\"hidden\" name=\"item_number\" value=\"".$itemNumber."\">\n";
		echo "<input type=\"hidden\" name=\"amount\" value=\"".$itemAmount."\">\n";
		echo "<input type=\"hidden\" name=\"no_shipping\" value=\"1\">\n";
		echo "<input type=\"hidden\" name=\"return\" value=\"".$paypal_Return."\">\n";
		echo "<input type=\"hidden\" name=\"cancel_return\" value=\"".$paypal_Cancel."\">\n";
		echo "<input type=\"hidden\" name=\"no_note\" value=\"1\">\n";
		echo "<input type=\"hidden\" name=\"currency_code\" value=\"USD\">\n";
		echo "<input type=\"hidden\" name=\"lc\" value=\"US\">\n";
		echo "<input type=\"hidden\" name=\"bn\" value=\"PP-BuyNowBF\">\n";
		echo "<input type=\"image\" src=\"https://www.paypal.com/en_US/i/btn/btn_paynowCC_LG.gif\" border=\"0\" name=\"submit\" alt=\"PayPal - The safer, easier way to pay online!\">\n";
		echo "<img alt=\"\" border=\"0\" src=\"https://www.paypal.com/en_US/i/scr/pixel.gif\" width=\"1\" height=\"1\">\n";
		echo "</form>\n";

	}
	else if ($paymentStatus == "Completed" || $info['member'] == 2)
	{
		echo "Status: ".date('Y')." Member";
		
		if ($info['member'] == 0)
		{			
		  $info['member'] = 1;
          $info['club'] = $club_Abbr;
		  $update = "UPDATE users SET member='1', club='$club_Abbr' WHERE username='$username'";
		  mysql_query($update) or die(mysql_error());
          sendMembershipPaidEmail($info);
		}
	}
	else
	{
		echo "Member Status: Pending";
	}
}


function sendMembershipPaidEmail($info)
{
  require 'include/configGlobals.php';
  $to      = $email_MembershipDirector.", ".$email_Administrator;
  $from    = $email_Administrator;
  $subject = 'New Member Registered on the '.$club_Abbr.' Online Registration Site';
  $message = "--$mime_boundary\n";
  $message .= "Content-Type: text/plain; charset=UTF-8\r\n";
  $message .= "Content-Transfer-Encoding: 8bit\r\n";
  $message .= 'A new member has registered themselves online.'."\n".
	            'Payment has been completed through PayPal.'."\n".
		          "\n".
							'Registered: '."\n".
							$info['fname']." ".$info['lname']."\n".
							$info['addr1']."\n".
							$info['addr2']."\n".
							$info['city'].", ".$info['state'].", ".$info['zip']."\n".
							"\n".
							'Home Phone: '.$info['hphone']."\n".
							'Cell Phone: '.$info['cphone']."\n".
							"\n".
							'Email: '.$info['email']."\n".
							"\n".
							'Emergency Contact:'."\n".
							$info['econtact']."\n".
							$info['econtact_phone']."\n".
							$info['econtact_rel']."\n".
							"\n".
							'Thank you!'."\n".
							'- '.$club_Abbr.' Online Administrator'."\n";
  $message .= "--$mime_boundary--\n\n";
  $ret = sendEmail($to, $from, $subject, $message);
}
?>