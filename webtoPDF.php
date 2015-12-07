<?php 

/**
 * redirect to another URL
 * @param url website to go to
 * 
 */ 
function goto_url($url) {
    echo '<script language = "javascript">';
    echo '  window.location.href = "'.$url.'"';
    echo '</script>';
}

/**
 * Retrieve the current website URL
 */
function curServerURL() {
	 $pageURL = 'http';
	 if (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on") {$pageURL .= "s";}
	 $pageURL .= "://";
	 if ($_SERVER["SERVER_PORT"] != "80" && $_SERVER["SERVER_PORT"] != "443") {
	  $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"];
	 } else {
	  $pageURL .= $_SERVER["SERVER_NAME"];
	 }
	 return $pageURL;
}

/**
 * Retrieve the current full URL and URI
 */
function curPageURL() {
	 $pageURL = 'http';
//	 if (!empty($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on") {$pageURL .= "s";}
	 $pageURL .= "://";
	 if ($_SERVER["SERVER_PORT"] != "80") {
	  $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
	 } else {
	  $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
	 }
	 return $pageURL;
}

/**
 *  return the uri path
 */
function uripath() {

	$thisdir = dirname(__FILE__);
	$web = $_SERVER['DOCUMENT_ROOT'];
	$thisdir = str_replace("\\",'/',$thisdir);
	echo $web."<br/>";
	echo $thisdir."<br/>";
	$res = str_replace($web, '',$thisdir);
	
	return $res;

}

define("SITEUSER","christene");
define("SITEPASS","Default1");
error_reporting(E_ALL);
ini_set("display_errors","1");
@set_time_limit(10000);


$roomid=0;
$roomtypeid=0;
$ratesid=0;
$resid=0;
if(isset($_GET['rid'])) {
		$resid=$_GET['rid'];
		$tag = "rid";
}elseif(isset($_GET['id'])) {
		$resid=$_GET['id'];
		$tag = "id";
}
if(isset($_GET['bid']) && !$resid) {
		$resid=$_GET['bid'];
		$tag = "bid";
}

if(isset($_GET['in'])) $checkin=urldecode($_GET['in']);
if(isset($_GET['out'])) $checkout=urldecode($_GET['out']);
if(isset($_GET['room'])) $roomid=$_GET['room'];
if(isset($_GET['roomtype'])) $roomtypeid=$_GET['roomtype'];
if(isset($_GET['rate'])) $ratesid=$_GET['rate'];
if(isset($_GET['guest'])) $guestid=$_GET['guest'];
if(isset($_GET['TMPL'])) $tmpl = $_GET['TMPL'];

echo "Resid ".$resid."<br/>";
if(!$resid) return;

$outputfile = dirname(__FILE__)."/TMP/".$tag.$resid.".pdf";
//$outputfile = basename($outputfile, ".pdf"); 
print "PDF ".$outputfile."<br/>\n";
print "template ".$tmpl."<br/>\n";

// Build the URL for the page to print.

$input = uripath()."/".$tmpl.'?PR=1&'.$tag.'='.$resid;
//if($checkin) $input .='&in='.$checkin;
//if($checkout) $input .= '&out='.$checkout;
if($roomid) $input .= '&room='.$roomid;
if($roomtypeid) $input .= '&roomtype='.$roomtypeid;
if($ratesid) $input .= '&rate='.$ratesid;
if(isset($guestid) && $guestid) $input .= '&guest='.$guestid;

print "<br/>input ".$input."<br/>\n";
$url = curServerURL().$input;
print "<br/>url ".$url."<br/>\n";
print "<br/>output ".$outputfile."<br/>\n";
print "Current Page ".curPageURL()."<br/>\n";
webtoMPDF($url, $outputfile, SITEUSER, SITEPASS);
goto_url(getenv("HTTP_REFERER"));
/**
 * This function generates a PDF file to the reports directory 
 * using the GET fields - ID, BY, TYPE and TMPL
 * file is output to the local reports directory
 * This version uses the MPDF library
 */
/**
 * web MPDF57 html to pdf
 * @param url [in] The URL of the page to convert
 * @param outputfile [in] Target device
 * @param username [in] website username for secured websites
 * @param password [in] website password for secured websites
 */
function webtoMPDF($url, $outputfile,$username, $password) {
	include_once(dirname(__FILE__)."/MPDF57/mpdf.php");
	print "PDF ".$outputfile."<br>\n";
	$tmpl = $_GET['TMPL'];
	// create a new cURL resource
	$ch = curl_init();

	// set URL and other appropriate options
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	if($username) {
		curl_setopt($ch,CURLOPT_USERPWD,"$username:$password");
	}
	// grab URL and pass it to the browser
	$html = curl_exec($ch);
	// close cURL resource, and free up system resources
	curl_close($ch);
	// ob_end_clean();
	// echo $html;

	$mpdf=new mPDF(); 
	$mpdf->WriteHTML($html);
	$mpdf->Output($outputfile,'F');

}


?>
