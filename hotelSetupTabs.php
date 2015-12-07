<?php
/**
 * @package OTA Hotel Management
 * @copyright e-Novate Pte Ltd 2012-2015
 */ 
?>

       <table class="listing-table">	        
	       <tbody>
	       	<tr>	
	 			<?php print_rightMenu_admin();?> 	 			
	          <td width="" valign="top">	         
	          <table width="105%">
			  <tr>
				 <td>
				 
				 <h2><a href="https://www.youtube.com/watch?v=eyRTHlfRKcc" target="reshelp"  title="Youtube help video"><img src='images/help.png' width="25" height="25" title="Youtube help video" /><?php echo $_L['HTL_title']; ?></a></h2>			  
			  </tr>			   
			  <tr><td>
              <div id="TabbedPanels1" class="TabbedPanels">
                <ul id="tabgroup" class="TabbedPanelsTabGroup">                
					<?php 
					//This tab index cross reference to the spry assets tab javaxcript at the bottom of index.php
					$tabidx = 0; 
					?>
                  <li class="TabbedPanelsTab" tabindex="<?php echo $tabidx;?>" onclick="getTabIndex(<?php echo $tabidx; $tabidx++;?>);"><?php echo $_L['ITM_hotel']; ?></li>
                  <li class="TabbedPanelsTab" tabindex="<?php echo $tabidx;?>" onclick="getTabIndex(<?php echo $tabidx; $tabidx++;?>);"><?php echo $_L['ADM_items']; ?></li>
                  <li class="TabbedPanelsTab" tabindex="<?php echo $tabidx;?>" onclick="getTabIndex(<?php echo $tabidx; $tabidx++;?>);"><?php echo $_L['ADM_documentnos']; ?></li>
                  <?php if (is_ebridgeCustomer()) {?>
                  	<li class="TabbedPanelsTab" tabindex="<?php echo $tabidx;?>" onclick="getTabIndex(<?php echo $tabidx; $tabidx++;?>);"><?php echo $_L['CST_fields']; ?></li>
                  <?php } ?>
                  <li class="TabbedPanelsTab" tabindex="<?php echo $tabidx;?>" onclick="getTabIndex(<?php echo $tabidx; $tabidx++;?>);"><?php echo $_L['OTA_title']; ?></li>
                  <li class="TabbedPanelsTab" tabindex="<?php echo $tabidx;?>" onclick="getTabIndex(<?php echo $tabidx; $tabidx++;?>);"><?php echo $_L['UPL_import']; ?></li>
                </ul>
                <div class="TabbedPanelsContentGroup">               
                  
                  <div class="TabbedPanelsContent">
<!--                  	<iframe  src="adminhotel.php" width="800" height="420" frameborder="0" scrolling="no">       	-->
<!--                  	-->
<!--                  	</iframe>                  -->
						<?php include_once 'adminhotel.php';?>
                  </div>
                  
                  <div class="TabbedPanelsContent">
						<?php include_once 'items.php';?>
                  </div>
                  
                  <div class="TabbedPanelsContent">
                  	<?php include_once 'documents.php';?>                
                  </div>                  
                  
				  <?php if (is_ebridgeCustomer()) {?>
	                  <div class="TabbedPanelsContent">  
                  	<?php include_once 'customfields.php';?>  	                  
	                  </div>                 
				  <?php } ?>
                  <div class="TabbedPanelsContent">
				  <?php 
				    if ($_GET['tab']=='agodaconf' || $_POST['tab']=="agodaconf") {
						include_once 'agodaconf.php';                     
					}	else if ($_GET['tab']=='expediaconf' || $_POST['tab']=="expediaconf"){	
						include_once 'expediaconf.php';    
                  	}	else {			  
						include_once 'otasync.php';                      
					} 
				  ?>
                  </div>  
                  <div class="TabbedPanelsContent"> 
                  	<?php include_once 'uploadExcel.php';?>  
                  </div>  				
				
				</div>
			</div>				
	           <td>
			   </tr>
			   </table>
	          </td>	  	                 
	        </tr>
	        <tr>
	          <td colspan="2">&nbsp;</td>
	        </tr>
	      </tbody>
      </table>
	  <script>
		document.getElementById("tabgroup").addEventListener("click", function(){
			alert("!!");	
		});
	  </script>

