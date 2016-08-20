<?php
require_once 'include/config.php';
?>
<br />

<noscript>
  <h1>Your browser does not support or has disabled Javascript.<br />Please enable it to use this site.</h1>
</noscript>

<?php	
echo "<form name=\"logonform\" action=\"".$http_Login."index.php\" method=\"post\">\n";
?>
<table class="transparent">
<tr><td>Username:</td><td>
<input type="text" name="username" maxlength="40">
</td></tr>
<tr><td>Password:</td><td>
<input type="password" name="password" maxlength="50">
</td><td><img src="images/classy-icons-set/png/32x32/lock.png" />SSL</td></tr>
<tr><td colspan="2" align="right">
<input type="submit" name="submit" value="Login">
</td></tr>
<tr><td></td>
<td>
<?php
echo "<a href=\"".$http_Login."forgot.php\">Forgot Password</a><br /><br />\n";
echo "<a href=\"".$http_Login."register.php\">Not Registered?</a>\n";
?>
</td>
<td></td></tr>
</table>
</form>

<script type="text/javascript" language="JavaScript">
document.forms['logonform'].elements['username'].focus();
</script>

<br />
This site has been tested to work on the following browsers<br />
<br />
<div>
<table class='transparent'>
<tr>
<td align='center'><a target="_blank" href='http://www.apple.com/safari/'><img src='images/safari-logo.png' alt='Safari' border='0' height='60' /></a></td>
<td align='center'><a target="_blank" href='http://www.opera.com'><img src='images/Opera_512x512.png' alt='Opera' border='0' height='75' /></a></td>
<td align='center'><a target="_blank" href='http://www.mozilla.com?from=sfx&amp;uid=223425&amp;t=561'><img src='images/firefox3.6_125x125.png' alt='Spread Firefox Affiliate Button' border='0' height='125' /></a></td>
<td align='center'><a target="_blank" href='http://www.google.com/chrome/'><img src='images/google-chrome-logo1.png' alt='Opera' border='0' height='75' /></a></td>
<td align='center'><a target="_blank" href='http://www.microsoft.com/windows/Internet-explorer/default.aspx'><img src='images/ie8-logo1.png' alt='IE8' border='0' height='60' /></a></td>
</tr>
<td align='center'>Safari 4</td>
<td align='center'>Opera 10</td>
<td align='center'>Firefox 3</td>
<td align='center'>Chrome 4</td>
<td align='center'>IE8</td>
</tr>
</table>
<br />
If you are using IE6, please visit this site: <a target="_blank" href="http://www.byebyeinternetexplorer.org">[link]</a>
</div>