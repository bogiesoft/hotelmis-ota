<?php
session_start();
/**
 * @package OTA Hotel Management
 * @copyright e-Novate Pte Ltd 2012-2015
 * @file rates.php
 * @brief rates webpage called by OTA Hotel Management
 * see readme.txt for credits and references
 * 
 * @addtogroup CODE_MANAGEMENT
 * @{
 * @defgroup RATE_MANAGEMENT Rate setup and management page
 * @{
 * This documentation is for code maintenance, not a user guide.
 */
//error_reporting(E_ALL & ~E_NOTICE);
include_once(dirname(__FILE__)."/login_check.inc.php");
include_once(dirname(__FILE__)."/queryfunctions.php");
include_once(dirname(__FILE__)."/functions.php");
include_once(dirname(__FILE__)."/lang/lang_en.php");

if(is_ebridgeCustomer()){
	include_once(dirname(__FILE__)."/OTA/advancedFeatures/rates.php");
	return;
}


$logofile=Get_LogoFile();
access("rates"); //check if user is allowed to access this page
$lang = get_language();
load_language($lang);
$ratesid=0;
$allowChangeRate=1;

if(isset($_GET['id'])) $ratesid = $_GET['id'];
if(isset($_POST['ratesid'])) $ratesid = $_POST['ratesid'];

if ($ratesid){
	$temprateType = get_ratetype_by_rateID($ratesid);
	if($temprateType==DEFAULTRATE && $_POST['ratetype'] != DEFAULTRATE){		
		$isDefaultRateUsed = isRateUsed($ratesid);			
		if($isDefaultRateUsed==1){
			$allowChangeRate=0;			
		}
	}
}

if (isset($_POST['Submit'])){
	$action=$_POST['Submit'];
	switch ($action) {
		case $_L['BTN_update']:
		case $_L['RTS_addrate']:
			$fv=new formValidator(); //from functions.php
			$fv->validateEmpty('code',$_L['RTS_noratecode_err']);
//			$fv->validateEmpty('description','Please enter rate description.');
			$fv->validateEmpty('occupancy',$_L['RTS_nooccup_err']);
			$fv->validateEmpty('date_started',$_L['RTS_nostrtdate_err']);
			$fv->validateEmpty('date_stopped',$_L['RTS_noenddate_err']);
			$fv->validateEmpty('currencycode',$_L['RTS_nocurcode_err']);
			$fv->validateEmpty('minpax',$_L['RTS_nominprson_err']);
			$fv->validateNumber('minpax',$_L['RTS_minprson_nmric_err']);
			$fv->validateEmpty('maxpax',$_L['RTS_nomaxprson_err']);
			$fv->validateNumber('maxpax',$_L['RTS_maxprson_nmric_err']);
			$fv->validateEmpty('minstay',$_L['RTS_nominnghts_err']);
			$fv->validateNumber('minstay',$_L['RTS_minstay_nmric_err']);
			$fv->validateEmpty('maxstay',$_L['RTS_nomaxstay_err']);
			$fv->validateNumber('maxstay',$_L['RTS_maxstay_nmric_err']);
			$fv->validateEmpty('minbook',$_L['RTS_nominadv_err']);
			$fv->validateNumber('minbook',$_L['RTS_minadv_nmric_err']);
			if(!$allowChangeRate){				
				$fv->addErrormsg($_L['RTS_Err_changeRateType']);
			}
			if($fv->checkErrors()){
				// display errors
				$validationMsgs = "<div align=\"left\"><h2>".$_L['PR_formerr']."</h2>".$fv->displayErrors()."</div>";
			} else {
				$ratesid=modify_rate($_POST['ratesid'],$_POST['code'],$_POST['description'],
					$_POST['bookingsrc'],$_POST['occupancy'],$_POST['ratetype'],$_POST['minpax'],$_POST['maxpax'],$_POST['minstay'], $_POST['maxstay'], 
					$_POST['minbook'],$_POST['currencycode'],$_POST['date_started'],$_POST['date_stopped']);

				if($_POST['ratetype'] == CUSTOMERRATE && $_POST['customerid']) {
					delete_rateroomtypes($ratesid);
					add_roomratetypes($ratesid, CUSTOMERRATE, $_POST['customerid']);
				}
				$syncrate = 0;
				if($_POST['ratetype'] == AGENTRATE ) {
					$agentid = isset($_POST['agentid'])?$_POST['agentid']:0;
					delete_rateroomtypes($ratesid);
					add_roomratetypes($ratesid, AGENTRATE, $agentid);
				}
				if($_POST['ratetype'] == PROMORATE || $_POST['ratetype'] == CUSTOMERRATE ||
				   $_POST['ratetype'] == AGENTRATE ) {
				    if($_POST['ratetype'] == PROMORATE) {
						delete_rateroomtypes($ratesid);
					}
					get_roomslist($rms, '','',0 );
					
					foreach($rms as $idx=>$val) {
						$rmidx = "RM".$idx;
						
						if (isset($_POST[$rmidx])){
//							echo "rm here ".$idx."<br/>";
							add_roomratetypes($ratesid, ROOMRATE, $idx);
							//echo $_POST['syncrate'];
						}
					}
					get_roomtypelist($rt);
					foreach($rt as $idx=>$val) {
						$rtidx = "RTP".$idx;
						if (isset($_POST[$rtidx])){
							add_roomratetypes($ratesid, ROOMTYPERATE, $idx);
						}
					}
				}
			}
			
			
			// @todo 
			// If e-Bridge customer and syncrate to host, then run the sync rate function
			// Add the result into the Success or fail statement.
			// loop over all roomtypes.
			if(!ratesid){
				echo "<div align=\"center\"><h1>".$_L['RTS_record_err']."</h1></div>";
			}
			else{
				echo "<div align=\"center\"><h1>".$_L['RTS_record_succs']."</h1></div>";
			}
			break;
		case $_L['RTS_delitem']:
			if($_POST['ratesid'] && $_POST['delitem']) delete_rateitem($_POST['ratesid'], $_POST['delitem']);
			break;
		case $_L['RTS_additem']:
			$jan = (empty($_POST['january'])) ? 0 : $_POST['january'] ;
			$feb = (empty($_POST['february'])) ? 0 : $_POST['february'] ;
			$mar = (empty($_POST['march'])) ? 0 : $_POST['march'] ;
			$apr = (empty($_POST['april'])) ? 0 : $_POST['april'] ;
			$may = (empty($_POST['may'])) ? 0 : $_POST['may'] ;
			$jun = (empty($_POST['june'])) ? 0 : $_POST['june'] ;
			$jul = (empty($_POST['july'])) ? 0 : $_POST['july'] ;
			$aug = (empty($_POST['august'])) ? 0 : $_POST['august'] ;
			$sep = (empty($_POST['september'])) ? 0 : $_POST['september'] ;
			$oct = (empty($_POST['october'])) ? 0 : $_POST['october'] ;
			$nov = (empty($_POST['november'])) ? 0 : $_POST['november'] ;
			$dec = (empty($_POST['december'])) ? 0 : $_POST['december'] ;
			$mon = (empty($_POST['monday'])) ? 0 : $_POST['monday'] ;
			$tue = (empty($_POST['tuesday'])) ? 0 : $_POST['tuesday'] ;
			$wed = (empty($_POST['wednesday'])) ? 0 : $_POST['wednesday'] ;
			$thu = (empty($_POST['thursday'])) ? 0 : $_POST['thursday'] ;
			$fri = (empty($_POST['friday'])) ? 0 : $_POST['friday'] ;
			$sat = (empty($_POST['saturday'])) ? 0 : $_POST['saturday'] ;
			$sun = (empty($_POST['sunday'])) ? 0 : $_POST['sunday'] ;
			$hol = (empty($_POST['holiday'])) ? 0 : $_POST['holiday'] ;
			
			
			$months = $jan|$feb|$mar|$apr|$may|$jun|$jul|$aug|$sep|$oct|$nov|$dec;
			if($months == 0) $months = HOTEL_YEAR;
			$days = $mon+$tue+$wed+$thu+$fri+$sat+$sun;
			if($days == 0) $days = HOTEL_WEEK; 
			$period = ($days+$months) | $hol;
			$maxcount = $_POST['maxcount'];
			if(!$maxcount) $maxcount = 0;
			$service = $_POST['service'];
			if(!$service) $service=0;
			$tax = $_POST['tax'];
			if(!$tax) $tax = 0;
			$price = $_POST['price'];
			if(!$price) $price = 0;
			modify_rateitem(0,$_POST['ratesid'],$_POST['itemcode'],$_POST['dis'],$period,$service,$tax,$price, $maxcount);	
			break;
		case $_L['RTS_listrates']:
			echo "List";
			break;
		case $_L['BTN_search']:
			//check if user is searching using rateid or ratecode
			if($_POST['optFind'] == "ID") {
				$ratesid = $_POST['search'];
			} else if($_POST['optFind'] == "Name") {
				$ratesid = get_rateid_bycode($_POST['search']);
			}
			break;
	}
}
if(isset($_POST['ratesid']) && !$ratesid) $ratesid=$_POST['ratesid'];
if(isset($_POST['code'])) $code=$_POST['code'];
if(isset($_POST['description'])) $desc=$_POST['description'];
if(isset($_POST['bookingsrc'])) $src=$_POST['bookingsrc'];
if(isset($_POST['ratetype'])) $ratetype=$_POST['ratetype'];
if(isset($_POST['minpax'])) $minpax=$_POST['minpax'];
if(isset($_POST['maxpax'])) $maxpax=$_POST['maxpax'];
if(isset($_POST['minstay'])) $minstay=$_POST['minstay'];
if(isset($_POST['maxstay'])) $maxstay=$_POST['maxstay'];
if(isset($_POST['currencycode'])) $currencycode=$_POST['currencycode'];
if(isset($_POST['date_started'])) $date_started=$_POST['date_started'];
if(isset($_POST['date_stopped'])) $date_stopped=$_POST['date_stopped'];
if(isset($_POST['occupancy'])) $date_stopped=$_POST['occupancy'];
if(isset($_POST['minbook'])) $minbook = $_POST['minbook'];
$rateitems = array();
$rcount = 0;
$rooms = array();
$roomtypes = array();
$syncrate = 0;


$guarantees = array();
$wassynced = "";
if($ratesid) {
	$rate = array();
	if(get_rate($ratesid, $rate)) {
		$description = $rate['description'];
		$code = $rate['ratecode'];
		$src = $rate['bookingsrc'];
		$occupancy = $rate['occupancy'];
		$ratetype = $rate['rate_type'];
		$minpax = $rate['minpax'];
		$maxpax = $rate['maxpax'];
		$minstay = $rate['minstay'];
		$maxstay = $rate['maxstay'];
		$currencycode = $rate['currency'];
		$date_started = $rate['date_started'];
		$date_stopped = $rate['date_stopped'];
		$minbook = $rate['minbook'];
		$rateitems = array();
		$rcount = get_rateitems($ratesid, $rateitems);
		if($ratetype== PROMORATE || $ratetype== AGENTRATE || $ratetype== CUSTOMERRATE )  {
			// Get the customer id associated with the rate

			if($ratetype== CUSTOMERRATE) {
				get_roomratetypes($ratesid, CUSTOMERRATE, $customerid);
	//			print "Customerid ".$customerid."<br/>";
			}
			// Get the agent id associated with the rate.
			if($ratetype== AGENTRATE) {
				// get the travel agent id associated
				get_roomratetypes($ratesid, AGENTRATE, $agentid);
			}
			// get any already selected rooms/roomtypes
			get_roomratetypes($ratesid, ROOMRATE, $rooms);
			get_roomratetypes($ratesid, ROOMTYPERATE, $roomtypes);

		}    
	}
}



// initialise variables if not already set.
if(!$roomexcl) $roomexcl = "";
if(!$roomtypeexcl) $roomtypeexcl = "";
if(!$minpax) $minpax=1;
if(!$maxpax) $maxpax=2;
if(!$minstay) $minstay=1;
if(!$maxstay) $maxstay=1;
if(!$minbook) $minbook = 0;
if(!$ratetype) $ratetype = DEFAULTRATE;
if(!$currencycode) $currencycode = get_defaultcurrencycode();
if(!$customerid) $customerid = 0;
if(!$agentid) $agentid = 0;

$cstyle = 'style="display: none;"';
$rstyle = 'style="display: none;"';
$astyle = 'style="display: none;"';
$bstyle = 'style="display: none;"';

if($ratetype == CUSTOMERRATE ) {
	$cstyle = '';
	$rstyle = '';
}
if($ratetype == PROMORATE ) {
	$rstyle = '';
	$bstyle = '';
}
if($ratetype == AGENTRATE) {
	$astyle = '';
	$rstyle = '';
}
?>

<?php
	$onsubmit = '';
?>	
<script type="text/javascript">
	  <!--
	  var request;
	  var dest;
	  function weekday_clicked(wk) {
		var ckstatus = wk.checked;
		var monday = document.getElementById('monday');
		var tuesday = document.getElementById('tuesday');
		var wednesday = document.getElementById('wednesday');
		var thursday = document.getElementById('thursday');
		var friday = document.getElementById('friday');
		var saturday = document.getElementById('saturday');
		var sunday = document.getElementById('sunday');
		var weekend = document.getElementById('weekend');
		var allweek = document.getElementById('allweek');
		if(monday.checked == true && tuesday.checked == true &&
		   wednesday.checked == true && thursday.checked == true && friday.checked == true &&
		   saturday.checked == true && sunday.checked == true) {
			allweek.checked = true;
			weekend.checked = true;
		} else {
			allweek.checked = false;
		}
		if(saturday.checked==true && sunday.checked== true) {
			weekend.checked = true;
		} else {
			weekend.checked = false;
		}
	  }
	  // Function to run if the weekend check box is selected
	  // will set saturday/sunday if off and unset all others
	  // or will unselect saturday/sunday/weekend and allweek if on.
	  // @param wke [in] The object selected.
	  function weekend_clicked(wke) {
		var ckstatus = wke.checked;
		var monday = document.getElementById('monday');
		var tuesday = document.getElementById('tuesday');
		var wednesday = document.getElementById('wednesday');
		var thursday = document.getElementById('thursday');
		var friday = document.getElementById('friday');
		var saturday = document.getElementById('saturday');
		var sunday = document.getElementById('sunday');
		var allweek = document.getElementById('allweek');
		if(ckstatus == true) {
			monday.checked = false;
			tuesday.checked = false;
			wednesday.checked = false;
			thursday.checked = false;
			friday.checked = false;
			allweek.checked = false;
			saturday.checked = true;
			sunday.checked = true;
		} else {
			saturday.checked = false;
			sunday.checked = false;
			allweek.checked = false;
		}	
	  }
	  function week_clicked(wke) {
		var ckstatus = wke.checked;
		var monday = document.getElementById('monday');
		var tuesday = document.getElementById('tuesday');
		var wednesday = document.getElementById('wednesday');
		var thursday = document.getElementById('thursday');
		var friday = document.getElementById('friday');
		var saturday = document.getElementById('saturday');
		var sunday = document.getElementById('sunday');
		var weekend = document.getElementById('weekend');
		if(ckstatus == true) {
			monday.checked = true;
			tuesday.checked = true;
			wednesday.checked = true;
			thursday.checked = true;
			friday.checked = true;
			weekend.checked = true;
			saturday.checked = true;
			sunday.checked = true;
		} else {
			monday.checked = false;
			tuesday.checked = false;
			wednesday.checked = false;
			thursday.checked = false;
			friday.checked = false;
			weekend.checked = false;
			saturday.checked = false;
			sunday.checked = false;
		}
	  }


	  function loadHTML(URL, destination, button) {
		dest = destination;
		var str = '?submit=' + button;
		URL=URL + str
		if (window.XMLHttpRequest){
			request = new XMLHttpRequest();
			request.onreadystatechange = processStateChange;
			request.open("GET", URL, true);
			request.send(null);
		} else if (window.ActiveXObject) {
			request = new ActiveXObject("Microsoft.XMLHTTP");
			if (request) {
				request.onreadystatechange = processStateChange;
				request.open("GET", URL, true);
				request.send();
			}
		}
	  }
	  function processStateChange() {
		if (request.readyState == 4){
			contentDiv = document.getElementById(dest);
			if (request.status == 200){
				response = request.responseText;
				contentDiv.innerHTML = response;
			} else {
				contentDiv.innerHTML = "Error: Status "+request.status;
			}
		}
	  }
	  // Function to show/hide form elements depending on the rate type.
	  // Rate type promotion, hide the customer/agents show the rooms
	  // Rate type customer, hide agents/rooms show the customer list
	  // Rate type agent, hide customer/rooms show the agents list
	  function showhideCustomerAgent(ratetype) {
		var cust = document.getElementById('customers');
		var agts = document.getElementById('agents');
		var blk = document.getElementById('blankrow');
		var rms = document.getElementById('roomlists');
		var i;
		var val;
		var txt;
//		alert("elements " + ratetype.length);	
		for (i = ratetype.length - 1; i>=0; i--) {
			if (ratetype.options[i].selected) {
				val = ratetype.options[i].value;
				txt = ratetype.options[i].text;
			}
		}
//		alert(val);
//		alert(txt);
		if ( val == '<?php echo CUSTOMERRATE; ?>') {
			cust.style.display = '';
			agts.style.display = 'none';
			blk.style.display = 'none';
			rms.style.display = '';
		} else if (val == '<?php echo AGENTRATE; ?>') {
			cust.style.display = 'none';
			blk.style.display = 'none';
			agts.style.display = '';
			rms.style.display = '';
		}else if (val == '<?php echo PROMORATE; ?>') {
			cust.style.display = 'none';
			agts.style.display = 'none';
			blk.style.display = '';
			rms.style.display = '';
		} else {
			cust.style.display = 'none';
			agts.style.display = 'none';
			rms.style.display = 'none';
		}
	  }
	  function loadHTMLPost(URL, destination, button){
		dest = destination;
		var str = 'button=' + button;
		if (window.XMLHttpRequest){
			request = new XMLHttpRequest();
			request.onreadystatechange = processStateChange;
			request.open("POST", URL, true);
			request.setRequestHeader("Content-Type","application/x-www-form-urlencoded; charset=UTF-8");
			request.send(str);
		} else if (window.ActiveXObject) {
			request = new ActiveXObject("Microsoft.XMLHTTP");
			if (request) {
				request.onreadystatechange = processStateChange;
				request.open("POST", URL, true);
				request.send();
			}
		}
	  }
	  function validateDates(){
		var currentTime = new Date();
		var month = currentTime.getMonth() + 1;
		var day = currentTime.getDate();
		var year = currentTime.getFullYear();
		var currentdate=day + "/" + month + "/" + year;
		if(day<10)
			day="0"+day;
		if(month<10)
			month="0"+month;
		var curdate=day + "/" + month + "/" + year;
		currentdate=currentdate.split("/");
		currentdate=new Date(currentdate[2],(currentdate[1]-1),currentdate[0]);
		//alert(currentdate);	
		var t1 = document.getElementById('date_started').value;    								
		var t2 =  document.getElementById('date_stopped').value;	
		var start_date=t1;
		var end_date=t2;
		var one_day=1000*60*60*24; 
		var x=t1.split("/");     
		var y=t2.split("/");
		var date1=new Date(x[2],(x[1]-1),x[0]);
  		var date2=new Date(y[2],(y[1]-1),y[0])
		var month1=x[1]-1;
		var month2=y[1]-1;
		var date1=new Date(x[2],(x[1]-1),x[0]);
  		var date2=new Date(y[2],(y[1]-1),y[0])
		var month1=x[1]-1;
		var month2=y[1]-1;
		//alert(date1);Mon Mar 19 2012 00:00:00 GMT+0800 
		var diff=date2-date1;
		var day=Math.floor(diff/(1000*60*60*24));
		var diffcurrentdate1=currentdate-date1;
		var diffcurrentdate2=currentdate-date2;
		if(diffcurrentdate2>0)
		{
 //   	    alert("<?php echo $_L['DT_grttoday_err'];?>");
    	    if(start_date!="")
    	    	document.getElementById('date_stopped').value=start_date;
    	    else
    	    	document.getElementById('date_stopped').value=curdate;
    	  //return false; 
    	} 
		if(diffcurrentdate1>0)
		{
//    	    alert("<?php echo $_L['DT_grttoday'];?>");
    	    document.getElementById('date_started').value=curdate;
    	   // return false; 
    	}
    	if(date1>date2)
    	{
//    	    alert("<?php echo $_L['DT_grtendday_err'];?>");
    	    document.getElementById('date_stopped').value=start_date;
    	   // return false; 
    	} 
    	return true; 	
	 }	
	  -->	 
	</script>
	<script language="JavaScript" src="js/highlight.js" type="text/javascript"></script>
	<style>
	  .plainDropDown{
		width:100px;
		font-size:11px;
	  }
	  .plainSelectList{
		width:200px;
		font-size:11px;
	  }
	  .plainButton {
		font-size:11px;	
	  }
	</style>
	
       <table height="500" class="listing-table">	        
	       <tbody>
	       	<tr>      	
	 		<?php print_rightMenu_admin();?> 	
	          <td valign="top">
				<table width="100%">
				<tr><td height="25px" colspan="5"><h2><a href="https://www.youtube.com/watch?v=KU8tRt9dl_o" target="reshelp"  title="Youtube help video"><img src='images/help.png' width="25" height="25" title="Youtube help video" /><?php echo $_L['RTS_title']; ?></a></h2></td></tr>
				<tr><td>
				<form action="<?php echo $_SERVER['REQUEST_URI'];?>" method="post" name="rates" id="rates" enctype="multipart/form-data" onsubmit="return selectall(this)">

					  
              <div id="TabbedPanels1" class="TabbedPanels">
                <ul id="tabgroup" class="TabbedPanelsTabGroup">                
                  <li class="TabbedPanelsTab" tabindex="0"><?php echo $_L['INV_rate']; ?></li>
                  <li class="TabbedPanelsTab" tabindex="1"><?php echo $_L['RTS_ptype']; ?></li>

                </ul>
                
                <div class="TabbedPanelsContentGroup">      
                  <div class="TabbedPanelsContent">
                  	<table height="413px" width="100%" border="0" cellpadding="1">
						  
						  <tr>
					  		<td colspan="6"  height="25px"><?php echo $validationMsgs;?></td>
					  	  </tr>
						  <?php if($errormsg){?>
						  <tr>
							<td height="" colspan="5">
							  <table width="100%"  border="0" cellpadding="1">
								<tr><td><font color="#FF0000"><b><?php echo $_L['RTS_warning']; ?></b></font><font color="#FF0000"><?php echo " ".$_L['RTS_warning_string']; ?></font></td></tr>
								<tr><td><font color="#FF0000"><i><?php echo $errormsg;?></i></font></td></tr>
								
							  </table>
							</td>
						  </tr>
						  <?php }?>		
						  <tr valign="top">
							<td height="35px"><?php echo $_L['RTS_code']; ?><font color="#FF0000">*</font></td>
							<td height="35px"><input type=text name=code size=20 maxlength=20 value='<?php echo $code; ?>' /><input type=text name=ratesid value='<?php echo $ratesid;?>' size=6 maxlength=10 readonly="readonly" /> </td>
							<td height="35px"><?php echo $_L['RTS_desc']; ?> </td>
							<td height="35px" colspan=2><input type=text id='description' name='description' size=20 maxlength=100 value='<?php echo $description; ?>' /> </td>
							<td height="35px"> </td>
						  </tr>
						  <tr>
							<td height="35px"><?php echo $_L['RTS_datefrom']; ?>:<font color="#FF0000">*</font></td>
							<td height="35px"><img src="images/ew_calendar.gif" width="16" height="15" border="0" onclick="setCalendarLanguage('<?php echo $lang; ?>');displayCalendar(document.forms[0].date_started,'dd/mm/yyyy',this);" /><input type="text" name="date_started" id="date_started" onchange="validateDates()" readonly="readonly" size=13 maxlength=13 value='<?php echo $date_started; ?>' /></td>
							<td height="35px"><?php echo $_L['RTS_dateto']; ?>:<font color="#FF0000">*</font></td>
							<td height="35px"><img src="images/ew_calendar.gif" width="16" height="15" border="0" onclick="setCalendarLanguage('<?php echo $lang; ?>');displayCalendar(document.forms[0].date_stopped,'dd/mm/yyyy',this);" /><input type="text" name="date_stopped" id="date_stopped" onchange="validateDates()" readonly="readonly" size=13 maxlength=13 value='<?php echo $date_stopped; ?>' /></td>
							<td height="35px"> </td>
						  </tr>
						  <tr>
							<td valign="top" colspan="5">
								  <table width="80%"  border="0" cellpadding="1">
									<tr>
									  <td height="80px"><?php echo $_L['RTS_ratetype']; ?><br/>
										<select id="ratetype" name="ratetype" onchange="showhideCustomerAgent(this);" >
										  <option value="<?php echo DEFAULTRATE; ?>" <?php if ($ratetype == DEFAULTRATE) echo "selected"; ?> ><?php echo $_L['RTS_default']; ?> </option>
										  <option value="<?php echo PROMORATE; ?>" <?php if ($ratetype == PROMORATE) echo "selected"; ?> ><?php echo $_L['RTS_promo']; ?> </option>
										  <option value="<?php echo CUSTOMERRATE; ?>" <?php if ($ratetype == CUSTOMERRATE) echo "selected"; ?> ><?php echo $_L['RTS_customer']; ?> </option>
										  <option value="<?php echo AGENTRATE; ?>" <?php if ($ratetype == AGENTRATE) echo "selected"; ?> ><?php echo $_L['RTS_agent']; ?> </option>
										  <option value="<?php echo DEFAULTFEE; ?>" <?php if ($ratetype == DEFAULTFEE) echo "selected"; ?> ><?php echo $_L['RTS_fee']; ?> </option>
										</select>
									  </td>
									  <td><?php echo $_L['RTS_bookingsrc']; ?><br/>
										<select name="bookingsrc">
										  <option value="<?php echo ALLSRC; ?>" <?php if ($src == ALLSRC) echo "selected"; ?> ><?php echo $_L['RTS_allsrc']; ?> </option>
										  <option value="<?php echo DIRECT; ?>" <?php if ($src == DIRECT) echo "selected"; ?> ><?php echo $_L['RTS_direct']; ?> </option>
										  <option value="<?php echo AGENT; ?>" <?php if ($src == AGENT) echo "selected"; ?> ><?php echo $_L['RTS_agent']; ?> </option>
										  <option value="<?php echo WEB; ?>" <?php if ($src == WEB) echo "selected"; ?> ><?php echo $_L['RTS_web']; ?> </option>
										  <option value="<?php echo DIRECTAGENT; ?>" <?php if ($src == DIRECTAGENT) echo "selected"; ?> ><?php echo $_L['RTS_directagent']; ?> </option>
										  <option value="<?php echo WEBOTA; ?>" <?php if ($src == WEBOTA) echo "selected"; ?> ><?php echo $_L['RTS_webota']; ?> </option>
										</select>
									  </td>
									  <td><?php echo $_L['RTS_currency']; ?><br/>
										<select name=currencycode >
										  <?php populate_select("countries","currency","currency",$currencycode,"currency <> ''"); ?>
										</select>
									  </td>
									  <td><?php echo $_L['RTS_occupancy']; ?><br/>
										<select name=occupancy>
										  <option value="<?php echo OSINGLE; ?>" <?php if($occupancy == OSINGLE) echo "selected"; ?> > <?php echo $_L['RTS_osingle']; ?></option>
										  <option value="<?php echo ODOUBLE; ?>" <?php if($occupancy == ODOUBLE) echo "selected"; ?> > <?php echo $_L['RTS_odouble']; ?> </option>
										  <option value="<?php echo OFAMILY; ?>" <?php if($occupancy == OFAMILY) echo "selected"; ?> > <?php echo $_L['RTS_ofamily']; ?> </option>
										</select>
									  </td>
									  <td>
										<table>
										  <tr id=blankrow <?php echo $bstyle; ?> >
										  <td width='140px'> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; </td>
										  </tr>
										  <tr id=customers <?php echo $cstyle; ?> >
											<td><?php echo $_L['RTS_customers']; ?><br/>
											  <select name=customerid class="plainDropDown">
												<option value=0> </option>
												<?php 
										        	populate_select("guests", "guestid", "eBridgeID",$customerid,"");
										        ?>										
											  </select>
											</td>
										  </tr>
										  <tr id=agents <?php echo $astyle; ?>  >
											<td><?php echo $_L['RTS_agents']; ?><br/>
											  <select name=agentid class="plainDropDown">
												<option value=0> </option>
												<?php populate_select("agents","agentid","agentname",$agentid,""); ?>
											  </select>
											</td>
										  </tr>
								
										</table>
									  </td>
									</tr>
									<tr id=roomlists <?php echo $rstyle; ?> >
									  <td height="60px" colspan=2><b><?php echo $_L['RM_roomno']; ?></b>
									  <div style="width: 270px; height: 50px; overflow:auto; border: 1px solid;"  >
									  <?php 
										  $rms = array();
										  get_roomslist($rms, '','',0 );
										  foreach($rms as $idx=>$val) {
												print "<input type='checkbox' id='RM".$idx."' name='RM".$idx."' value='".$idx."' ";
												foreach($rooms as $i=>$v) {
													if($v == $idx) {
														echo "checked";
														break;
													}
												}
												print " />".$rms[$idx]['roomno']." - ".$rms[$idx]['roomname']."<br/>";
											}
									  ?>
									  </div>
									  </td>
									  <td height="60px" colspan=2><b><?php echo $_L['RM_type']; ?></b>
									  <div style="width: 270px; height: 50px; overflow:auto; border: 1px solid;"  >
									  <table width='100%' height="">
									  <?php 
											$rt = array();
											get_roomtypelist($rt);
											foreach($rt as $idx=>$val) {
												print "<tr><td><input type='checkbox' id='RTP".$idx."' name='RTP".$idx."' value='".$idx."' ";
												foreach($roomtypes as $i=>$v) {
													if($v == $idx) {
														echo "checked";
														break;
													}
												}
												print " />".$rt[$idx]['roomtype']."</td><td>";

												print "</td></tr>";
												
											}
									
									  
									  ?>
									  </table>
									  </div>
			
									  </td>
									  <td height="30px" align=left valign=top>

									  </td>
									</tr>
									<tr>
									  <td height="50px"> <?php echo $_L['RTS_minpax']; ?><br/><input type=text name=minpax size=3 maxlength=3 value='<?php echo $minpax; ?>' /></td>
									  <td height="50px"><?php echo $_L['RTS_maxpax']; ?><br/><input type=text name=maxpax size=3 maxlength=3 value='<?php echo $maxpax; ?>' /></td>
									  <td height="50px"><?php echo $_L['RTS_minnights']; ?><br/><input type=text name=minstay size=3 maxlength=3 value='<?php echo $minstay; ?>' /></td>
									  <td height="50px"><?php echo $_L['RTS_maxnights']; ?><br/><input type=text name=maxstay size=3 maxlength=3 value='<?php echo $maxstay; ?>' /></td>
									  <td height="50px"><?php echo $_L['RTS_minbook']; ?><br/><input type=text name=minbook size=3 maxlength=3 value='<?php echo $minbook; ?>' /> </td>
									</tr>
                   		</table>
                   		</td>
                   		</tr>
                   		<tr height="50px" align="right">
                   			<td colspan="6">
							<div>
							<table align="right">
							  <tr><td><input class="button" type="submit" name="Submit"  value="<?php if (!$ratesid) { echo $_L['RTS_addrate']; } else { echo $_L['BTN_update']; } ?>" />
								<input class="button" type="button" name="Submit" value="<?php echo  $_L['RTS_listrates']; ?>" onclick="self.location='index.php?menu=ratesList'"/>
			     				</td></tr>	
							</table>
							</div> 
							</td>                  		
                   		</tr>
                  	</table>	
                  </div>    
                                
                  <div class="TabbedPanelsContent">
					<table height="465" width="100%"  border="0" cellpadding="1">
<!--						<tr><td colspan="2"><h2><?php //echo $_L['RTS_rateSetup'];?></h2></td></tr>-->
						
									<tr> 
									  <td height="310px" colspan=5>
										<?php if($ratesid) { ?>
										  <table border=1 cellspacing=0>
											<tr>
											  <th></th>
											  <th><?php echo $_L['RTS_pcode']; ?></th>
											  <th><?php echo $_L['RTS_ptype']; ?></th>
											  <th><?php echo $_L['RTS_amount']; ?></th>
											  <th><?php echo $_L['RTS_fees']; ?></th>
											  <th><?php echo $_L['RTS_days']; ?></th>
											  <th><?php echo $_L['RTS_months']; ?></th>
											  <th><?php echo $_L['RTS_holidays']; ?></th>
											  <th><?php echo $_L['RTS_max']; ?></th>
											</tr>
											<?php foreach ($rateitems as $idx=>$val) {
												print "<tr><td><input type=radio name=delitem value='".$idx."' /></td>";
												print "<td>".get_itemname($rateitems[$idx]['itemid'])."</td>";
												print "<td>";
												print get_discounttypestring($rateitems[$idx]['discounttype']);
												print "</td>";
												print "<td>".sprintf("%01.2f",$rateitems[$idx]['discountvalue'])."</td>";
												print "<td>";
												if($rateitems[$idx]['service']) print $_L['RTS_service']."<br/>";
												if($rateitems[$idx]['tax']) print $_L['RTS_tax'];
												print "</td>";
												print "<td>";
												print get_ratesperiodstring($rateitems[$idx]['validperiod'], 1, 0, 0);
												print "</td>";
												print "<td>";
												print get_ratesperiodstring($rateitems[$idx]['validperiod'], 0, 1, 0);
			//									print $rateitems[$idx]['validperiod'];
												print "</td>";
												print "<td>";
												print get_ratesperiodstring($rateitems[$idx]['validperiod'], 0, 0, 1);
			//									print $rateitems[$idx]['validperiod'];
												print "</td>";
												print "<td>".$rateitems[$idx]['maxcount']." </td></tr>";
											} ?>
											<tr>
											  <td> </td>
											  <td> 
												<select style="width: 70px;" name=itemcode>
												  <?php
												  $cond = "";
												  if ($ratetype == DEFAULTRATE) $cond = "itype=".ROOM;
													populate_select("details","itemid","item",0,$cond);
												  ?>
												</select>
											  </td>
											  <td> 
												<select style="width:70px; " name=dis >
												  <option value="<?php echo STANDARD; ?>" ><?php echo $_L['RTS_standard']; ?> </option>
												  <?php if ($ratetype <> DEFAULTRATE) { ?>
												  <option value="<?php echo FIXED; ?>" ><?php echo $_L['RTS_fixed']; ?> </option>
												  <option value="<?php echo PERCENT; ?>" ><?php echo $_L['RTS_percent']; ?> </option>
												  <option value="<?php echo FOC; ?>"  ><?php echo $_L['RTS_foc']; ?> </option>
												  <?php } ?>
												</select>
											  </td>
											  <td><input type=text name="price" size=3 maxlength=10 /> </td>
											  <td><?php echo $_L['RTS_service']; ?><br/>
												<input type=checkbox name=service id=service value=1><br/>
												<?php echo $_L['RTS_tax']; ?><br/>
												<input type=checkbox name=tax id=tax value=1>
											  </td>
											  <td>
												<table>
												  <tr>
													<td><?php echo $_L['RTS_mon']; ?> <br/>
													  <input type=checkbox name=monday id=monday value='<?php echo HOTEL_MON; ?>' onclick="weekday_clicked(this);" />
													</td>
													<td><?php echo $_L['RTS_tue']; ?><br/>
												      <input type=checkbox name=tuesday id=tuesday value='<?php echo HOTEL_TUE; ?>' onclick="weekday_clicked(this);" /> 
													</td>
												  </tr>
												  <tr>
													<td><?php echo $_L['RTS_wed']; ?> <br/>
													  <input type=checkbox name=wednesday id=wednesday value='<?php echo HOTEL_WED; ?>' onclick="weekday_clicked(this);" /> 
													</td>									  
													<td><?php echo $_L['RTS_thu']; ?> <br/>
													  <input type=checkbox name=thursday id=thursday value='<?php echo HOTEL_THU; ?>' onclick="weekday_clicked(this);" /> 
													</td>
												  </tr>
												  <tr>
													<td><?php echo $_L['RTS_fri']; ?> <br/>
													  <input type=checkbox name=friday id=friday value='<?php echo HOTEL_FRI; ?>' onclick="weekday_clicked(this);" /> 
													</td>
													<td><?php echo $_L['RTS_sat']; ?> <br/> 
												      <input type=checkbox name=saturday id=saturday value='<?php echo HOTEL_SAT; ?>' onclick="weekday_clicked(this);" /> 
													</td>									  
												  </tr>
												  <tr>
													<td><?php echo $_L['RTS_sun']; ?> <br/>
												      <input type=checkbox name=sunday id=sunday value='<?php echo HOTEL_SUN; ?>' onclick="weekday_clicked(this);" /> 
													</td>
													<td><?php echo $_L['RTS_wkd']; ?> <br/>
													  <input type=checkbox name=weekend id=weekend value='<?php echo HOTEL_WEND; ?>' onclick="weekend_clicked(this);"/> 
													</td>
												  </tr>
												  <tr>
													<td><?php echo $_L['RTS_wek']; ?> <br/>
													  <input type=checkbox name=allweek id=allweek value='<?php echo HOTEL_WEEK; ?>' onclick="week_clicked(this);" /> 
													</td>									  
												  </tr>
												</table>
											  </td>
											  <td>
												<table>
												  <tr>
													<td><?php echo $_L['RTS_jan']; ?> <br/>
													  <input type=checkbox name=january id=january value='<?php echo HOTEL_JAN; ?>' <?php if($ratetype == DEFAULTRATE) echo "checked readonly"; ?> /> 
													</td>
													<td><?php echo $_L['RTS_feb']; ?> <br/>
													  <input type=checkbox name=february id=february value='<?php echo HOTEL_FEB; ?>' <?php if($ratetype == DEFAULTRATE) echo "checked readonly"; ?> /> 
													</td>
													<td><?php echo $_L['RTS_mar']; ?> <br/>
													  <input type=checkbox name=march id=march value='<?php echo HOTEL_MAR; ?>' <?php if($ratetype == DEFAULTRATE) echo "checked readonly"; ?> /> 
													</td>
												  </tr>
												  <tr>
													<td><?php echo $_L['RTS_apr']; ?> <br/>
													  <input type=checkbox name=april id=april value='<?php echo HOTEL_APR; ?>' <?php if($ratetype == DEFAULTRATE) echo "checked readonly"; ?> /> 
													</td>									  
													<td><?php echo $_L['RTS_may']; ?> <br/>
														<input type=checkbox name=may id=may value='<?php echo HOTEL_MAY; ?>' <?php if($ratetype == DEFAULTRATE) echo "checked readonly"; ?> /> 
													</td>
													<td><?php echo $_L['RTS_jun']; ?> <br/>
														<input type=checkbox name=june id=jun value='<?php echo HOTEL_JUN; ?>' <?php if($ratetype == DEFAULTRATE) echo "checked readonly"; ?> /> 
													</td>
												  </tr>
												  <tr>
													<td><?php echo $_L['RTS_jul']; ?> <br/> 
														<input type=checkbox name=july id=july value='<?php echo HOTEL_JUL; ?>' <?php if($ratetype == DEFAULTRATE) echo "checked readonly"; ?> /> 
													</td>
													<td><?php echo $_L['RTS_aug']; ?> <br/> 
														<input type=checkbox name=august id=august value='<?php echo HOTEL_AUG; ?>' <?php if($ratetype == DEFAULTRATE) echo "checked readonly"; ?> /> 
													</td>									  
													<td><?php echo $_L['RTS_sep']; ?> <br/>
														<input type=checkbox name=september id=september value='<?php echo HOTEL_SEP; ?>' <?php if($ratetype == DEFAULTRATE) echo "checked readonly"; ?> /> 
													</td>
												  </tr>
												  <tr>
													<td><?php echo $_L['RTS_oct']; ?> <br/>
														<input type=checkbox name=october id=october value='<?php echo HOTEL_OCT; ?>' <?php if($ratetype == DEFAULTRATE) echo "checked readonly"; ?> /> 
													</td>
													<td><?php echo $_L['RTS_nov']; ?> <br/>
														<input type=checkbox name=november id=november value='<?php echo HOTEL_NOV; ?>' <?php if($ratetype == DEFAULTRATE) echo "checked readonly"; ?> /> 
													</td>
													<td><?php echo $_L['RTS_dec']; ?> <br/>
														<input type=checkbox name=december id=december value='<?php echo HOTEL_DEC; ?>' <?php if($ratetype == DEFAULTRATE) echo "checked readonly"; ?> /> 
													</td>									  
												  </tr>
												</table>
											  </td>
											  <td>
											 	 <?php echo $_L['RTS_holidays']; ?><br/><input type=checkbox name=holiday id=holiday value='<?php echo HOTEL_HOLS; ?>' <?php if($ratetype == DEFAULTRATE) echo "checked readonly"; ?> /> 
											  </td>
											  <td>
												 <input type=text name=maxcount size=2 maxlength=5 value='0' <?php if($ratetype == DEFAULTRATE) echo "readonly"; ?> /> 
											  </td>
											</tr>
										  </table>
										<?php 
										} // if $ratesid
										?>
									  </td>
									</tr>	
									<tr align="right">
									  <td colspan=5>
									  <?php 
										if($ratesid) {
										  print "<input class='button' type=submit name='Submit' value='".$_L['RTS_delitem']."' /> ";
										  print "<input class='button' type=submit name='Submit' value='".$_L['BTN_refresh']."' /> ";
										  print "<input class='button' type=submit name='Submit' value='".$_L['RTS_additem']."' /> ";
										}
									  ?>
									  </td>
									</tr>													
													  					
					</table>	
                  </div>
	
                  
                                		
				</div>
			</div>									  
					  
					  
				  
				  
				</form>
				</td></tr></table>
	          </td>	              
	        </tr>

	      </tbody>
      </table>

<?php
/**
 * @}
 * @}
 */
?>