<?php 
//stop_action();
$_REDIR_TIMEOUT=40000;

$admin_groups=get_admins_group_ids();
//var_dump($admin_groups);

//if($_POST)
$res=$_DB->scheme->select('user',Array('id','login','password'))->where("login='".$_POST['login']."' AND password=md5('".$_POST['password']."')")->exe();
if($_DB->scheme->result_count($res))
{
//set_act_mess("err_name","������� �������� ������",true);
	success('�� ������� �����. ����� �������� �� ��������.',2000);
	$user_row =$_DB->scheme->res_row($res);
	
	$res=$_DB->scheme->select('groupmember',Array('user','group'))->where("user='".$user_row['id']."' AND `group` IN (".implode(',', $admin_groups).")")->exe();
	if($_DB->scheme->result_count($res))
	{	
		$_SESSION['uid']=$user_row['id'];
	}
	else 
	{
		set_act_mess("err_auth","You are not admin",true);
	}
}
else 
{
	set_act_mess("err_auth","������� ����� ���� ������",true);
}

?>