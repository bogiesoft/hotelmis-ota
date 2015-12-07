<?php
/**
 * @package OTA Hotel Management
 * @copyright e-Novate Pte Ltd 2012-2015
 * @file initialsetup.php
 * @brief Hotel Management System Initial setup page called by OTA Hotel Management Installer
 * see readme.txt for credits and references
 * @addtogroup CODE_MANAGEMENT
 * @{
 * @defgroup ADMIN_MANAGEMENT Hotel setup and management page
 * @{
 */
error_reporting(0);
include_once(dirname(__FILE__).'/../functions.php');
include_once(dirname(__FILE__)."/../lang/lang_en.php");

$logofile = Get_LogoFile();
$showPost = 0;
if ($_POST['submit']){
	$action=$_POST['submit'];
	switch ($action) {
		case $_L['RM_addroom']:
			$fv=new formValidator(); //from functions.php
			$fv->validateEmpty('ROOMTYPE',$_L['RMT_normtyp']);
			$fv->validateEmpty('ROOMCOUNT',"Please enter the number of rooms");
			$fv->validateNumber('ROOMCOUNT',"Please enter valid number of rooms");
			$fv->validateEmpty('ROOMCOST',"Please enter the pricing for the room",1,6);
			$fv->validateEmpty('CURRENCY',$_L['CUR_currency_error2'],3,3);
			if($fv->checkErrors()){
				$showPost=1;
				// display errors
				$validationMsgs = "<div align=\"left\"><h2>".$_L['PR_formerr']."</h2>".$fv->displayErrors()."</div>";
			}
			else {
				$roomtype =trim( $_POST['ROOMTYPE']);
				$roomcount = trim($_POST['ROOMCOUNT']);
				$roomcost = trim($_POST['ROOMCOST']);
				$currency = trim($_POST['CURRENCY']);
				$ratecode = "DEF_".$roomtype;
				$date_started = date("d/m/Y");
				$date_stopped = date('d/m/Y',strtotime(date("d/m/Y", mktime()) . " + 365 day"));
				
				//Room No. File Name
				$roomno_file = "roomno.txt";
				$roomno = 100;
				if (file_exists ($roomno_file)){
					$roomno = file_get_contents($roomno_file);
				}
				//Default Room URL
				$defurl = "http://www.example.com/image.jpg";
				
				$rateid = modify_rate(0,$ratecode, "Default Rate", 15,"F",1,1,1,1, 1, 0,$currency,$date_started,$date_stopped);
				
				modify_rateitem(0, $rateid, 1, 1, 16773247, 1, 1, $roomcost, 0);
				$roomtypeid = modify_roomtype(0,$roomtype,$roomtype,$rateid,$defurl);
				for($i=0;$i<$roomcount;$i++){
					$add_roomno = $roomno + $i;
					$roomstatus = "V";
					$roomid = modify_room(0,$add_roomno,$roomtypeid,"",1,1,1,0,0,0,1,$roomstatus,"","", $rateid);
					if(isset($_POST['AMENITIES'])){
						$amenitylist = $_POST['AMENITIES'];
						//print_r($amenitylist);
						foreach($amenitylist as $amenity) {
							add_roomamenity($roomid, $amenity);
						}
					}
				}
				$roomno = $roomno + 100;
				$fh = fopen($roomno_file, 'w');
				fwrite($fh, $roomno);
				fclose($fh);
			}
			break;
		case $_L['BTN_next']:
			header("Location:index.php?action=thankyou");
			break;
	}
}
$roomamenities = array();
get_roomamenities($roomamenities);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link href="../css/new.css" rel="stylesheet" type="text/css" />
<title><?php echo $_L['MAIN_Title']; ?></title>
</head>
<body>
<form action="index.php?action=initialsetup" name="initialsetup" id="initialsetup" method="post" enctype="multipart/form-data">
      <table width="100%" border="0" cellpadding="1" align="center">
        <tr valign="top">           
    <td class="c4" align="center">
	<table  width="80%"  border="0" cellpadding="1">
      <tr>
        <td align="center">&nbsp;</td>
      </tr>
	  <tr>
        <td align="center">&nbsp;</td>
      </tr>
      <tr>
        <td valign="middle" align="center">
        <h4><br/>
		<?php echo $_L['MAIN_Title']; ?><br/>Setup Wizard</h4> </td>
      </tr>
      <tr>
  		<td><?php echo $validationMsgs?></td>
  	  </tr>
	<tr>
        <td  align="center"><div id="Requests">
        <table width="80%"  border="0" cellpadding="1">
		  <tr>
		    <td>
			<table width="100%"  border="0" cellpadding="1">	
		      <tr>
		        <td style="padding:5px;" align="left" colspan="3"><h2><?php echo $_L['RM_title']; ?></h2></td>
		      </tr>
			  <tr>
			    <td style="padding:5px;" align="left"><?php echo $_L['RM_type']; ?></td>
			    <td align="left"><input type="text" name="ROOMTYPE" id="ROOMTYPE" maxlength=20 value="<?php if($showPost==1 && isset($_POST['ROOMTYPE'])) echo $_POST['ROOMTYPE'];?>"/></td>
			    <td align="left"><?php echo $_L['RM_amenity']; ?></td>
			  </tr>	
			  <tr>
			    <td style="padding:5px;" align="left"><?php echo $_L['RM_numrooms']; ?></td>
			    <td align="left"><input type="text" name="ROOMCOUNT" id="ROOMCOUNT" maxlength=20 value="<?php if($showPost==1 && isset($_POST['ROOMCOUNT'])) echo $_POST['ROOMCOUNT'];?>"/></td>
			    <td align="left" rowspan="3"> 
			    <select id="AMENITIES" name="AMENITIES[]" multiple="multiple" size="4">
			    <?php
					foreach($roomamenities as $idx=>$val) {
					  if(!$allocated[$idx] ){
					  	  print "<option value='".$idx."'";
					  	  if ($showPost==1 && isset($_POST['AMENITIES'])){
						  	  foreach($_POST['AMENITIES'] as $pidx=>$pval) {
								  if ($pval ==$idx){ 
			            			print 'selected="selected"';
								  }
						  	  }
					  	  }
						  print "> ".$val."</option>\n";
					  }
					}
				?>
			    </select>
			    </td>
			  </tr>
			  <tr>
			    <td style="padding:5px;" align="left"><?php echo $_L['RTS_price']; ?></td>
			    <td align="left"><input type="text" name="ROOMCOST" id="ROOMCOST" maxlength=20 value="<?php if($showPost==1 && isset($_POST['ROOMCOST'])) echo $_POST['ROOMCOST'];?>"/></td>
			  </tr>
			  <tr>
			    <td style="padding:5px;" align="left"><?php echo $_L['CUR_currency']; ?></td>
			    <td align="left">
				<!-- changing currency text field into select field in the form -->
			    <select name=CURRENCY >
					<?php populate_select("countries","currency","currency",$currencycode,"currency <> ''"); ?>
				</select>
			    </td>
			  </tr>	
			  <tr>
			  	<td ></td>
			  	<td ></td>
			    <td style="padding:5px;" align="right"><input class="button" type="submit" name="submit" id="submit" value="<?php echo $_L['RM_addroom']; ?>" /></td>
			  </tr>	  
			</table>
		   </td>
		</tr>
		<tr>
		   <td style="padding:5px;"  align="right"><input class="button" type="submit" name="submit" id="submit" value="<?php echo $_L['BTN_next']; ?>" /></td>
		</tr>
		</table>
		</div>
		</td>
		<td class="c4" width="5%"> </td>
		</tr>
		  <tr>
		  <td colspan=3>
		  &nbsp;
		  </td>
		  </tr>
		  <tr>
				  <td colspan=3>
				  &nbsp;
		  </td>
		  </tr>
		  <tr>
				  <td colspan=3>
				  &nbsp;
		  </td>
		  </tr>
   </table>
   </td>
  </tr>
   </table>
</form>
</body>
</html>
<?php
/**
 * @}
 * @}
 */
 ?>