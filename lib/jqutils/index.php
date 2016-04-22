<?php 
function jqready($code,$comment=NULL)
{
	addjs(xbrotherfileurl(__FILE__,'/js/jquery.min.js'));
	//echo "::JS:: READY";
	global $_SCR_READY_HEAD,$_SCR_READY_END;
	$_SCR_READY_HEAD="
	\$( document ).ready(function() {";
  		$_SCR_READY_END="
});
	";
	global $_SCRIPT_BLOCKS_READY;
	// return if accepted
	$_SCRIPT_BLOCKS_READY[]=$sb;
}

function jqinit()
{
	addjs(xbrotherfileurl(__FILE__,'/js/jquery.min.js'));
}

$_SCRIPT_JQREADY_PTR=null;
$_JQR_BLOCK=Array();

function jqready_gather($code)
{
	global $_SCRIPT_BLOCKS_READY;
	global $_SCR_READY_HEAD,$_SCR_READY_END;
	addjs(xbrotherfileurl(__FILE__,'/js/jquery.min.js'));
	init_bootstrap();
	$_SCR_READY_HEAD="
	\$( document ).ready(function() {";
	$_SCR_READY_END="
});
	";
	// return if accepted
	if(!in_array($code,$_SCRIPT_BLOCKS_READY))
		$_SCRIPT_BLOCKS_READY[]=$code;
}
?>