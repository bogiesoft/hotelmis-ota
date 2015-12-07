<?php
/**
 * @package OTA Hotel Management
 * @copyright e-Novate Pte Ltd 2012-2015
 */
 $showadvance=0;
  $showadvance = $_POST['showadv'];
 if(isset($_POST['advBtn']) && $_POST['advBtn']=="Advanced" ){
	$showadvance = 1;
 }elseif ($_POST['advBtn']=="Standard" ){
	$showadvance = 0;
 }
 
 ?>
       <table class="listing-table" border="0" height="500">
	        
	       <tbody>
	       	<tr>	
	 		<?php print_rightMenu_home();?> 	
	          <td valign="top">
	          	
			    <form id="mainform" name="mainfrom" action="index.php" method="post" enctype="multipart/form-data">
			      <table class="c16" width="100%"  border="0" cellpadding="1" align="center">
			        <tr valign="top">
			          <td class="c4" width="65%">
			            <table class="tdbgcl" width="100%" border="0" cellpadding="1">
			              
			               <tr>
			                <td>&nbsp;</td>           
			              </tr>
						  <tr>
			                <td align=right>
			                  <input type="Submit" id="advBtn"  name="advBtn" class="button" value="<?php if($showadvance) echo "Standard"; else echo $_L['BTN_advanced'];?>" >
			                </td>
			               
			              </tr>
						  <tr>
			                <td>&nbsp;</td>
			              
			              </tr>
			              <tr>
						   <input type="hidden" id="showadv" name="showadv" value="<?php echo $showadvance;?>" />
						  <?php if(!$showadvance){?>
						 
			                <td >
											<!-- Here lies the calendar -->
											<?php
			//								echo "Session".$_SESSION['userid']."<br/>";
											if(isset($_SERVER['HTTPS'])) { $ssl = "s"; }
											else { $ssl = ""; }
											$path = "http".$ssl."://".$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']);
											//echo "Path is ".$path."\n";
											if($_SESSION['userid']) {
											?>
												<script type='text/javascript'>
												$(document).ready(function() {
														var date = new Date();
														var d = date.getDate();
														var m = date.getMonth();
														var y = date.getFullYear();
														var h = date.getHours();
														var i = 0;
														var s = 0;
														
														
														$('#calendar').fullCalendar({
															header: {
																left: 'prev,next today',
																center: 'title',
																right: 'month,basicWeek,basicDay'
															},
															defaultView: 'month',
															editable: false,
															slotMinutes: 5,
															firstHour: h,
															eventLimit: true,
															events: [
															<?php
																$inStart = date("d/m/Y", (time() - 24*60*60*2)); 	// 2 days ago
																$inEnd = date("d/m/Y", (time() + 24*60*60*60)); 	// 60 days from today 
																$fromdate = date("Y/m/d", (time() - 24*60*60*2));
																$endDate = date("Y/m/d", (time() + 24*60*60*60));
																$comma = "";
																for ($i = strtotime($fromdate); $i <= strtotime($endDate); $i += 86400) {
																	echo $comma."{\n";
																	echo "title: utf8Decode('".$_L['USR_rooms']."'),\n";
																	echo "start: new Date(".date("Y", $i).",".(date("m", $i)-1).",".date("d", $i).",0,0,0),\n"; // check in time
																	echo "end: new Date(".date("Y", $i).",".(date("m", $i)-1).",".date("d", $i).",0,0,0),\n";
																	echo "allDay: true,\n";														
																	echo "url: '".$path."/index.php?menu=roomsview&date=".date("Y/m/d",$i)."',\n";
																	//echo "eventColor: '#000000'\n";
																	echo "}\n";													
																	$comma = ",";
																}
																$rlist = array(); // results array
																$res = get_all_reservations($inStart, $inEnd, "", $rlist, RES_ACTIVE);
																$inLast = 0;
																$inInc = 0;
																for ($idx=0;$idx<$res;$idx++){
																	$inStart = str_replace("/","-", $rlist[$idx]['checkindate']);
																	list($in_dd,$in_mm,$in_yy,$in_hh,$in_ii) = sscanf($inStart, "%d-%d-%d %d:%d");
																	if($rlist[$idx]['checkindate'] == $inLast) {
																		if($inInc == 30) { 
																			$inInc = 0;
																		} else {
																			$inInc+=5;
																		}
																		$in_ii += $inInc;
																	} else {
																		$inInc = 0;
																	}
																	$inLast = $rlist[$idx]['checkindate'];
																	//$in_mm--;
																	$in_i2 = $in_ii+5;
																	echo $comma."{\n";
																	if($rlist[$idx]['status']==RES_CANCELREQUESTED){
																		$title = "title: utf8Decode('".$_L['RSV_canselreq']."') + '-".$rlist[$idx]['guestname']." (".$rlist[$idx]['reservation_by'].")',\n";
																	} else {
																		$title= "title: utf8Decode('".$_L['REG_checkin']."') + '-".$rlist[$idx]['guestname']." (".$rlist[$idx]['reservation_by'].")',\n"; // person name
																	}
																	echo $title;
																	echo "start: new Date(".$in_yy.",".($in_mm-1).",".$in_dd.",".$in_hh.",".$in_ii.",0),\n"; // check in time
																	echo "end: new Date(".$in_yy.",".($in_mm-1).",".$in_dd.",".$in_hh.",".$in_i2.",0) ,\n";
																	echo "allDay: false,\n";														
																	if($rlist[$idx]['status']==RES_CANCELREQUESTED){
																		echo "url: '".$path."/index.php?menu=reservation&resid=".$rlist[$idx]['reservation_id']."',\n";
																		echo "eventColor: '#FF0000'\n";
																	}
																	else{
																		echo "url: '".$path."/index.php?menu=reservation&resid=".$rlist[$idx]['reservation_id']."',\n";
																	}
																	echo "}\n";
																	$comma = ",";
																}
																
																$outStart = date("d/m/Y", (time() - 24*60*60*2)); 	// 2 days ago
																$outEnd = date("d/m/Y", (time() + 24*60*60*5)); 	// 5 days from today 													
																$blist = array();
																$book = get_all_bookings($blist, BOOK_CHECKEDIN, 0);
																$outLast = 0;
																$outInc = 0;	
																for ($idx=0;$idx<$book;$idx++){
																 	$outStart = str_replace("/","-", $blist[$idx]['checkoutdate']);
																 	list($out_dd,$out_mm,$out_yy,$out_hh,$out_ii) = sscanf($outStart, "%d-%d-%d %d:%d");
																 	if($blist[$idx]['checkoutdate'] == $outLast) {
																 		if($outInc == 30) { 
																 			$outInc = 0;
																 		} else {
																  			$outInc+=5;
																 		}
																 		$out_ii += $outInc;
																 	} else {
																 		$outInc = 0;
																 	}
																	$outLast = $blist[$idx]['checkoutdate'];
																	$out_mm--;
																	$out_i2 = $out_ii+5;
																	echo $comma."{\n";
																	echo "title: utf8Decode('".$_L['REG_checkout']."') + '- ".$blist[$idx]['roomno'] ." ".$blist[$idx]['guestname'] ."',\n"; // person name
																	echo "start: new Date(".$out_yy.",".$out_mm.",".$out_dd.",".$out_hh.",".$out_ii.",0),\n"; // check out time
																	echo "end: new Date(".$out_yy.",".$out_mm.",".$out_dd.",".$out_hh.",".$out_i2.",0) ,\n";
																	echo "allDay: false,\n";
																	echo "url: '".$path."/index.php?menu=booking&id=".$blist[$idx]['book_id']."'\n";
																	echo "}\n";
																	$comma = ",";
																}		
																
															?>
															]
			/*												
															eventClick: function(event) {
																if (event.url) {
																	window.open(event.url);
																	return false;
																}
															}
			*/											
														})
												});
												</script>
												<div id='calendar'></div>
											<?php
											}
											else 
											{
											?>
												<table id ="FrontTable">
													<tr>
														<td>
															<table>
																<tr><td><b>e-Bridge connects your customers and agents directly to your Reservation System</b></td></tr>
																<tr><td><iframe width="640" height="390" src="//www.youtube.com/embed/FFCYfIvjlWk" frameborder="0" allowfullscreen></iframe></td></tr>
															</table>
														</td>
														<td>
															<table>	
																<tr>
																	<td>
																		<table id ="RightFrontTable">
																			<tr><td>Looking for a cost effective way of increasing sales and room revenue?</td></tr>	
																			<tr><td>e-Bridge connects you  directly to your customers and agents from as little as USD 50 a year.<br>
																					Transact securely on a real time basis with minimum effort.<br>
																					Receive a free website and reservation software.<br>
																					Contact us today 30days free unlimited usage with your 1st purchase.<br></td></tr>
																			<tr><td>Find out more at <a href="https://www.e-bridgedirect.com"><b>www.e-bridgedirect.com</b></a> <br>or contact us at <b>marketing@e-novate.asia</b><br> or at <b>+65 6747 0497.</b></td></tr>													
																		</table>
																	</td>
																</tr>												
															</table>	
														</td>	
													</tr>
												</table>
											
											<?php 
											}
											?>
			                </td>
							<?php }else{?>
							<td >
							
								<?php if(is_ebridgeCustomer()){
										include_once(dirname(__FILE__)."/OTA/advancedFeatures/adv_calendar.php");
									}else{
										print "Not ebridge customer";
									}
								?>
								
							</td>
							
							<?php }?>
			               
			              </tr>
			            </table>
			          </td>
					    <?php if(!$showadvance){?>
					  
					  <td class="c4" width="20%">
					  	<h2><?php if($_SESSION['userid']) echo $_L['RSV_canselreq']; ?></h2><br/>
						<div style="overflow-y: scroll; height:150px;">
					  		<?php 
					  		if($_SESSION['userid']) {
						  		$i = 1;
								$res = get_all_reservations($inStart, $inEnd, "", $rlist, RES_CANCELREQUESTED);
						  		for ($idx=0;$idx<$res;$idx++){
									echo $i++."."; 
						  			echo '<a href='.$path.'/index.php?menu=reservation&resid='.$rlist[$idx]['reservation_id'].' >'. $rlist[$idx]['voucher_no'].'</a>';
						  			echo $rlist[$idx]['guestname']."<br/>";
						  		}
							}
							?>
						</div>
						<h2> <?php echo $_L['USR_billlist']; ?></h2><br/>
						<div style="overflow-y: scroll; height:250px;">
					  		<?php 
					  		if($_SESSION['userid']) {	
								$billids = array();
								if(get_billids_by_status(STATUS_OPEN, $billids)) {
									$i=1;
									
									foreach($billids as $billid =>$bstatus) {
										if(is_bill_inDebit($billid)) {
											echo $i++."."; 
						  					echo '<a href='.$path.'/index.php?menu=invoice&id='.$billid.' >'. get_billnumber($billid).'</a><br/>';
										}
									}
								}
					  		}
					  		?>
					  	</div>		  
					  </td>
					  <?php }?>
			        </tr>
			      </table>
			    </form>	             
	           
	          </td> 
	        </tr>
	        <tr>
	          <td colspan="2">&nbsp;</td>
	        </tr>
	      </tbody>
      </table>
