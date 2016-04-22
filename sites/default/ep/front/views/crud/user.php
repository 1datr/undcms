<?php
		if(!signature('page:integer|1'))
		error404();
		$select=Array('id','login','password','name','avatar');
		$_PSIZE=5;
		$res=$_DB->scheme->select('user',$select)->page($_PSIZE,$_QUERY['page'])->exe();
		$pcount=$_DB->scheme->last_select_pagecount($_PSIZE);
		use_template("view_user",Array('res'=>$res,'pcount'=>$pcount));
		
		