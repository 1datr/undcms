<?php
		if(!signature('page:integer|1'))
		error404();
		$select=Array('id','title','content','from','from|login','to','to|login','date');
		$_PSIZE=5;
		$res=$_DB->scheme->select('pmessage',$select)->page($_PSIZE,$_QUERY['page'])->exe();
		$pcount=$_DB->scheme->last_select_pagecount($_PSIZE);
		use_template("view_pmessage",Array('res'=>$res,'pcount'=>$pcount));
		
		