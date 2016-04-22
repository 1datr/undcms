<?php 
if(!empty($_SESSION['uid']))
{
	$res = $_DB->scheme->select('user',Array('id','login'))->where("id = ".$_SESSION['uid'])->exe();
	$row =$_DB->scheme->res_row($res);
	use_mod_template('authed',Array('_LOGIN'=>$row['login'],'id'=>$row['id']));
}
else	
{
	echo get_form('mod_auth/auth');	
	//use_mod_template('authform');
}
?>