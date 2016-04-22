<?php
		//var_dump($_POST);
		$id=$_POST['row']['id'];
		unset($_POST['row']['id']);
		$_DB->scheme->update('user',$_POST['row'])->where("id=$id")->exe();
		?>