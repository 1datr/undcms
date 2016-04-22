<?php
		if(!signature('page:integer|1'))
		error404();
		$select=Array('id','name','parent','parent|name');
		$_PSIZE=5;
		$res=$_DB->scheme->select('group',$select)->page($_PSIZE,$_QUERY['page'])->exe();
		$pcount=$_DB->scheme->last_select_pagecount($_PSIZE);
		use_template("view_group",Array('res'=>$res,'pcount'=>$pcount));
		
		