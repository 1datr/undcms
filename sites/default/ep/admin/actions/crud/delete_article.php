
<?php
	if(!empty($_POST['id']))
	{
		$_DB->scheme->delete_item('article',$_POST['id'])->exe();
	}		
			?>