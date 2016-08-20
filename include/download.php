<?php
require_once 'include/config.php';
require_once 'include/paypal.php';
require_once 'include/nolicense_ziplib.php';

function downloadEntries($eventKey)
{
  $eventQuery = "SELECT * from events WHERE eventID = '".$eventKey."'";
  $eventCheck = mysql_query($eventQuery);
  while ($eventInfo = mysql_fetch_array($eventCheck))
  {
    $eventDB = $eventInfo['eventDB'];
    $eventType = $eventInfo['eventType'];
    $eventDate = $eventInfo['eventDate'];

    $filename = $eventType."_".$eventDate.'-EntryList.txt';
    $handle = fopen($filename, "w+");
	$output = "Class\t".
                  "Number\t".
                  "First Name\t".
                  "Last Name\t".
                  "Car Model\t".
                  "Car Color\t".
                  "Member\t".
                  "Paid\t".
                  "Amnt.\t".
                  "Method\t".
                  "Treadware\t\r\n";
	fwrite($handle, $output, strlen($output));

    $eventDBQuery = "SELECT * from ".$eventDB;
    $eventDBCheck = mysql_query($eventDBQuery);
    while ($eventDBInfo = mysql_fetch_array($eventDBCheck))
    {
	  
      $entryUser = $eventDBInfo['registeredUser'];
	  $entryVehicle = $eventDBInfo['vehicleKey'];
	  $entryNumber = $eventDBInfo['vehicleNumber'];
	  $entryClass = $eventDBInfo['vehicleClass'];
      
	  $userQuery = "SELECT * from users WHERE username = '".$entryUser."'";
      $userCheck = mysql_query($userQuery);
      while ($userInfo = mysql_fetch_array($userCheck))
      {
        $vehicleQuery = "SELECT * from vehicles WHERE vehicleID = '".$entryVehicle."'";
        $vehicleCheck = mysql_query($vehicleQuery);
        while ($vehicleInfo = mysql_fetch_array($vehicleCheck))
        {
                  $amount = ""; $member = ""; $type = ""; $method = "";
                  $paid = userPaidEvent($userInfo, $eventInfo, $amount, $member, $type, $method);
		  $output = getAxwareClass($vehicleInfo['sccnh_class'],$vehicleInfo['scca_class'])."\t". // class
		            $entryNumber."\t". // number
		            $userInfo['fname']."\t". // first name
			    $userInfo['lname']."\t". // last name
			    $vehicleInfo['year']." ".$vehicleInfo['make']." ".$vehicleInfo['model']."\t". // vehicle
			    $vehicleInfo['color']."\t". // vehicle color
                            $member."\t". // member
                            $paid."\t". // paid
                            $amount."\t". // amount
                            $method."\t". // paid membership method
                            $vehicleInfo['treadware']."\t". // treadwear Yes, I know it's spelled wrong.
			    "\r\n";
          fwrite($handle, $output, strlen($output));
        }
      }
    }
      header('Pragma: no-cache');
      header('Cache-Control: no-cache, must-revalidate');
      header("Content-type: application/octet-stream");
      header("Content-Disposition: attachment; filename=\"" . basename($filename) . "\"");
      header("Content-length: ".(string)(filesize($filename)));
      header("Content-Transfer-Encoding: binary\n");

	  rewind($handle);

      fpassthru($handle);

      fclose($handle);
      unlink($filename, $handle);
  }
}

function downloadMember($eventKey)
{
  updateMemberStatus();
  $eventQuery = "SELECT * from events WHERE eventID = '".$eventKey."'";
  $eventCheck = mysql_query($eventQuery);
  while ($eventInfo = mysql_fetch_array($eventCheck))
  {
    $eventDB = $eventInfo['eventDB'];
    $eventType = $eventInfo['eventType'];
    $eventDate = $eventInfo['eventDate'];

    $filename = $eventType."_".$eventDate.'-MembershipList.txt';
    $handle = fopen($filename, "w+");
	$output = "Class\t".
                  "Number\t".
                  "First Name\t".
                  "Last Name\t".
                  "Car Model\t".
                  "Car Color\t".
                  "Member\t".
                  "Member Type\t".
                  "Paid\t".
                  "Amount\t".
                  "Paid/Method\t".
                  "Address\t".
                  "City\t".
                  "State\t".
                  "zip\t".
                  "Home\t".
                  "Cell\t".
                  "Email #1\t".
                  "Treadware\t\r\n";
	fwrite($handle, $output, strlen($output));

    $eventDBQuery = "SELECT * from ".$eventDB;
    $eventDBCheck = mysql_query($eventDBQuery);
    while ($eventDBInfo = mysql_fetch_array($eventDBCheck))
    {
	  
      $entryUser = $eventDBInfo['registeredUser'];
	  $entryVehicle = $eventDBInfo['vehicleKey'];
	  $entryNumber = $eventDBInfo['vehicleNumber'];
	  $entryClass = $eventDBInfo['vehicleClass'];
      
	  $userQuery = "SELECT * from users WHERE username = '".$entryUser."'";
      $userCheck = mysql_query($userQuery);
      while ($userInfo = mysql_fetch_array($userCheck))
      {
        $vehicleQuery = "SELECT * from vehicles WHERE vehicleID = '".$entryVehicle."'";
        $vehicleCheck = mysql_query($vehicleQuery);
        while ($vehicleInfo = mysql_fetch_array($vehicleCheck))
        {
                  $amount = ""; $member = ""; $type = ""; $method = "";
                  $paid = userPaidMembership($userInfo, $eventInfo, $amount, $member, $type, $method);
		  $output = getAxwareClass($vehicleInfo['sccnh_class'],$vehicleInfo['scca_class'])."\t". // class
		            $entryNumber."\t". // number
		            $userInfo['fname']."\t". // first name
			    $userInfo['lname']."\t". // last name
			    $vehicleInfo['year']." ".$vehicleInfo['make']." ".$vehicleInfo['model']."\t". // vehicle
			    $vehicleInfo['color']."\t". // vehicle color
                            $member."\t". // member
                            $type."\t". // member type
                            $paid."\t". // paid
                            $amount."\t". // amount
                            $method."\t". // paid membership method
                            $userInfo['addr1']." ".$userInfo['addr2']."\t". // address
                            $userInfo['city']."\t". // city
                            $userInfo['state']."\t". // state
                            $userInfo['zip']."\t".// zip
                            $userInfo['hphone']."\t". // home
                            $userInfo['cphone']."\t". // cell
                            $userInfo['email']."\t". // email
                            $vehicleInfo['treadware']."\t". // treadwear Yes, I know it's spelled wrong.
			    "\r\n";
          fwrite($handle, $output, strlen($output));
        }
      }
    }
      header('Pragma: no-cache');
      header('Cache-Control: no-cache, must-revalidate');
      header("Content-type: application/octet-stream");
      header("Content-Disposition: attachment; filename=\"" . basename($filename) . "\"");
      header("Content-length: ".(string)(filesize($filename)));
      header("Content-Transfer-Encoding: binary\n");

	  rewind($handle);

      fpassthru($handle);

      fclose($handle);
      unlink($filename, $handle);
  }

}

function downloadPrintableList($eventKey)
{
  require 'include/configGlobals.php';
  updateMemberStatus();
  $eventQuery = "SELECT * from events WHERE eventID = '".$eventKey."'";
  $eventCheck = mysql_query($eventQuery);
  while ($eventInfo = mysql_fetch_array($eventCheck))
  {
    $eventDB = $eventInfo['eventDB'];
    $eventType = $eventInfo['eventType'];
    $eventDate = $eventInfo['eventDate'];

    $filename = $eventType."_".$eventDate.'-PrintableList.html';
    $handle = fopen($filename, "w+");

    $output = "<html><head><title>".$club_Abbr." Pre-registration List : ".$eventType." ".$eventDate."</title></head>\n".
              "<body><center><h2>".$club_Abbr." Pre-registration List : ".$eventType." ".$eventDate."</h2>\n".
              "Downloaded: ".date('r')."</center><br /><table border=\"1\">\n";
    fwrite($handle, $output, strlen($output));

	$output = "<tr><td>Class</td>".
                  "<td>Number</td>".
                  "<td>Name</td>".
                  "<td>Car</td>".
                  "<td>Member Status</td>".
                  "<td>Event Status</td>".
                  "<td>Address</td>".
                  "<td>Phone</td>".
                  "<td>Emergency</td></tr>\r\n";
	fwrite($handle, $output, strlen($output));

    $eventDBQuery = "SELECT * from ".$eventDB." ORDER BY 'vehicleNumber' ASC"; // Added order for ease at event check-in
    $eventDBCheck = mysql_query($eventDBQuery);
    while ($eventDBInfo = mysql_fetch_array($eventDBCheck))
    {
	  
      $entryUser = $eventDBInfo['registeredUser'];
	  $entryVehicle = $eventDBInfo['vehicleKey'];
	  $entryNumber = $eventDBInfo['vehicleNumber'];
	  $entryClass = $eventDBInfo['vehicleClass'];
      
	  $userQuery = "SELECT * from users WHERE username = '".$entryUser."'";
      $userCheck = mysql_query($userQuery);
      while ($userInfo = mysql_fetch_array($userCheck))
      {
        $vehicleQuery = "SELECT * from vehicles WHERE vehicleID = '".$entryVehicle."'";
        $vehicleCheck = mysql_query($vehicleQuery);
        while ($vehicleInfo = mysql_fetch_array($vehicleCheck))
        {
                  $amount = ""; $member = ""; $type = ""; $method = "";
                  $amount2 = ""; $member2 = ""; $type2 = ""; $method2 = "";

                  $paid = userPaidMembership($userInfo, $eventInfo, $amount, $member, $type, $method);
                  $paid2 = userPaidEvent($userInfo, $eventInfo, $amount2, $member2, $type2, $method2);

		  $output = "<tr><td>".getAxwareClass($vehicleInfo['sccnh_class'],$vehicleInfo['scca_class'])."</td>". // class
		            "<td>".$entryNumber."</td>". // number

			    "<td>".$userInfo['lname'].", ". // last name
		            $userInfo['fname']."</td>". // first name

			    "<td>".$vehicleInfo['color']." ". // vehicle color
			    $vehicleInfo['year']." ".$vehicleInfo['make']." ".$vehicleInfo['model']."<br />".
                            "Treadwear: ".$vehicleInfo['treadware']."</td>". // vehicle

                            "<td>".$type;
                            if ($type == "Online-".$club_Abbr)
                            {
                              $output .= "<br />". // member type
                              "Paid: ".$paid."<br />". // paid
                              "Amount: ".$amount."<br />". // amount
                              "Method: ".$method."</td>"; // paid membership method
                            }
                            else if ($type != "Non-Member")
                            {
                              $output .="<br />Need ID";
                            }
                            else
                            {
                              $output .= "</td>";
                            }

                            $output .= "<td>Paid: ".$paid2."<br />". //paid
                            "Amount: ".$amount2."<br />". // amount
                            "Method: ".$method2."</td>". // paid event method

                            "<td>".$userInfo['addr1']." ".$userInfo['addr2']."<br />". // address
                            $userInfo['city'].", ". // city
                            $userInfo['state']." ". // state
                            $userInfo['zip']."</td>".// zip

                            "<td>H:".$userInfo['hphone']."<br />". // home
                            "C:".$userInfo['cphone']."</td>". // cell

                            "<td>".$userInfo['econtact']."<br />".
                            $userInfo['econtact_rel']."<br />".
                            $userInfo['econtact_phone']."</td></tr>". // emergency contact info
			    "\r\n";
          fwrite($handle, $output, strlen($output));
        }
      }
    }
    $output = "</table></body></html>";
    fwrite($handle, $output, strlen($output));

      header('Pragma: no-cache');
      header('Cache-Control: no-cache, must-revalidate');
      header("Content-type: application/octet-stream");
      header("Content-Disposition: attachment; filename=\"" . basename($filename) . "\"");
      header("Content-length: ".(string)(filesize($filename)));
      header("Content-Transfer-Encoding: binary\n");

	  rewind($handle);

      fpassthru($handle);

      fclose($handle);
      unlink($filename, $handle);
  }

}

function downloadAllFiles($eventKey)
{
  updateMemberStatus();
  $eventQuery = "SELECT * from events WHERE eventID = '".$eventKey."'";
  $eventCheck = mysql_query($eventQuery);
  while ($eventInfo = mysql_fetch_array($eventCheck))
  {
    $eventDB = $eventInfo['eventDB'];
    $eventType = $eventInfo['eventType'];
    $eventDate = $eventInfo['eventDate'];

    $entriesFilename = $eventType."_".$eventDate.'-EntryList.txt';
    $memberFilename = $eventType."_".$eventDate.'-MembershipList.txt';
    $printableFilename = $eventType."_".$eventDate.'-PrintableList.html';
    $archiveFilename = $eventType."_".$eventDate.'-EventFiles.zip';

    $entriesHandle = fopen($entriesFilename, "w+");
    $memberHandle = fopen($memberFilename, "w+");
    $printableHandle = fopen($printableFilename, "w+");
    
    createEntriesFile($eventInfo, $entriesHandle);
    createMemberFile($eventInfo, $memberHandle);
    createPrintableFile($eventInfo, $printableHandle);
    
    fclose($entriesHandle);
    fclose($memberHandle);
    fclose($printableHandle);

    $zip = new Ziplib;
//    if ($archiveHandle = $zip_open($archiveFilename) )
//    {
      $zip->zl_add_file(file_get_contents($entriesFilename), $entriesFilename, "n");
      $zip->zl_add_file(file_get_contents($memberFilename), $memberFilename, "n");
      $zip->zl_add_file(file_get_contents($printableFilename), $printableFilename, "n");
//      $zip->close();
    
//      $archiveHandle = fopen($archiveFilename, "br");

      header('Pragma: no-cache');
      header('Cache-Control: no-cache, must-revalidate');
      header("Content-type: application/octet-stream");
      header("Content-Disposition: attachment; filename=\"" . basename($archiveFilename) . "\"");
//      header("Content-length: ".(string)(filesize($archiveFilename)));
      header("Content-Transfer-Encoding: binary\n");

    echo $zip->zl_pack("TEST");
//      fpassthru($archiveHandle);
//      fclose($archiveHandle);
//      unlink($archiveFilename, $archiveHandle);
//    }
    
    unlink($entriesFilename);
    unlink($memberFilename);
    unlink($printableFilename);
  }
}

function createEntriesFile($eventInfo, &$handle)
{
    $eventDB = $eventInfo['eventDB'];
    $eventType = $eventInfo['eventType'];
    $eventDate = $eventInfo['eventDate'];


	$output = "Class\t".
                  "Number\t".
                  "First Name\t".
                  "Last Name\t".
                  "Car Model\t".
                  "Car Color\t".
                  "Member\t".
                  "Paid\t".
                  "Amnt.\t".
                  "Method\t".
                  "Treadware\t\r\n";
	fwrite($handle, $output, strlen($output));

    $eventDBQuery = "SELECT * from ".$eventDB;
    $eventDBCheck = mysql_query($eventDBQuery);
    while ($eventDBInfo = mysql_fetch_array($eventDBCheck))
    {
	  
      $entryUser = $eventDBInfo['registeredUser'];
	  $entryVehicle = $eventDBInfo['vehicleKey'];
	  $entryNumber = $eventDBInfo['vehicleNumber'];
	  $entryClass = $eventDBInfo['vehicleClass'];
      
	  $userQuery = "SELECT * from users WHERE username = '".$entryUser."'";
      $userCheck = mysql_query($userQuery);
      while ($userInfo = mysql_fetch_array($userCheck))
      {
        $vehicleQuery = "SELECT * from vehicles WHERE vehicleID = '".$entryVehicle."'";
        $vehicleCheck = mysql_query($vehicleQuery);
        while ($vehicleInfo = mysql_fetch_array($vehicleCheck))
        {
                  $amount = ""; $member = ""; $type = ""; $method = "";
                  $paid = userPaidEvent($userInfo, $eventInfo, $amount, $member, $type, $method);
		  $output = getAxwareClass($vehicleInfo['sccnh_class'],$vehicleInfo['scca_class'])."\t". // class
		            $entryNumber."\t". // number
		            $userInfo['fname']."\t". // first name
			    $userInfo['lname']."\t". // last name
			    $vehicleInfo['year']." ".$vehicleInfo['make']." ".$vehicleInfo['model']."\t". // vehicle
			    $vehicleInfo['color']."\t". // vehicle color
                            $member."\t". // member
                            $paid."\t". // paid
                            $amount."\t". // amount
                            $method."\t". // paid membership method
                            $vehicleInfo['treadware']."\t". // treadwear Yes, I know it's spelled wrong.
			    "\r\n";
          fwrite($handle, $output, strlen($output));
        }
      }
    }

	  rewind($handle);
    
}

function createMemberFile($eventInfo, &$handle)
{
    $eventDB = $eventInfo['eventDB'];
    $eventType = $eventInfo['eventType'];
    $eventDate = $eventInfo['eventDate'];

	$output = "Class\t".
                  "Number\t".
                  "First Name\t".
                  "Last Name\t".
                  "Car Model\t".
                  "Car Color\t".
                  "Member\t".
                  "Member Type\t".
                  "Paid\t".
                  "Amount\t".
                  "Paid/Method\t".
                  "Address\t".
                  "City\t".
                  "State\t".
                  "zip\t".
                  "Home\t".
                  "Cell\t".
                  "Email #1\t".
                  "Treadware\t\r\n";
	fwrite($handle, $output, strlen($output));

    $eventDBQuery = "SELECT * from ".$eventDB;
    $eventDBCheck = mysql_query($eventDBQuery);
    while ($eventDBInfo = mysql_fetch_array($eventDBCheck))
    {
	  
      $entryUser = $eventDBInfo['registeredUser'];
	  $entryVehicle = $eventDBInfo['vehicleKey'];
	  $entryNumber = $eventDBInfo['vehicleNumber'];
	  $entryClass = $eventDBInfo['vehicleClass'];
      
	  $userQuery = "SELECT * from users WHERE username = '".$entryUser."'";
      $userCheck = mysql_query($userQuery);
      while ($userInfo = mysql_fetch_array($userCheck))
      {
        $vehicleQuery = "SELECT * from vehicles WHERE vehicleID = '".$entryVehicle."'";
        $vehicleCheck = mysql_query($vehicleQuery);
        while ($vehicleInfo = mysql_fetch_array($vehicleCheck))
        {
                  $amount = ""; $member = ""; $type = ""; $method = "";
                  $paid = userPaidMembership($userInfo, $eventInfo, $amount, $member, $type, $method);
		  $output = getAxwareClass($vehicleInfo['sccnh_class'],$vehicleInfo['scca_class'])."\t". // class
		            $entryNumber."\t". // number
		            $userInfo['fname']."\t". // first name
			    $userInfo['lname']."\t". // last name
			    $vehicleInfo['year']." ".$vehicleInfo['make']." ".$vehicleInfo['model']."\t". // vehicle
			    $vehicleInfo['color']."\t". // vehicle color
                            $member."\t". // member
                            $type."\t". // member type
                            $paid."\t". // paid
                            $amount."\t". // amount
                            $method."\t". // paid membership method
                            $userInfo['addr1']." ".$userInfo['addr2']."\t". // address
                            $userInfo['city']."\t". // city
                            $userInfo['state']."\t". // state
                            $userInfo['zip']."\t".// zip
                            $userInfo['hphone']."\t". // home
                            $userInfo['cphone']."\t". // cell
                            $userInfo['email']."\t". // email
                            $vehicleInfo['treadware']."\t". // treadwear Yes, I know it's spelled wrong.
			    "\r\n";
          fwrite($handle, $output, strlen($output));
        }
      }
    }

	  rewind($handle);
    
}

function createPrintableFile($eventInfo, &$handle)
{
  require 'include/configGlobals.php';
    $eventDB = $eventInfo['eventDB'];
    $eventType = $eventInfo['eventType'];
    $eventDate = $eventInfo['eventDate'];

    $output = "<html><head><title>".$club_Abbr." Pre-registration List : ".$eventType." ".$eventDate."</title></head>\n".
              "<body><center><h2>".$club_Abbr." Pre-registration List : ".$eventType." ".$eventDate."</h2>\n".
              "Downloaded: ".date('r')."</center><br /><table border=\"1\">\n";
    fwrite($handle, $output, strlen($output));

	$output = "<tr><td>Class</td>".
                  "<td>Number</td>".
                  "<td>Name</td>".
                  "<td>Car</td>".
                  "<td>Member Status</td>".
                  "<td>Event Status</td>".
                  "<td>Address</td>".
                  "<td>Phone</td>".
                  "<td>Emergency</td></tr>\r\n";
	fwrite($handle, $output, strlen($output));

    $eventDBQuery = "SELECT * from ".$eventDB." ORDER BY 'vehicleNumber' ASC"; // Added order for ease at event check-in
    $eventDBCheck = mysql_query($eventDBQuery);
    while ($eventDBInfo = mysql_fetch_array($eventDBCheck))
    {
	  
      $entryUser = $eventDBInfo['registeredUser'];
	  $entryVehicle = $eventDBInfo['vehicleKey'];
	  $entryNumber = $eventDBInfo['vehicleNumber'];
	  $entryClass = $eventDBInfo['vehicleClass'];
      
	  $userQuery = "SELECT * from users WHERE username = '".$entryUser."'";
      $userCheck = mysql_query($userQuery);
      while ($userInfo = mysql_fetch_array($userCheck))
      {
        $vehicleQuery = "SELECT * from vehicles WHERE vehicleID = '".$entryVehicle."'";
        $vehicleCheck = mysql_query($vehicleQuery);
        while ($vehicleInfo = mysql_fetch_array($vehicleCheck))
        {
                  $amount = ""; $member = ""; $type = ""; $method = "";
                  $amount2 = ""; $member2 = ""; $type2 = ""; $method2 = "";

                  $paid = userPaidMembership($userInfo, $eventInfo, $amount, $member, $type, $method);
                  $paid2 = userPaidEvent($userInfo, $eventInfo, $amount2, $member2, $type2, $method2);

		  $output = "<tr><td>".getAxwareClass($vehicleInfo['sccnh_class'],$vehicleInfo['scca_class'])."</td>". // class
		            "<td>".$entryNumber."</td>". // number

			    "<td>".$userInfo['lname'].", ". // last name
		            $userInfo['fname']."</td>". // first name

			    "<td>".$vehicleInfo['color']." ". // vehicle color
			    $vehicleInfo['year']." ".$vehicleInfo['make']." ".$vehicleInfo['model']."<br />".
                            "Treadwear: ".$vehicleInfo['treadware']."</td>". // vehicle

                            "<td>".$type;
                            if ($type == "Online-".$club_Abbr)
                            {
                              $output .= "<br />". // member type
                              "Paid: ".$paid."<br />". // paid
                              "Amount: ".$amount."<br />". // amount
                              "Method: ".$method."</td>"; // paid membership method
                            }
                            else if ($type != "Non-Member")
                            {
                              $output .="<br />Need ID";
                            }
                            else
                            {
                              $output .= "</td>";
                            }

                            $output .= "<td>Paid: ".$paid2."<br />". //paid
                            "Amount: ".$amount2."<br />". // amount
                            "Method: ".$method2."</td>". // paid event method

                            "<td>".$userInfo['addr1']." ".$userInfo['addr2']."<br />". // address
                            $userInfo['city'].", ". // city
                            $userInfo['state']." ". // state
                            $userInfo['zip']."</td>".// zip

                            "<td>H:".$userInfo['hphone']."<br />". // home
                            "C:".$userInfo['cphone']."</td>". // cell

                            "<td>".$userInfo['econtact']."<br />".
                            $userInfo['econtact_rel']."<br />".
                            $userInfo['econtact_phone']."</td></tr>". // emergency contact info
			    "\r\n";
          fwrite($handle, $output, strlen($output));
        }
      }
    }
    $output = "</table></body></html>";
    fwrite($handle, $output, strlen($output));

	  rewind($handle);
}

function getAxwareClass($clubClass, $sccaClass)
{
  if ($clubClass == "Stock")
    $axClass = "S";
  else if ($clubClass == "Sticky Stock")
    $axClass = "X";
  else if ($clubClass == "Street Prepared")
    $axClass = "SP";
  else if ($clubClass == "Prepared")
    $axClass = "M"; // somewhere 'prepared' became 'modified'. Not my decision. -sarah b 7/17/08
  else if ($clubClass == "Race")
    $axClass = "R";
  else
    $axClass = "?";
  
  if ($sccaClass == "Unknown")
    $sccaClass = "?";

  return $axClass.$sccaClass;
}
?>
