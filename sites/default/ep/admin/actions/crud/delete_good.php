
<?php
	if(!empty($_POST['id']))
	{
		$_DB->scheme->delete_item('good',$_POST['id'])->exe();
	}		
			?>