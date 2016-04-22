
<?php
	if(!empty($_POST['id']))
	{
		$_DB->scheme->delete_item('group',$_POST['id'])->exe();
	}		
			?>