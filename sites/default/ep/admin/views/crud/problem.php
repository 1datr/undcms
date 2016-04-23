<?php
		if(!signature('page:integer|1'))
		error404();
		$select=Array('id','name','charact','adate','autor','autor|login');
		$_PSIZE=5;
		$res=$_DB->scheme->select('problem',$select)->page($_PSIZE,$_QUERY['page'])->exe();
		$pcount=$_DB->scheme->last_select_pagecount($_PSIZE);
		use_template("view_problem",Array('res'=>$res,'pcount'=>$pcount));
		
		