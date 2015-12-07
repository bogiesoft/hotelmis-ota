<?php
/**
 * @package OTA Hotel Management
 * @copyright e-Novate Pte Ltd 2012-2015
 * @file setup/index.php
 * @brief Hotel Management System Initial setup page called by OTA Hotel Management Installer
 * see readme.txt for credits and references
 * @addtogroup CODE_MANAGEMENT
 * @{
 * @defgroup ADMIN_MANAGEMENT Hotel setup and management page
 * @{
 */
 include_once(dirname(__FILE__)."../functions.php");
error_reporting(0);

/**
 * @}
 * @}
 */
?>


<html><head>
<meta http-equiv="Content-Type" content="text/html; charset=us-ascii" />
<title><?php echo $_L['MAIN_Title'];?></title>
<link href="../css/styles2.css" rel="stylesheet" type="text/css">
<!--<link rel="StyleSheet" href="css/pulldownmenus.css" type="text/css">-->
<!--<link rel="stylesheet" type="text/css" href="css/vendor.css">-->
<!--<link rel="stylesheet" type="text/css" href="css/client.css">-->
<!--<link rel="stylesheet" type="text/css" href="css/profile.css">-->
<!--<link type="text/css" rel="stylesheet" href="js/dhtmlgoodies_calendar.css" media="screen">-->
<!--<script type="text/javascript" src="js/dhtmlgoodies_calendar.js"></script> -->
<!--<script language="JavaScript" type="text/javascript" src="js/ScrollableTable.js"></script>-->
<!--<script language="javascript" type="text/javascript" src="js/form_funcs.js"> </script>-->
<!--<script language="JavaScript" type="text/javascript" src="js/popup-window.js"></script>-->

<link type="text/css" rel="stylesheet" href="js/dhtmlgoodies_calendar.css" media="screen"></link>
<script type="text/javascript" src="js/dhtmlgoodies_calendar.js"></script>
    <link href="../css/new.css" rel="stylesheet" type="text/css" />
	<script type='text/javascript' src='js/urlpost.js'></script>
	<link href='js/fullcalendar.css' rel='stylesheet' />
	<link href='js/fullcalendar.print.css' rel='stylesheet' media='print' />
	<script src='js/lib/moment.min.js'></script>
	<script src='js/lib/jquery.min.js'></script>
	<script src='js/fullcalendar.min.js'></script>

	<script type="text/javascript" src="ckeditor/ckeditor.js"></script>
</head>

<body>
	<div class="wrapper">
		<div class="boundary">
			<div class="header">
				<div class="logo"><a href="http://www.e-novate.asia" target="_blank"><img src="../images/enovate_logoWEB2.jpg" alt=""></a></div>
			</div>
		<div class="clr"></div>
		<div class="wrapper">
		  <?php 
			if (isset($_GET['action'])){
				if($_GET['action'] == 'configsetup' ) {
					include("configsetup.php");
				} else if($_GET['action'] == 'initialsetup' ) {
					include("initialsetup.php");
				} else if($_GET['action'] == 'thankyou' ) {
					include("thankyou.php");
				}
			} else {
				include("configsetup.php");
			}

		  ?>
		  </div>
		<div>
			<table>
				<tr><?php print_footerinit(); ?>	</tr>
			</table>
		</div>
		</div>
    <div>
</body>
</html>