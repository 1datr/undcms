<?php 
// ���� �����
function captcha_img($addparams=NULL)
{
	global $_CURRMODULE;
	$_CURRMODULE = 'captcha';
	$capcha_id="cap_img";
	
	if(xarray_key_exists('capid', $addparams))
		$capcha_id=$addparams['capid'];
	bs_act_mess("err_".$capcha_id."_wrong");
	use_mod_template('capcha_block',Array('capcha_id'=>$capcha_id,'addparams'=>$addparams));	
}
// ��������� �����
function check_capcha($capcha_id='capcha')
{	
	
	if($_SESSION[$capcha_id]!=$_POST[$capcha_id])
	{
		set_act_mess("err_".$capcha_id."_wrong","�������� ����������� ���",true);
	}
}
?>