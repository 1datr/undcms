<?php 
//stop_action();
$_REDIR_TIMEOUT=40000;
//if($_POST)
$res=$_DB->scheme->select('user',Array('id','login','password'))->where("login='".$_POST['login']."' AND password=md5('".$_POST['password']."')")->exe();
if($_DB->scheme->result_count($res))
{
//set_act_mess("err_name","������� �������� ������",true);
	success('�� ������� �����. ����� �������� �� ��������.',2000);
	$row =$_DB->scheme->res_row($res);
	$_SESSION['uid']=$row['id'];
}
else 
{
	set_act_mess("err_auth","������� ����� ���� ������",true);
}

?>