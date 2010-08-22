<?  
  // Send header so page will always be recent 
  header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
  
  ob_start();
  
  $adminFile = "comm.php";
  
  if ($action=="") $action="ip";
    
  // Connect to SQL database  
  include("config.php");
  
?>

<HTML>
<HEAD>
  <TITLE>Earthdoom Admin</TITLE>
  <STYLE>
  <!--
    a:link    {color:#000080; text-decoration:none}
    a:active  {color:#000080; text-decoration:none}
    a:visited {color:#000080; text-decoration:none}
    a:hover   {color:#000080; text-decoration:underline}
	
	table     {font-size:12px}
	.mytable     {font-size:12px;border-style:solid;border-color:#E0E0E4;border-width:3}
	.mytableheader {background:#E0E0E4};
	body      {font-family:arial,verdana; font-size:12px}
	input     {font-family:arial,verdana; font-size:12px;border-style:solid;border-width:1px}
  
  -->
  </STYLE>

</HEAD>
<BODY>
<BIG><BIG><BIG><B>Empire Wars Admin</B></BIG></BIG></BIG><BR>
<SMALL>Made by <a href="mailto:flipsio@hotmail.com">Frank</a> for <a href="http://www.ewars.f2o.org">Empirewars</a>.</SMALL><BR><BR>
<?

  
  // *** LOGIN PROCEDURE ***
  
  // User wants to log in?  
  if ($action=="login") {
  
     
     if (isPassValid($pass)) {
       // Valid password	   
	   
	   // Log it
       $sQuery = "INSERT INTO pa_logging (text, stamp, type, ip) VALUES('admin.php login success', " . time() . ", 'admin_login', '" . $REMOTE_ADDR . "');";
       mysql_query($sQuery);	
	   
	   // Set password cookie to expire when session ends
	   setcookie("admin[pass]", $pass);
	   
	   // Redirect
	   header("Location: $adminFile");
	   
	   // Clean page buffer
	   ob_end_clean();
	 } else {
	   // Empty or wrong password
	   
	   if ($pass=="") {	   
	     // Log hit
         $sQuery = "INSERT INTO pa_logging (text, stamp, type, ip) VALUES('admin.php hit', " . time() . ", 'admin_hit', '" . $REMOTE_ADDR . "');";
	   } else {
	     // Log wrong password
         $sQuery = "INSERT INTO pa_logging (text, stamp, type, ip) VALUES('admin.php failed login (pass=$pass)', " . time() . ", 'admin_wrongpass', '" . $REMOTE_ADDR . "');";
	     echo ("<FONT COLOR=\"red\">Password incorrect! Your IP has been logged!<BR>Keep trying and get your account deleted!</FONT>");
	   }
       mysql_query($sQuery);	   
	   ?>
 	   <FORM ACTION="<? echo ($adminFile); ?>" METHOD="post">
	   <INPUT TYPE="hidden" NAME="action" VALUE="login">
	   Enter the password: <INPUT TYPE="password" NAME="pass"><BR>
	   <INPUT TYPE="submit" VALUE="    Login    ">
	   </FORM>
	   </BODY>
	   </HTML>
	   <?  
	   // Flush buffer content
	   ob_end_flush();
	 }
	 
     // Stop
	 die();
  }
  
  // *** LOGOUT PROCEDURE ****
  if ($action == "logout") {
    $pass = "";
	if (isset($admin) && isset($admin["pass"]) && isPassValid($admin["pass"]))
	  $pass = $admin["pass"];
	  
	// Let password cookie expire
    setcookie("admin[pass]", $pass, time()-3600);
	
	// Redirect to login page
	header("Location: $adminFile");
	
	// Clear page buffer
	ob_end_clean();
	
	// Stop
	die();  
  }
  
  // *** LOGIN INTO MAIN PAGE ***
    
  $bLoggedIn = FALSE;
  
  // Get logtype
  if ($logtype != "all" && $logtype != "admin")
    $logtype = "login";
  
  // Rows displayed per page
  $iRowsPerPage = 20;
  
  // Current page
  $iPage = (int)$page;  
  if ($iPage<0) $iPage = 0;
    
  // Check cookie & password
  if (isset($admin) && isset($admin["pass"]) && isPassValid($admin["pass"])) {
    $bLoggedIn = TRUE;
  }
  
  // Access denied, redirect to login page
  if (!$bLoggedIn) {
    // Remove cookie
    setcookie("admin[pass]", "", time()-3600);
	
	// Redirect to login page
	header("Location: $adminFile?action=login");
	
	// Clear buffer
	ob_end_clean();	
	
	// Stop
	die();
	
  }
  
  // *** LOGIN SUCCESSFUL ****
  
  // Set (new) cookie to expire when session ends
  setcookie("admin[pass]", $admin["pass"]);
  
  // Compose query to get user data
  $sUserQuery = "SELECT id as id, name, username, email, password, x, y, size, score, closed FROM pa_users";
  
  // IP filter
  $sIP = $ip;
  
  if ($action=="accountinfo") {
  
    /***************************
     **** ACCOUNT INFO (ALL)****
	 ***************************/
	
    // Get sorting info
    $sSorting = "id";
    if ($sortby != "")
      $sSorting = $sortby;
	  
	$sQuery = $sUserQuery . " ORDER BY $sSorting LIMIT ". ($page * $iRowsPerPage) . ", $iRowsPerPage";
	
	$hResult = mysql_query($sQuery);
  
  } else if ($action=="singleaccountinfo") {
  
    /***************************
     **** ACCOUNT INFO (SINGLE)****
	 ***************************/
	
    // Get sorting info
    $sSorting = "id";
    if ($sortby != "")
      $sSorting = $sortby;
	
	$id = (int)$id;
	  
	$sQuery = $sUserQuery . " WHERE id=$id ORDER BY $sSorting LIMIT ". ($page * $iRowsPerPage) . ", $iRowsPerPage";	
	$hResult = mysql_query($sQuery);
	
	$sQueryIPs = "SELECT DISTINCT ip, COUNT(id) AS Logins, MAX(stamp) AS Last_login FROM pa_logging WHERE author=$id GROUP BY ip ORDER BY ip";
	$hResultIPs = mysql_query($sQueryIPs);
  
  } else if ($action=="updateaccount") {
  
    /************************
     **** UDPATE ACCOUNT ****
	 ************************/
	
	ob_end_clean();
	 
    $closed = ($closed=="")?"0":"1";	
	$sQuery = "UPDATE pa_users SET x=$x, y=$y, CLOSED=$closed WHERE id=$id";
		
	//echo ("$sQuery<BR>");
	mysql_query($sQuery) or die("Error in query"); 	
	 
	?>
	<SCRIPT LANGUAGE="JavaScript">
	  location = '<? echo ("$adminFile?action=singleaccountinfo&id=$id"); ?>';
	</SCRIPT>
	<?
	die();
  } else if ($action=="deleteaccount") {
  
    /************************
     **** DELETE ACCOUNT ****
	 ************************/
	
	$sQuery = "DELETE FROM pa_users WHERE id=$id";
	mysql_query($sQuery);
	
	ob_end_clean();
	?>
	<SCRIPT LANGUAGE="JavaScript">
	  location = '<? echo ("$adminFile?action=accountinfo"); ?>';
	</SCRIPT>
	<?
	die();
  } else if ($action=="ip") { 
  
    /**************************
     **** MULTI IP TRACING ****
	 **************************/
	
    // Get sorting info
    $sSorting = "Nr_of_users";
    if ($sortby != "")
      $sSorting = $sortby;
		
	// Reverse the sorting if sorted on number of users
	if ($sSorting == "Nr_of_users")
	  $sSorting .= " DESC";
		
    $sQuery = "SELECT ip, count(distinct author) as Nr_of_users, max(stamp) as Last_login FROM pa_logging, pa_users WHERE type='login' AND pa_logging.author = pa_users.id GROUP BY ip HAVING count(distinct author)>1";
	$sQuery = $sQuery . " ORDER BY $sSorting LIMIT " . ($page * $iRowsPerPage) . ", $iRowsPerPage";
	
	$hResult = mysql_query($sQuery);
	
  } else if ($action=="users_per_ip") { 
  
    /**********************
     **** USERS PER IP ****
	 **********************/
	
    // Get sorting info
    $sSorting = "id";
    if ($sortby != "")
      $sSorting = $sortby;
		
    $sQuery = "SELECT pa_users.id AS id, pa_users.username, COUNT(pa_logging.id) as Logins, MAX(pa_logging.stamp) as Last_login FROM pa_logging, pa_users WHERE type='login' AND pa_logging.author = pa_users.id AND pa_logging.ip = '$ip' GROUP BY AUTHOR";
	$sQuery .= " ORDER BY $sSorting LIMIT " . ($page * $iRowsPerPage) . ", $iRowsPerPage";
	
	$hResult = mysql_query($sQuery);
  
  } else {  // ($action == "viewlog")
    /******************
     **** VIEW LOG ****
	 ******************/
  
    // Get sorting info
    $sSorting = "id";
    if ($sortby != "")
      $sSorting = $sortby;  
  	
    // **** Compose query ****
    $sWhere = "";
  
    // Include filter for login events
    if ($logtype == "login")
	  $sWhere .= "WHERE type = 'login'";
	else if ($logtype == "admin")
	  $sWhere .= "WHERE type LIKE 'admin%'";
	
    // Include filter to exclude ticker events
    if ($sWhere == "")
      $sWhere .= " WHERE type <> 'ticker'";
    //else 
      //$sWhere .= " AND type <> 'ticker'";
	
    if ($sIP != "") $sWhere .= " AND ip = '$sIP'";
  
    // Create query
    $sQuery = "SELECT id,stamp,type,text AS Account_or_Text,ip,author,toid FROM pa_logging $sWhere";
  
    // Add sorting order
    $sQuery .= " ORDER BY $sSorting";
  
    // Reverse sort if by id or stamp
    if ($sSorting == "id" || $sSorting == "stamp")
      $sQuery .= " DESC";  
	  
	// Paginate results
	$sQuery .= " LIMIT ". ($page * $iRowsPerPage) . ", $iRowsPerPage";
  
    //echo ("$sQuery<BR>");
	
    // Execute query in log table
    $hResult = mysql_query($sQuery) or die("Error in query, quitting");
  }

  // Print  
  ?>
  
  
    <INPUT TYPE="button" VALUE="     Reset     " onclick="location='<? echo ($adminFile); ?>';return false">
	<INPUT TYPE="button" VALUE="     Logout    " onclick="location='<? echo ($adminFile); ?>?action=logout';return false"><BR><BR>
	
	
    <TABLE CELLSPACING=0 CELLPADDING=3 CLASS="mytable">	
	<TR CLASS="mytableheader">
	<TD VALIGN=TOP>
	  <BIG><B>Multi IP Tracing</B></BIG><BR>
	</TD>
	<TD VALIGN=TOP>
	  <BIG><B>Log</B></BIG><BR>
	</TD>
	<TD VALIGN=TOP>
	  <BIG><B>Accounts</B></BIG><BR>
	</TD>
	<TD VALIGN=TOP>
	  <BIG><B>Account Info</B></BIG><BR>
	</TD>
	</TR>
	<TR>
	<TD VALIGN=BOTTOM WIDTH=150>
	  <TABLE BORDER=0 CELLSPACING=0 CELLPADDING=3 WIDTH=100% HEIGHT=110>
      <FORM ACTION="<? echo ($adminFile); ?>" METHOD="get">
	  <TR><TD VALIGN=TOP>
	  This feature automatically shows the IP's under which more than one user has logged in.
	  </TD></TR><TR><TD VALIGN=BOTTOM>	  
	  <INPUT TYPE="hidden" NAME="action" VALUE="ip">
	  <INPUT TYPE="hidden" NAME="logtype" VALUE="<? echo ($logtype); ?>">
	  <INPUT TYPE="hidden" NAME="ip" VALUE="<? echo ($sIP); ?>">
	  <INPUT TYPE="submit" VALUE="  Multi IP Tracing  "><BR>  
	  </TD></TR>
	  </FORM>
	  </TABLE>
	</TD>	
	<TD VALIGN=BOTTOM WIDTH=170>
	  <TABLE BORDER=0 CELLSPACING=0 CELLPADDING=3 WIDTH=100% HEIGHT=110>
      <FORM ACTION="<? echo ($adminFile); ?>" METHOD="get">
	  <TR><TD VALIGN=TOP>
	  Shows the log table contents.
	  </TD></TR><TR><TD VALIGN=BOTTOM>
	  <INPUT TYPE="hidden" NAME="action" VALUE="viewlog">
	  Event type: 
	  <SELECT NAME="logtype">
	    <OPTION VALUE="all" <? if ($logtype=="all") echo ("SELECTED"); ?>>All</OPTION>
	    <OPTION VALUE="login" <? if ($logtype=="login") echo ("SELECTED"); ?>>Login</OPTION>
	    <OPTION VALUE="admin" <? if ($logtype=="admin") echo ("SELECTED"); ?>>Admin</OPTION>
	  </SELECT><BR>
	  IP: <INPUT TYPE="text" NAME="ip" SIZE=15 VALUE="<? echo (stripslashes($ip)); ?>"><BR>
	  <INPUT TYPE="submit" VALUE="   Show Log   ">
	  </TD></TR>
	  </FORM>
	  </TABLE>
	</TD>
	<TD VALIGN=BOTTOM WIDTH=130>
	  <TABLE BORDER=0 CELLSPACING=0 CELLPADDING=3 WIDTH=100% HEIGHT=110>
      <FORM ACTION="<? echo ($adminFile); ?>" METHOD="get">
	  <TR><TD VALIGN=TOP>
	  Lists the accounts, and enables you to close or delete accounts.
	  </TD></TR><TR><TD VALIGN=BOTTOM>
	  <INPUT TYPE="hidden" NAME="action" VALUE="accountinfo">
	  <INPUT TYPE="hidden" NAME="logtype" VALUE="<? echo ($logtype); ?>">
	  <INPUT TYPE="hidden" NAME="ip" VALUE="<? echo ($sIP); ?>">
	  <INPUT TYPE="submit" VALUE="     Show accounts     "><BR>
	  </TD></TR>
	  </FORM>
	  </TABLE>
	</TD>
	<TD VALIGN=BOTTOM WIDTH=130>
	  <TABLE BORDER=0 CELLSPACING=0 CELLPADDING=3 WIDTH=100% HEIGHT=110>
      <FORM ACTION="<? echo ($adminFile); ?>" METHOD="get">
	  <TR><TD VALIGN=TOP>
	  View an account, and edit it.
	  </TD></TR><TR><TD VALIGN=BOTTOM>
	  <INPUT TYPE="hidden" NAME="action" VALUE="singleaccountinfo">
	  <INPUT TYPE="hidden" NAME="logtype" VALUE="<? echo ($logtype); ?>">
	  <INPUT TYPE="hidden" NAME="ip" VALUE="<? echo ($sIP); ?>">
	  Account ID: <INPUT TYPE="text"   NAME="id" SIZE=4 VALUE="<? echo ($id); ?>"><BR>
	  </TD></TR><TR><TD VALIGN=BOTTOM>
	  <INPUT TYPE="submit" VALUE="     Show account     "><BR>
	  </TD></TR>
	  </FORM>
	  </TABLE>
	</TD>
	</TR>
	</TABLE>  
	<BR>
  
  <?
  
  if ($action == "accountinfo") {
    echo ("<B><BIG>ACCOUNTS</BIG></B><BR><BR>");
  } else if ($action == "singleaccountinfo") {
    echo ("<B><BIG>ACCOUNT INFO</BIG></B><BR><BR>");
  } else if ($action == "ip") {
    ?><B><BIG>MULTI IP TRACING</BIG></B><BR><BR>
	This feature automatically shows the IP's under which <i>more than one user</i> has logged in.<BR>
	It only counts the non-deleted users.<BR>
	Click on the IP to see the accounts related to that IP.<BR><BR>
	<?
  } else if ($action == "users_per_ip") {
    ?><B><BIG>ACCOUNTS FROM IP <? echo ($ip); ?></BIG></B><BR><BR>
	This screen is showing all (non-deleted) accounts that originated from IP <? echo ($ip); ?>.<BR>
	Click an account to see its details.<BR>
	<?
	echo ("<A HREF=\"$adminFile?action=viewlog&logtype=login&ip=$ip\">Click here to see logins made from IP $ip</A><BR>");
	echo ("<A HREF=\"$adminFile?action=viewlog&logtype=all&ip=$ip\">Click here to see all events from IP $ip</A><BR><BR>");
  } else if ($action == "viewlog") {
    echo ("<B><BIG>LOG:</BIG>");
    if ($sIP != "") {
      echo (" Viewing $logtype entries of IP " . stripslashes($sIP)); // . " (" . gethostbyaddr($sIP) .")");
	}
    echo ("</B><BR><BR>");
  }  
    
  // Get single account info (if chosen)
  if ($action == "singleaccountinfo") {
    if (mysql_num_rows($hResult)>0) {
    $row = mysql_fetch_array($hResult);
    $bClosed = ($row["closed"]==1?TRUE:FALSE);
	?>
<table class="mytable" border=0 cellpadding=2 cellspacing=0>	  
  <tr class="mytableheader">
    <td colspan=2>
	 <big><b><? echo ("#" . $row["id"] . ": " . $row["username"]); ?></b></big>
	</td>
  </tr>
  <tr><td>
	<table cellpadding=2 cellspacing=0>
	  <tr>
	    <td width=100>ID</td>
		<td width=200><? echo ($row["id"]); ?></td>
	  </tr>
	  <tr>
	    <td>username</td>
		<td><? echo ($row["username"]); ?></td>
	  </tr>
	  <tr>
	    <td>Password</td>
		<td><? echo ($row["password"]); ?></td>
	  </tr>
	  <tr>
	    <td>Name</td>
		<td><? echo ($row["name"]); ?></td>
	  </tr>
	  <tr>
	    <td>Email</td>
		<td><a href="<? echo ($row["email"]); ?>"><? echo ($row["email"]); ?></a></td>
	  </tr>
	  <tr>
	    <td>Size</td>
		<td><? echo ($row["size"]); ?></td>
	  </tr>
	  <tr>
	    <td>Score</td>
		<td><? echo ($row["score"]); ?></td>
	  </tr>
	  <FORM ACTION="<? echo ($adminFile); ?>" METHOD="post">
	  <INPUT TYPE="hidden" NAME="action" VALUE="updateaccount">
	  <INPUT TYPE="hidden" NAME="id"     VALUE="<? echo ($id); ?>">
	  <tr>
	    <td valign=top>Location</td>
		<td valign=top>
		  x <INPUT TYPE="text" NAME="x" VALUE="<? echo ($row["x"]); ?>" SIZE=2><BR>
		  y <INPUT TYPE="text" NAME="y" VALUE="<? echo ($row["y"]); ?>" SIZE=2>
		</td>
	  </tr>
	  <tr>
	    <td>Closed</td>
		<td><INPUT TYPE="checkbox" NAME="closed" <? if ($bClosed) echo ("CHECKED"); ?>></td>
	  </tr>
	  <tr>
	    <td>&nbsp;</td>
		<td><INPUT TYPE="submit" VALUE="    Save data...   "></td>		
		</td>
	  </tr>
	  </FORM>
	</table>
  </td>
  <td valign=top>
    <BIG><B>IP addresses used</B></BIG>
    <?
	  PrintRecordset($hResultIPs, false, false, false);	
    ?>

  </td>
  </tr>
</table>
  <FORM ACTION="<? echo ($adminFile); ?>" METHOD="post" ONSUBMIT="return confirm('Are you sure you want to DELETE this account?');">	
	<INPUT TYPE="hidden" NAME="action" VALUE="deleteaccount">
    <INPUT TYPE="hidden" NAME="ip" VALUE="<? echo ($ip); ?>">
    <INPUT TYPE="hidden" NAME="id" VALUE="<? echo ($id); ?>">
    <INPUT TYPE="submit" VALUE="  Delete account ">	
  </FORM>
	
	<?
	} else {
      echo ("User #$id not found.<BR>");
	}
  }
  
  if ($action !== "singleaccountinfo")
    // Print the table
    $ok = PrintRecordset($hResult, $sSorting);  
  
  // Free record sets
  mysql_free_result($hResult);
  
  if ($action=="viewlog") {
  ?>
    <BR>
	To effectively multi-hunt, check the 'Only show login events' checkbox above, and sort by IP.<BR>
	When you see multiple 'text' (=Account name) entries next to an IP, this is reason for suspicion!<BR>
  <?  
  }
  
  /*
  $sQuery = "SELECT ip, count(author) as Nr_of_users, max(stamp) as Last_login FROM pa_logging WHERE type='login' GROUP BY ip HAVING count(author)>1 ORDER BY Nr_of_users DESC";
  $hResult = mysql_query($sQuery);
  
  PrintRecordset($hResult, "");
  */
  
  
  
  
  // ********** MISC. FUNCTIONS  *************
  
  function PrintRecordset($hResult, $sSorting, $allowsort = true, $showPrevNext = true) {
    global $adminFile, $action, $logtype, $ip, $userid, $iPage, $iRowsPerPage;
	
	// Check for invalid recordset
	if ($hResult === FALSE) {
	  echo ("Invalid recordset.<BR>");
	  return FALSE;
	}
  
    // Check for empty recordset
    if (mysql_num_rows($hResult) == 0) {
	   echo ("No records to show...<BR>");
	   return FALSE;	
	}
	
	// Move to start of recordset
	mysql_data_seek($hResult, 0);
	
	$count = mysql_num_rows($hResult);
	
	if ($showPrevNext) {
      // Print previous link
	  if ($iPage > 0)
	    echo ("<a href=\"$adminFile?action=$action&userid=$userid&sortby=$fieldname&logtype=$logtype&sortby=$sSorting&ip=$ip&page=" . ($iPage-1). "\">&lt;&lt; Previous</a> | ");	  
	  else 
	    echo ("&lt;&lt; Previous | ");	  
      // Print page number
	  echo ("Page " . ($iPage+1));
	  // Print next link
	  if ($iRowsPerPage == $count)
        echo (" | <a href=\"$adminFile?action=$action&userid=$userid&sortby=$fieldname&logtype=$logtype&sortby=$sSorting&ip=$ip&page=" . ($iPage+1). "\">Next &gt;&gt;</a>");
	  else
        echo (" | Next &gt;&gt;");
	}
	
    ?><TABLE CLASS="mytable" CELLSPACING=0 CELLPADDING=3 BORDER=0><?
	
	// HACK: Is the table displaying account data?
	$bIsAccountData = (substr($action, 0, 7) == "account");
	
	// HACK: Is the table displaying the users per ip
	$bIsUsersPerIP = ($action == "users_per_ip");
		
	// Print header
    echo ("<TR class=\"mytableheader\">");
	for ($i=0; $i<mysql_num_fields($hResult); $i++) {
	  $fieldname = mysql_field_name($hResult, $i);
	  if ($sSorting == $fieldname || !$allowsort)
	    echo ("<TD><B>$fieldname</B></TD>");
	  else	  
	    echo ("<TD><A HREF=\"$adminFile?action=$action&userid=$userid&sortby=$fieldname&logtype=$logtype&ip=$ip&\">$fieldname</TD>");
	}
	if ($bIsAccountData)
	  echo ("<TD>Edit/Delete Account</TD>");
	else if ($bIsUsersPerIP)
	  echo ("<TD>&nbsp;</TD>");
	echo ("</TR>");	
  
    // Print rows
	while ($row = mysql_fetch_array($hResult)) {
      echo ("<TR>");
	  // Iterate over the fields
	  for ($i=0; $i<mysql_num_fields($hResult); $i++) {
	    // Get cell content
	    $str = $row[$i];
		// Hack; convert any content from fields named stamp to a readable date format
		if (mysql_field_name($hResult, $i) == "stamp" || mysql_field_name($hResult, $i) == "Last_login")
		  $str = datetime2str($row[$i]);
		// Strip HTML tags
		$str = ereg_replace("<", "&lt;", $str);
		$str = ereg_replace(">", "&gt;", $str);
		
		// Print cell content
		echo ("<TD><A NAME=\"" . $row["id"] . "\">");
		if (mysql_field_name($hResult, $i) == "ip") // Convert IP column cells to links
		  echo ("<A HREF=\"$adminFile?action=users_per_ip&logtype=login&ip=$str\">$str</A>");
		else
	      echo ($str);
		echo ("</TD>");
	  }
	  
      if ($bIsAccountData) {
	    $iUserID = $row["id"];
	    $bClosed = $row["closed"];
		echo ("<TD>");
        PrintCloseDeleteButtons($iUserID, $bClosed);		
		echo ("</TD>");
      } else if ($bIsUsersPerIP)	  
        echo ("<TD><A HREF=\"$adminFile?action=singleaccountinfo&logtype=$logtype&ip=$ip&id=" . $row["id"]. "\">See account info</a></TD>");
	  echo ("</TR>");	
	}
	
	echo ("</TABLE>");
  
  }
  
  function PrintCloseDeleteButtons($userid, $bClosed) {
  
    global $adminFile, $bShowLogin, $ip, $sortby;
	
    ?>
	<TABLE BORDER=0 CELLSPACING=3 CELLPADDING=0 STYLE="border-width:0">
	<TR>	
	  <FORM ACTION="<? echo ($adminFile); ?>" METHOD="get">	  
	  <TD>
	   <INPUT TYPE="hidden" NAME="action" VALUE="singleaccountinfo">
	   <INPUT TYPE="hidden" NAME="id" VALUE="<? echo ($userid); ?>">
	   <INPUT TYPE="hidden" NAME="ip" VALUE="<? echo ($ip); ?>">
	   <INPUT TYPE="submit" VALUE="  Edit...  ">	
	  </TD>
	  </FORM>
	  
	<FORM ACTION="<? echo ($adminFile); ?>" METHOD="post" ONSUBMIT="return confirm('Are you sure you want to DELETE this account?');">	
    <TD> 
	<INPUT TYPE="hidden" NAME="action" VALUE="deleteaccount">
	<INPUT TYPE="hidden" NAME="showlogin" VALUE="<? if ($bShowLogin) echo ("on"); ?>">
	<INPUT TYPE="hidden" NAME="ip" VALUE="<? echo ($ip); ?>">
	<INPUT TYPE="hidden" NAME="userid" VALUE="<? echo ($userid); ?>">
	<INPUT TYPE="hidden" NAME="sortby" VALUE="<? echo ($sortby); ?>">
	<INPUT TYPE="submit" VALUE="  Delete  ">	
	</TD>
	</FORM>
	</TR>
	</TABLE>
	<?
  
  }

  function datetime2str($d) {
    $date = getdate($d);
    $minutes = $date["minutes"];
    if ($minutes<10) $minutes = "0" . $minutes;
    return $date["mday"] . "/" . $date["mon"] . "/" . $date["year"] . ", " . $date["hours"] . ":$minutes";
  }
  
  function isPassValid($pass) {
    return (md5($pass) == "9c1ad00a16a7c67e2727b471ac969e96");	
  }

  

?>
</BODY>
</HTML>
<?
  ob_end_flush();
?>
