<?php 
$_DB->scheme->x_add('department',Array(
		'name'=>'text',
		//'fld1'=>'varchar',
		'parent'=>'#department.id',
		//'adate'=>'datetime',// date
		'leader'=>'#user.id|{login}',
		'#defdata'=>Array(
			//	Array('name'=>'1234','leader'=>3),
			//	Array('name'=>'5678','charact'=>'SSS FFF TTT','user'=>3),
		)
));

$_DB->scheme->x_add('idea',Array(
		'name'=>'text',
		//'fld1'=>'varchar',
		'charact'=>'memo',
		'adate'=>'datetime',// date
		'autor'=>'#user.id|{login}',
	/*	'#defdata'=>Array(
				Array('name'=>'1234','charact'=>'2rrfrgtrgtrg frfr','user'=>3),
				Array('name'=>'5678','charact'=>'SSS FFF TTT','user'=>3),
		)*/
));

$_DB->scheme->x_add('problem',Array(
		'name'=>'text',
		//'fld1'=>'varchar',
		'charact'=>'memo',
		'adate'=>'datetime',// date
		'autor'=>'#user.id|{login}',
	/*	'#defdata'=>Array(
				Array('name'=>'1234','charact'=>'2rrfrgtrgtrg frfr','user'=>3),
				Array('name'=>'5678','charact'=>'SSS FFF TTT','user'=>3),
		)*/
));
/*
$_DB->scheme->add('article',Array(
		'name'=>'text',
		//'fld1'=>'varchar',
		'atext'=>'memo',
		'adate'=>'datetime',// date  
		'user'=>'#user.id|{login}',
		'#defdata'=>Array(
				Array('name'=>'1234','atext'=>'2rrfrgtrgtrg frfr','user'=>1),
				Array('name'=>'rf34','atext'=>'2rio tttj 4585383','user'=>1),
				Array('name'=>'12eew','atext'=>'2ewre ererer 87r','user'=>1),
			)
		)
	); */
?>