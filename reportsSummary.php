<?php
/**
 * @package OTA Hotel Management
 * @copyright e-Novate Pte Ltd 2012-2015
 */ 
?>
       <table height="500" class="listing-table">	        
	       <tbody width=60% >
	       	<tr>	
	 		<?php print_rightMenu_reports();?> 			 	
	          <td valign="top">	          	
						<table class="tdbgcl" width="95%"  border="1" cellpadding="1" height="400">
						<tr bgcolor="3593DE"><th><?php echo $_L['RT_reportName']; ?></th><th> <?php echo $_L['RT_reportDesc'];?></th><th></th></tr>
						<tr>
						<td align="left" >
						<a id="res" name="res" ><b> <?php echo  $_L['RT_holidayRpt'];?></b></a>
                      	</td>
						<td align="left"><?php echo  $_L['RT_Holidaydesc'];?></td>
						</tr>
						
						<tr>
						<td align="left" >
						<a id="res" name="res" ><b> <?php echo  $_L['RT_GuestReport'];?></b></a>
                      	</td>
                      	
						<td align="left"><?php echo  $_L['RT_hotelguestdesc'];?></td>
						</tr>
						<tr>
						<td align="left" >
						<a id="res" name="res" ><b><?php echo  $_L['RT_onlineBookingRpt'];?></b></a>
                      	</td>
						<td align="left"><?php echo  $_L['RT_OnlineBookingdesc'];?></td>
						</tr>
						
						<tr>
						<td align="left" >
						<a id="res" name="res" ><b> <?php echo  $_L['RT_roomrpt'];?></b></a>
                      	</td>
						<td align="left"><?php echo  $_L['RT_roomstatusdesc'];?></td>
						</tr>
						
						<tr>
						<td align="left" >
						<a id="res" name="res" ><b><?php echo  $_L['RT_ReceiptDailyrpt'];?></b></a>
                      	</td>
						<td align="left"><?php echo  $_L['RT_ReceiptDailydesc'];?></td>
						</tr>
						
						<tr>
						<td align="left" >
						<a id="res" name="res" ><b><?php echo  $_L['RT_Receiptrpt'];?></b></a>
                      	</td>
						<td align="left"><?php echo  $_L['RT_Receiptdesc'];?></td>
						</tr>
						
						<tr>
						<td align="left" >
						<a id="res" name="res" ><b> <?php echo  $_L['RT_roomusabilityrpt'];?></b></a>
                      	</td>
						<td align="left"><?php echo  $_L['RT_roomusabilitydesc'];?></td>
						</tr>
						<tr>
						<td align="left" >
						<a id="res" name="res" ><b> <?php echo  $_L['RT_shiftRpt'];?></b></a>
                      	</td>
						<td align="left"><?php echo  $_L['RT_shiftdesc'];?></td>
						</tr>
						<tr>
						<td align="left" >
						<a id="res" name="res" ><b><?php echo  $_L['RT_taxreport'];?></b></a>
                      	</td>
						<td align="left"><?php echo  $_L['RT_taxdesc'];?></td>
						</tr>
						<tr>
						<td align="left" >
						<a id="res" name="res" ><b><?php echo  $_L['RT_agodareport'];?></b></a>
                      	</td>
						<td align="left"><?php echo  $_L['RT_AgodaBookingdesc'];?></td>
						</tr>
						<tr>
						<td align="left" >
						<a id="res" name="res" ><b><?php echo  $_L['RT_tourismreport'];?></b></a>
                      	</td>
						<td align="left"><?php echo  $_L['RT_tourismdesc'];?></td>
						</tr>						
						</table>          
	           
	          </td>
	          
	        </tr>
	        <tr>
	          <td colspan="2">&nbsp;</td>
	        </tr>
	      </tbody>
      </table>
