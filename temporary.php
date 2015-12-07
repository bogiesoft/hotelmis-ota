<?php 
session_start();
/**
 * @package OTA Hotel Management
 * @file temporary.php
 * @copyright e-Novate Pte Ltd 2012-2015
 * @brief page called by OTA Hotel Management
 */
//error_reporting(E_ALL&~ E_NOTICE);
ob_start();
include_once(dirname(__FILE__)."/functions.php");
include_once(dirname(__FILE__)."/lang/lang_en.php");

/** 
 * This prints the standard menu
 */
function print_std_menus($username) { 
	global $_L;
	echo '<table class="navigation">
         
           <tbody><tr>
              <td class="main-links" align="center" height="32"><a href="/hotelmis/index.php?menu=home" title="Home">'.$_L['ADP_home'].'</a> <a href="/hotelmis/index.php?menu=mysettings" title="My Settings">'.$_L['MNU_mySettings'].'</a>';
		if (accessNew("reports")) {
			echo '<a href="/hotelmis/index.php?menu=reports" title="Reports">'.$_L['ADM_reports'].'</a>';
		}
		if (accessNew("admin") || accessNew("agents") || accessNew("rooms") || accessNew("rates")) {
			echo '<a href="/hotelmis/index.php?menu=admin" title="Admin">'.$_L['USR_admin'].'</a>';
		}
		echo '<a href="#" onclick="window.open(&quot;/hotelmis/doxygen/html/index.html&quot;)" title="Help">'.$_L['MNU_help'].'</a> <a href="/hotelmis/index.php?menu=logout" title="Logout">'.$_L['PR_logout'].' ('.$username.')</a>';
		echo '</td>
           </tr>         
       </tbody></table>';

}

/**
 * This prints the right menu pane for Home page
 */
function print_rightMenu_home() {
	echo '         
	<td class="border-right" height="365" valign="top" width="150">
		<table border="0" cellpadding="2" cellspacing="2" width="100%">         
			<tbody>';
	if (accessNew("reservation") || accessNew("booking")) {
		echo '
             <tr>
                <td class="modules-heading"><strong>Reserve/Book</strong></td>
              </tr>
              <tr>
                <td><div class="listing">
                  <ul>
                     <ul>';
		if (accessNew("reservation")) {
                   echo '<li><a href="/hotelmis/index.php?menu=reservation" title="Reservation">Reservation</a></li>';
		}
		if (accessNew("booking")) {
                   echo '<li><a href="/hotelmis/index.php?menu=booking" title="Booking">Guest Check In</a></li>';
		}       
		echo '
                  </ul>
                  </ul>
                </div></td>
              </tr>';
	}
	if (accessNew("guest")) {
	echo '
              <tr>
                <td class="modules-heading"><strong>Profile</strong></td>
              </tr>
              <tr>
                <td>
                	<div class="listing">
	                  <ul>
	                     <ul>
	                   <li><a href="/hotelmis/index.php?menu=profile" title="Clients">Guest Profile</a></li>
	                  </ul>
	                  </ul>
                	</div>
                </td>
              </tr>';
	}
	if (accessNew("billing")) {
	echo '
              <tr>
                <td class="modules-heading"><strong>Billing</strong></td>
              </tr>
              <tr>
                <td>
                	<div class="listing">
	                  <ul>
	                     <ul>
	                   <li><a href="/hotelmis/index.php?menu=invoice" title="Clients">Invoice</a></li>
	                   <li><a href="/hotelmis/index.php?menu=exportInvoice" title="Clients">Export Invoice</a></li>
	                  </ul>
	                  </ul>
                	</div>
                </td>
			</tr>';
	}
//	if (accessNew("lookup")) {
//	echo '
//              <tr>
//                <td class="modules-heading"><strong>Look Up</strong></td>
//              </tr>
//              <tr>
//                <td>
//                	<div class="listing">
//	                  <ul>
//	                     <ul>
//	                   <li><a href="/hotelmis/index.php?menu=search" title="Clients">Search</a></li>
//	                  </ul>
//	                  </ul>
//                	</div>
//                </td>
//			</tr>';
//	}
	echo '              
            </tbody>
		</table>
	</td>';	
}

/**
 * 
 * This prints the right menu pane for Admin page
 */
function print_rightMenu_admin() {
	echo '         
	<td class="border-right" height="365" valign="top" width="150">
		<table border="0" cellpadding="2" cellspacing="2" width="100%">         
			<tbody>';
	if (accessNew("admin") || accessNew("agents") || accessNew("rooms") || accessNew("rates")) {
		echo '
             <tr>
                <td class="modules-heading"><strong>Administration</strong></td>
              </tr>
              <tr>
                <td><div class="listing">
                  <ul>
                     <ul>';
	if (accessNew("admin")) {
			echo '
                		<li><a href="/hotelmis/index.php?menu=admin" title="Reservation">Hotel Setup</a></li>  
						<li><a href="/hotelmis/index.php?menu=websiteSetup" title="Website Setup">Website Setup</a></li>  
						<li><a href="/hotelmis/index.php?menu=userSetup" title="User Setup">User Setup</a></li>  
						<li><a href="/hotelmis/index.php?menu=emailSetup" title="Email Setup">Email Setup</a></li>  
						<li><a href="/hotelmis/index.php?menu=policySetup" title="Policy Setup">Policy Setup</a></li>  
						<li><a href="/hotelmis/index.php?menu=currencySetup" title="Currency Setup">Currency Setup</a></li>  
						<li><a href="/hotelmis/index.php?menu=holidaySetup" title="Holiday Setup">Holiday Setup</a></li> ';
	}
	if (accessNew("agents")) {
			echo '<li><a href="/hotelmis/index.php?menu=agentSetup" title="Agent Setup">Agent Setup</a></li>';
	}                  
	if (accessNew("rates")) {
			echo '<li><a href="/hotelmis/index.php?menu=rateSetup" title="Room Rates">Room Rate Setup</a></li>';
	}
	if (accessNew("rooms")) {
			echo '<li><a href="/hotelmis/index.php?menu=roomSetup" title="Clients">Room Setup</a></li>';
	}	
	if (accessNew("admin")) {
			echo '<li><a href="/hotelmis/index.php?menu=roomTypeSetup" title="Holiday Setup">Room Type Setup</a></li>';
	}			
    echo '            </ul>
                  </ul>
                </div></td>
              </tr>';
	}
	echo '              
            </tbody>
		</table>
	</td>';
	
}

/**
 * 
 * This prints the right menu pane for My Settings page
 */
function print_rightMenu_mySettings() {
	echo '         
	<td class="border-right" height="365" valign="top" width="150">
		<table border="0" cellpadding="2" cellspacing="2" width="100%">         
			<tbody>';
		echo '
             <tr>
                <td class="modules-heading"><strong>My Profile</strong></td>
              </tr>
              <tr>
                <td><div class="listing">
                  <ul>
                     <ul>
                		<li><a href="/hotelmis/index.php?menu=myProfile" title="My Profile">Profile</a></li>  
						<li><a href="/hotelmis/index.php?menu=myShift" title=My Shift">Shift</a></li>  
                  </ul>
                  </ul>
                </div></td>
              </tr>';
				//<li><a href="/hotelmis/index.php?menu=changeMyPassword" title="Change My Password">Change Password</a></li>  
	echo '              
            </tbody>
		</table>
	</td>';
	
}

/**
 * 
 * This prints the right menu pane for Reports page
 */
function print_rightMenu_reports() {
	global $_L;
	echo '         
	<td class="border-right" height="365" valign="top" width="150">
		<table border="0" cellpadding="2" cellspacing="2" width="100%">         
			<tbody>';
	if (accessNew("reports")) {
		echo '
             <tr>
                <td class="modules-heading"><strong>Reports</strong></td>
              </tr>
              <tr>
                <td><div class="listing">
                  <ul>
                     <ul>
                		<li><a href="/hotelmis/index.php?menu=holidayReport" title="'.$_L['RT_Holidayrpt'].'">'.$_L['RT_holidayRpt'].'</a></li>  
						<li><a href="/hotelmis/index.php?menu=guestReport" title="'.$_L['RT_hotelguestrpt'].'">'.$_L['RT_GuestReport'].'</a></li>  
						<li><a href="/hotelmis/index.php?menu=onlineBookingReport" title="'.$_L['RT_OnlineBookingrpt'].'">'.$_L['RT_onlineBookingRpt'].'</a></li>  
						<li><a href="/hotelmis/index.php?menu=roomStatusReport" title="'.$_L['RT_roomstatusrpt'].'">'.$_L['RT_roomrpt'].'</a></li>  
						<li><a href="/hotelmis/index.php?menu=receiptDailyReport" title="'.$_L['RT_ReceiptDailyrpt'].'">'.$_L['RT_ReceiptDailyrpt'].'</a></li>  
						<li><a href="/hotelmis/index.php?menu=receiptReport" title="'.$_L['RT_Receiptrpt'].'">'.$_L['RT_Receiptrpt'].'</a></li>  
						<li><a href="/hotelmis/index.php?menu=roomUsabilityReport" title="'.$_L['RT_roomusabilityrpt'].'">'.$_L['RT_roomusabilityrpt'].'</a></li>  
						<li><a href="/hotelmis/index.php?menu=shiftReport" title="'.$_L['RT_shiftrpt'].'">'.$_L['RT_shiftRpt'].'</a></li>  
						<li><a href="/hotelmis/index.php?menu=taxReport" title="'.$_L['RT_taxreport'].'">'.$_L['RT_taxreport'].'</a></li>  
						<li><a href="/hotelmis/index.php?menu=agodaReport" title="'.$_L['RT_agodareport'].'">'.$_L['RT_agodareport'].'</a></li>  
						<li><a href="/hotelmis/index.php?menu=tourismReport" title="'.$_L['RT_tourismreport'].'">'.$_L['RT_tourismreport'].'</a></li>  
                  </ul>
                  </ul>
                </div></td>
              </tr>';
	}
	echo '              
            </tbody>
		</table>
	</td>';
	
}


?>