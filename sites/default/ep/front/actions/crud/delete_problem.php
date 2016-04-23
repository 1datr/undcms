
<?php
	if(!empty($_POST['id']))
	{
		$_DB->scheme->delete_item('problem',$_POST['id'])->exe();
	}		
			?>