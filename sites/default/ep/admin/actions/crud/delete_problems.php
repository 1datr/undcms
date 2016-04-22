
<?php
	if(!empty($_POST['id']))
	{
		$_DB->scheme->delete_item('problems',$_POST['id'])->exe();
	}		
			?>