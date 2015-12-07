<?php
/**
 * @package OTA Hotel Management
 * @copyright e-Novate Pte Ltd 2012-2015
 * @file lookup.php
 * @brief lookup webpage called by OTA Hotel Management
 * see readme.txt for credits and references
 * 
 */
error_reporting(E_ALL & ~E_NOTICE);
include_once(dirname(__FILE__)."/login_check.inc.php");
include_once(dirname(__FILE__)."/queryfunctions.php");
include_once(dirname(__FILE__)."/functions.php");
include_once(dirname(__FILE__)."/dailyfunc.php");
include_once(dirname(__FILE__)."/lang/lang_en.php");

$lang = get_language();
load_language($lang);
$logofile=Get_LogoFile();


access("lookup"); //check if user is allowed to access this page

if (isset($_POST['Submit'])){
	switch ($action) {
		case 'Add Transaction Details':
			$fv=new formValidator(); //from functions.php
			$fv->validateEmpty('item','Please enter item name.');
			if($fv->checkErrors()){
				// display errors
				echo "<div align=\"center\">";
				echo "<h2>".$_L['PR_formerr']."</h2>";
				echo $fv->displayErrors();
				echo "</div>";
			}
			else {
				}
			break;
		case 'Add Document':
			$fv=new formValidator(); //from functions.php
			$fv->validateEmpty('doc_code','Please enter document code.');
			$fv->validateEmpty('doc_type','Please enter document type.');			
			if($fv->checkErrors()){
				// display errors
				echo "<div align=\"center\">";
				echo "<h2>".$_L['PR_formerr']."</h2>";
				echo $fv->displayErrors();
				echo "</div>";
			}
			else {
			}
			break;
		case 'Add Transaction Type':
			$fv=new formValidator(); //from functions.php
			$fv->validateEmpty('trans_code','Please enter transaction code.');
			$fv->validateEmpty('trans_type','Please enter transaction type.');			
			if($fv->checkErrors()){
				// display errors
				echo "<div align=\"center\">";
				echo "<h2>".$_L['PR_formerr']."</h2>";
				echo $fv->displayErrors();
				echo "</div>";
			}
			else {
			}
			break;
		case 'Add Payment Mode':
			$fv=new formValidator(); //from functions.php
			$fv->validateEmpty('payment_option','Please enter payment option.');
			if($fv->checkErrors()){
				// display errors
				echo "<div align=\"center\">";
				echo "<h2>".$_L['PR_formerr']."</h2>";
				echo $fv->displayErrors();
				echo "</div>";
			}
			else {
			}
			break;							
		case 'Find':
			break;
	}
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link href="css/new.css" rel="stylesheet" type="text/css">
<title><?php echo $_L['MAIN_Title'];?></title>

<script type="text/javascript">
<!--

//-->	 
</script>
<script language="JavaScript" src="js/highlight.js" type="text/javascript"></script>
	<SCRIPT type="text/javascript" src="js/dhtmlgoodies_calendar.js"></script>
	<SCRIPT type="text/javascript" src="js/datefuncs.js"></script>
</head>

<body>
<form action="lookup.php" method="post" enctype="multipart/form-data">
<table width="100%"  border="0" cellpadding="1" align="center" bgcolor="#66CCCC">
  <tr valign="top">
          <td class="c3" width="15%">
            <table width="100%" border="0" cellpadding="1">
              <tr>
                <td class="c2" width="15%">
                  <table class="c1" cellspacing="0" cellpadding="0"
                  width="100%" align="left">
                    <tr>
                      <td width="110" align="center">
                        <a href="index.php"><img src="<?php echo $logofile; ?>" width="70" height="74" border="0" /><br /> <?php echo $_L['MAIN_Home'];?></a>
                      </td>
                    </tr>
                    <tr>
                      <td>
                        &nbsp;
                      </td>
                    </tr>
                    <tr>
                      <td align="center">
                        <?php signon();?>
                      </td>
                    </tr>
					  <tr>
						<td>&nbsp;</td>
					  </tr>
					  <tr>
						<td align="center">
							<?php shift_times(); ?>
						</td>
					  </tr>
                  </table>
                </td>
              </tr><?php require_once("menu_header.php");?>
            </table>
          </td>
    
    
    <td bgcolor="#66CCCC"><table  bgcolor="#66CCCC" width="100%"  border="0" cellpadding="1">
      <tr>
        <td align="center" onclick="" style="cursor:pointer">Details</td>
		<td align="center" onclick="" style="cursor:pointer">Document Types</td>
		<td align="center" onclick="" style="cursor:pointer">Transaction Types</td>
		<td align="center" onclick="" style="cursor:pointer">Payment Modes</td>
		<td align="center" onclick="" style="cursor:pointer">Room Types</td>
      </tr>
      <tr>
        <td valign="top" colspan="5"><div id="maincontent"></div></td>
      </tr>
    </table></td>
  </tr>
  <tr>
	<td colspan=2>
	<table> <tr>
   <?php print_footer(); ?>
   </tr></table>
   </td>
  </tr>
   </table>
</form>
</body>
</html>
