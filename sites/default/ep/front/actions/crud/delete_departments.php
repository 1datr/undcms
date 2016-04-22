
<?php
	if(!empty($_POST['id']))
	{
		$_DB->scheme->delete_item('departments',$_POST['id'])->exe();
	}		
			?>