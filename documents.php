<?php
session_start();
/**
 * @package OTA Hotel Management
 * @copyright e-Novate Pte Ltd 2012-2015
 * @file documents.php
 * @brief document setup webpage called by OTA Hotel Management
 * see readme.txt for credits and references
 * 
 * @addtogroup CODE_MANAGEMENT
 * @{
 * @addtogroup ADMIN_MANAGEMENT 
 * @{
 * This documentation is for code maintenance, not a user guide.
 */
//error_reporting(E_ALL & ~E_NOTICE);
include_once(dirname(__FILE__)."/login_check.inc.php");
include_once(dirname(__FILE__)."/queryfunctions.php");
include_once(dirname(__FILE__)."/functions.php");
include_once(dirname(__FILE__)."/lang/lang_en.php");

$lang = get_language();
load_language($lang);
access("admin"); //check if user is allowed to access this page
$logofile = Get_LogoFile();


if (isset($_GET["search"])){
	find($_GET["search"]);
}	

if (isset($_POST['Submit']) && $_POST['tab']=='documents'){
	$action=$_POST['Submit'];
	switch ($action) {
		case $_L['BTN_save']:
				modify_documents($_POST['propertyno'], $_POST['invoiceno'],$_POST['receiptno'],$_POST['voucherno']);
			break;
		case $_L['BTN_delete']:
			break;
		case $_L['BTN_list']:
			break;
		case $_L['BTN_search']:
			//check if user is searching using name, payrollno, national id number or other fields
			//			find($_POST["search"]);
			break;
	}

}
if(!$propertyno) $propertyno = 1;

$items = array();
$noitems = get_documents($propertyno, $invoiceno, $receiptno, $voucherno);

if(!$invoiceno && $_POST['invoiceno']) {
	$invoiceno = $_POST['invoiceno'];
}
if(!$receiptno && $_POST['receiptno']) {
	$receiptno = $_POST['receiptno'];
}
if(!$voucherno && $_POST['voucherno']) {
	$voucherno = $_POST['voucherno'];
}



?>
<!--<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">-->
<!--<html xmlns="http://www.w3.org/1999/xhtml">-->
<!--<head>-->
<!--<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />-->
<!--<link href="css/new.css" rel="stylesheet" type="text/css">-->
<!--<link href="css/styles2.css" rel="stylesheet" type="text/css">-->
<!--</head>-->
<!--<body class="tdbgcl">-->
<form action="<?php $_SERVER['REQUEST_URI']?>" method="post" enctype="multipart/form-data">
      <table class="tdbgcl" width="100%" border="0" cellpadding="1" align="center">
        <tr valign="top">
    
    <td width="85%" class="c4">
	<table class="tdbgcl" width="100%"  border="0" cellpadding="1">
      <tr>
        <td align="center"><input type="hidden" name="tab" id="tab" value="<?php echo "documents"; ?>"/>
        <input type="hidden" name="activeTab" id="activeTab" value="<?php echo $tabvar;?>"/></td>
      </tr>
	  <tr><td>&nbsp;</td></tr>      
      <tr>
        <td>
		&nbsp;
		</td>
      </tr>
      <tr>
        <td><div id="Requests">
          <table width="80%" class=c1  border="0" cellpadding="1">
		<?php
		print "<tr bgcolor=\"#3593DE\" align='left'><th>".$_L['ADM_propertyno']."</th><th>".$_L['ADM_invoiceno']."</th><th>".$_L['ADM_receiptno']."</th><th>".$_L['ADM_voucherno']."</th></tr>";
		print "<td bgcolor=\"#CCCCCC\"> <input type=text name='propertyno' value='".$propertyno."' readonly size=10 /> </td>\n";
		print "<td bgcolor=\"#CCCCCC\"> <input type=text name='invoiceno' value='".$invoiceno."' size=10 maxlength=15 /></td>\n";
		print "<td bgcolor=\"#CCCCCC\"> <input type=text name='receiptno' value='".$receiptno."' size=10 maxlength=15 /></td>\n";
		print "<td bgcolor=\"#CCCCCC\"> <input type=text name='voucherno' value='".$voucherno."' size=10 maxlength=15 /></td>\n";
		print "</tr>";
		
		?>

	</table>
        </div>
        <div>
			<table height="283px">
				<tr><td></td></tr>			
			</table>        
        </div>
        <div>
			<table align="right">
				<tr><td>&nbsp;</td></tr>
				<tr><td colspan="2">
					<input class="button" type=submit value='<?php echo $_L['BTN_save']; ?>' name='Submit' />
					<input class="button" type=submit value='<?php echo $_L['BTN_refresh']; ?>' name='Submit' /> 					
			</table>           
        </div>        
        </td>		
      </tr>
	  <tr bgcolor="#66CCCC" >
        <td align="left"><div id="RequestDetails"></div>
		</td>
      </tr>
    </table></td>
  </tr>
</table>
</form>
<!--</body>-->
<!--</html>-->

<?php
/**
 * @}
 * @} 
 */
?>