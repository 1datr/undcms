<?php
		
		if(!signature('id:integer'))
		error404();
		$res=$_DB->scheme->select('department','*')->where('id='.$_QUERY['id'])->exe();
		$row=$_DB->scheme->res_row($res);
		if(!$_DB->scheme->result_count($res))
		error404();
		use_template('edit_department',Array('row'=>$row));
				?>
				