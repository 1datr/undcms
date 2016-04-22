<?php 
//var_dump($_POST);
if(empty($_POST['emailfrom']))
	set_act_mess("err_addr","¬ведите адрес",true);
if(empty($_POST['mes']))
	set_act_mess("err_mess","¬ведите сообщение",true);
check_capcha('cap_feedback');
success('¬аше сообщение отправлено. ∆дите перехода на страницу.',2000);

?>