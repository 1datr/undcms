<?php 
//var_dump($_POST);
if(empty($_POST['emailfrom']))
	set_act_mess("err_addr","������� �����",true);
if(empty($_POST['mes']))
	set_act_mess("err_mess","������� ���������",true);
check_capcha('cap_feedback');
success('���� ��������� ����������. ����� �������� �� ��������.',2000);

?>