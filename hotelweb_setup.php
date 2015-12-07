<?php
/**
 * @package OTA Hotel Management
 * @copyright e-Novate Pte Ltd 2012-2015
 * @file hotelweb_setup.php
 * @brief hotelweb web setup page called by OTA Hotel Management
 */

session_start();
//error_reporting(E_ALL & ~E_NOTICE);
include_once(dirname(__FILE__)."/login_check.inc.php");
include_once(dirname(__FILE__)."/queryfunctions.php");
include_once(dirname(__FILE__)."/functions.php");
include_once(dirname(__FILE__)."/lang/lang_en.php");

$lang = get_language();
load_language($lang);
access("admin"); //check if user is allowed to access this page

$logofile = Get_LogoFile();

$hoteldesc="";
$outfile = "hoteldescription.txt";
$validationMsgs="";

if (isset($_POST['Submit'])){	
	$action=$_POST['Submit'];
	switch ($action) {		
		case $_L['HWS_btn_updatedec']:
			$data=	$_POST[hoteldesc];
			$fh = fopen($outfile, 'w');
			fwrite($fh, $data);
			fclose($fh);	
			break;
		case $_L['HWS_addimg']:
			// instantiate form validator object
			$fv=new formValidator(); //from functions.php
			$fv->EmptyCheck('imgurl',$_L['HWS_err_imgurl']);
							
			
			if($fv->checkErrors()){
				// display errors
				$validationMsgs = "<div align=\"left\"><h2>".$_L['PR_formerr']."</h2>".$fv->displayErrors()."</div>";				
			} 
			else {						
				$hoteldesc=$_POST["hoteldesc"]; 
				$imgtitle=$_POST["imgtitle"];
				$imgurl=$_POST["imgurl"];
				$imgdesc=$_POST["imgdesc"];
			    $imgpg=$_POST["imgpage"];
				$isvideo=$_POST["isvideo"];
				$picid=modify_hotelgallery($picid,$imgtitle,$imgurl,$imgdesc,$imgpg,$isvideo);				
			}
			break;	
		case  $_L['BTN_delete']:			
			if(isset($_POST['deleteImg'])){	 	
				delete_hotelgallery($_POST['deleteImg']);				 	
			}
			break;	
	}
}
$gallery =array();
// Get all of the gallery
get_hotelgallery($gallery,-1,-1);

if(file_exists($outfile)){	
	$fh = fopen($outfile, 'r');
	$hoteldesc = fread($fh,filesize($outfile));
	fclose($fh);	
}		
?>
       <table class="listing-table">	        
	       <tbody>
	       	<tr>	
	 		<?php print_rightMenu_admin();?> 		
	          <td valign="top">
	          	<table>
				  <tr>
					<td>
					<h2><a href=""  title="Youtube help video"><img src='images/help.png' width="25" height="25" title="Youtube help video" /><?php echo $_L['MNU_websetup']; ?></a></h2>
					</td>
				  </tr> 	          	
	         	<tr><td>
				<form action="<?php echo $_SERVER['REQUEST_URI'];?>" name='websetup' id='websetup' method="post" enctype="multipart/form-data">
              <div id="TabbedPanels1" class="TabbedPanels">
                <ul id="tabgroup" class="TabbedPanelsTabGroup">                
                  <li class="TabbedPanelsTab" tabindex="0"><?php echo $_L['HWS_hoteldesc']; ?></li>
                  <li class="TabbedPanelsTab" tabindex="1"><?php echo $_L['HWS_galimgurl']; ?></li>
                </ul>
                <div class="TabbedPanelsContentGroup">               
                  
                  <div class="TabbedPanelsContent">
                  	<table width="100%" border="0">
                  			<tr><td>&nbsp;</td></tr><tr><td>&nbsp;</td></tr>
                  			<tr>
				  				<td><?php echo $validationMsgs?></td>
				  			</tr>
				            <tr>
							  <td><b><?php echo $_L['HWS_warning'];?></b>: <?php echo $_L['HWS_warning_string'];?></td>
				            </tr>
				            <tr valign="top" height="340px">
				              <td  width="80%"><textarea name="hoteldesc" id="hoteldesc" cols="45" rows="5" ><?php echo $hoteldesc;?></textarea>
				              <script type="text/javascript">
								CKEDITOR.replace( 'hoteldesc',
								{
									toolbar :
									[
										{ name: 'clipboard', items : [ 'Cut','Copy','Paste','PasteText','PasteFromWord','-','Undo','Redo' ] },
										{ name: 'editing', items : [ 'Find','Replace','-','SelectAll','-','Scayt' ] },
										{ name: 'insert', items : [ 'Image','Flash','Table','HorizontalRule','Smiley','SpecialChar','PageBreak'
								                 ,'Iframe' ] },
									                '/',
										{ name: 'styles', items : [ 'Styles','Format' ] },
										{ name: 'basicstyles', items : [ 'Bold','Italic','Strike','-','RemoveFormat' ] },
										{ name: 'paragraph', items : [ 'NumberedList','BulletedList','-','Outdent','Indent','-','Blockquote' ] },
										{ name: 'links', items : [ 'Link','Unlink','Anchor' ] },
										{ name: 'tools', items : [ 'Maximize','-','About' ] }
										],
										width: "700px",
								        height: "110px"								
								});
					        </script>
				              </td>
				            </tr>
				            <tr align="right">
				              <td>
				              <input class="button" type="submit" name="Submit" id="Submit" value="<?php echo $_L['HWS_btn_updatedec']; ?>" /></td>
				            </tr>	
				  	</table>				
                  </div>
                  
                  <div class="TabbedPanelsContent">
				       <table width="100%" border="0">
				       		<tr><td>&nbsp;</td></tr>
				            <tr height="120px">
				              <td>				              
				              <table width="80%" border="1" cellspacing="0">
				                <tr>
				                  <td><?php echo $_L['HWS_imgtitle']; ?></td>
				                  <td><?php echo $_L['HWS_imgurl']; ?><font color="#FF0000">*</font></td>
				                  <td><?php echo $_L['HWS_imgdesc']; ?></td>
				                  <td><?php echo $_L['HWS_page']; ?></td>
				                  <td><?php echo $_L['HWS_imgvideo']; ?></td>
				                </tr>				          
				                <tr>
				                  <td>
				                  <input name="imgtitle" id="imgtitle" type="text" size='14' maxlength='100' value="<?php if(isset($_POST["imgtitle"])&&!empty($validationMsgs)) echo $_POST["imgtitle"]?>" />
				                  </td>
				                  <td><input type="text" name="imgurl" id="imgurl" size='23' maxlength='100' /></td>
				                  <td><textarea name="imgdesc" id="imgdesc" cols="20" rows="2"><?php if(isset($_POST["imgdesc"])&&!empty($validationMsgs)) echo $_POST["imgdesc"]?></textarea></td>
								  <td><input type="radio" name="imgpage" id="imgpage" value="0" checked /> <?php echo $_L['HWS_gallery']; ?> <input type="radio" name="imgpage" id="imgpage" value="1"/> <?php echo $_L['HWS_promo']; ?> </td>
								  <td><input type="radio" name="isvideo" id="isvideo" value="0" checked /> <?php echo $_L['HWS_image']; ?> <input type="radio" name="isvideo" id="isvideo" value="1"/> <?php echo $_L['HWS_video']; ?></td>
				         
				                </tr>
				              </table>
				              </td>				              
				            </tr>
				            <tr ><td align="right"><input class="button" type="submit" name="Submit" id="Submit" value="<?php echo $_L['HWS_addimg']; ?>" /></td></tr>
				            <tr><td>&nbsp;</td></tr>
				            <tr height="212px">
				            <td>
				            <div style="overflow:auto; height:200px;">
				            <table border='1' width="80%" cellspacing="0"><tr>
				             <?php
				            	if($gallery){	
								$i=0;			
								foreach($gallery as $idx=>$value) {
								  //display existing records 
				
									echo "<td><input type=radio name='deleteImg'  id='deleteImg' value='".$gallery[$idx]['PicID']."' /></td>";					
									echo "<td>";				
									echo $gallery[$idx]['Title'];				
									echo "</td>";
				
									echo "<td width='45%'>";				
									echo $gallery[$idx]['URL'];				
									echo "</td>";
									
									echo "<td>";
									echo $gallery[$idx]['Description'];				
									echo "</td>";
									echo "<td>";
									if($gallery[$idx]['page']) {
										echo $_L['HWS_promo'];
									} else {
										echo $_L['HWS_gallery'];
									}
									echo "</td>";
									echo "<td>";
							
									if($gallery[$idx]['imgtype']) {
										echo $_L['HWS_video'];
									} else {
										echo $_L['HWS_image'];
									}
									echo "</td>";
				
									echo "</tr>";
									$i++;				
								}	
							}	
							?>
							</tr>
							</table>
							</div>
							</td>			
							</tr>
							<tr>
								<td align='right'>
								<input class="button" type="submit" name="Submit" id="Submit" value= "<?php echo $_L['BTN_delete']; ?>" />
								<?php
								if(is_ebridgeCustomer()){ 
				
									print "<a href='OTA/advancedFeatures/update_web.php' class='button'  target='_blank'>Update Website</a>";
								}
								?>
								</td>	
							</tr>
				            <tr>
				              <td>&nbsp;</td>
				            </tr>
				            <tr>
				              <td>&nbsp;</td>
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

