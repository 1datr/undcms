<?php
init_module('pmess');

function on_pmess_dbinit($params)
{
	
	$params['db']->scheme->x_add('pmessage',Array(
			'title'=>'text',
			'content'=>'memo',
			'from'=>'#user.id|{login} ',
			'to'=>'#user.id|{login} ',
			'date'=>'datetime',
	)
	);
	
}

function on_pmess_after_get_page($params)
{
	global $_QUERY;
	//echo ">> ".$_GET['q']." >>";
	
}
// ����� ���� mod_auth
function  on_pmess_after_get_block($params)
{
	global $_QUERY;
	if($params['BLOCKNAME']=='mod_auth/authform')
	{
		if(!empty($_SESSION['uid']))
		{	
			global $_DB;
			$res=$_DB->scheme->select('pmessage',Array())->where("`to`={UID}")->exe('quinfo',Array('UID'=>$_SESSION['uid']));
			global $_CURRMODULE;
			$_CURRMODULE='pmess';
			$umess = use_mod_template('usermess_info',Array(
				'count'=>$_DB->scheme->result_count($res)
			),true);
			
			//echo $umess;
			
			$params['BODY']=$umess.$params['BODY'];
		}
	}
	
}
?>