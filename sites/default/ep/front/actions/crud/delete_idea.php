
<?php
	if(!empty($_POST['id']))
	{
		$_DB->scheme->delete_item('idea',$_POST['id'])->exe();
	}		
			?>