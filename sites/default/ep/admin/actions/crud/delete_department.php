
<?php
	if(!empty($_POST['id']))
	{
		$_DB->scheme->delete_item('department',$_POST['id'])->exe();
	}		
			?>