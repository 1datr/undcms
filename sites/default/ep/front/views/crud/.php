<?php
		if(!signature('page:integer|1'))
		error404();
		$select=Array('id');
		$_PSIZE=5;
		$res=$_DB->scheme->select('',$select)->page($_PSIZE,$_QUERY['page'])->exe();
		$pcount=$_DB->scheme->last_select_pagecount($_PSIZE);
		use_template("view_",Array('res'=>$res,'pcount'=>$pcount));
		
		