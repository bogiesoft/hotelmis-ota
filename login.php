<?php
/**
 * @package OTA Hotel Management
 * @copyright e-Novate Pte Ltd 2012-2015
 */ 
?> 
     <table class="main-table">
        <tbody><tr>
          <td class="main-heading" height="30"><?php echo $_L['PR_login'];?></td>
        </tr>
       <tr>
          <td height="300" valign="top"><br>
            <?php echo $_L['GEN_pleaseEnterYour']."<strong>".$_L['GEN_account']."</strong> ".$_L['GEN_followedBy']." <strong> ".$_L['GEN_password']."</strong> ".$_L['GEN_toLogin'].".<br>"?> 
            <br>
            <br>
            
            <form name="frmLogin" enctype="multipart/form-data" method="post" action="index.php">
              <table class="inner-table">
              	
                <tbody><tr>
                  <td><table class="border">
                  <tbody><tr>
                      <td colspan="2" height="5"></td>
                    </tr>
                    <tr>
                     <?php
					  if($_SESSION['userid']) {
					  ?>
					  <td><input type=hidden name='username' value='<?php echo $username ?>' /></td>
					  <?php
					  } else {
					  ?>
                      <td class="email-txt" align="right" height="25" width="154"><font class="mandatory">*</font><?php echo $_L['GEN_account'];?>&nbsp;</td>
                      <td align="left" width="250"><input name="username" class="input" maxlength="100" type="text"></td>
                    </tr>
                    <tr>
                      <td class="email-txt" align="right" height="25"><font class="mandatory">*</font><?php echo $_L['GEN_password'];?>&nbsp;</td>
                      <td align="left"><input name="password" class="input" maxlength="20" type="password"></td>
                    </tr>
                    <?php
					}
					
					?>
                    <tr>
                      <td align="right" height="25">&nbsp;</td>
                      <td align="left">
                      	<input type="submit" name='login' class="button" value='<?php 
                      		echo !isset($_SESSION['userid']) ? $_L['PR_login'] : $_L['PR_logout'];
							echo "' />";?>
	
                      </td>
                    </tr>
                    
                  </tbody></table></td>
                </tr>
                <tr>
                  <td align="right" height="40"><a href="#" class="forgotpassword" title="Forgot your password?">Forgot your password?</a></td>
                </tr>
             </tbody></table>
            </form>
          </td>
        </tr>
        
     </tbody></table>      