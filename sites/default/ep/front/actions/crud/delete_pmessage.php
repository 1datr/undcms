
<?php
	if(!empty($_POST['id']))
	{
		$_DB->scheme->delete_item('pmessage',$_POST['id'])->exe();
	}		
			?>