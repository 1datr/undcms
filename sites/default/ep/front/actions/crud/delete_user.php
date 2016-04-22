
<?php
	if(!empty($_POST['id']))
	{
		$_DB->scheme->delete_item('user',$_POST['id'])->exe();
	}		
			?>