<?php
require_once dirName(__FILE__)."/inc/utils.php";

init_module('auth');

function on_auth_dbinit($params)
{
	$params['db']->scheme->x_add('user',Array(
		'login'=>'text',
		'password'=>'text',
		'name'=>Array("Type"=>'text','charset'=>'utf8'),
		'avatar'=>'/avatar',
		'#defdata'=>Array(
				Array('login'=>'root','name'=>'root','password'=>md5('123456')),
				Array('login'=>'vasya','name'=>'Vasya','password'=>md5('vasya')),
				Array('login'=>'masha','name'=>'Masha','password'=>md5('masha')),
				Array('login'=>'grisha','name'=>'grisha','password'=>md5('grisha')),
				Array('login'=>'pasha','name'=>'pasha','password'=>md5('pasha')),
				Array('login'=>'sasha','name'=>'sasha','password'=>md5('sasha')),
				Array('login'=>'dasha','name'=>'dasha','password'=>md5('dasha')),
		),
		
	));
	global $op_std_custom_op;
	$params['db']->scheme->x_add('group',Array(
		'name'=>'text',
		'parent'=>'#~group.id|{name} ',
		'#defdata'=>Array('name'=>'admins'),
		'#addata'=>Array(
			'#op'=>Array(	
							'admin'=>Array(
									'addmember'=>$op_std_custom_op,
							),
					)
				),
			)
	);
	
//
	$params['db']->scheme->x_add('groupmember',Array(
		'user'=>'#user.id|{login}',
		'group'=>'#group.id|{name}',
		'owner'=>'logic',
		'#addata'=>Array('conname'=>'groups'),
	));
}

function on_auth_after_dbinit($params)
{
	
	$res=$params['db']->scheme->select('group',Array('id'))->where("name='admins'")->exe();
	$group_row=$params['db']->scheme->res_row($res);
	
	$res=$params['db']->scheme->select('user',Array('id'))->where("login='root'")->exe();
	$user_row=$params['db']->scheme->res_row($res);
	
	$params['db']->scheme->insert('groupmember',Array(
						'user'=>$user_row['id'],
						'group'=>$group_row['id'],
						'owner'=>1
			))->exe();
	
}

function on_auth_before_get_page($args)
{
	//var_dump($args);
	global $_EP;
	
    if($_EP=='admin')
	{
		if(!is_role_guest() && !is_role_admin())
		{
			unset($_SESSION['uid']);
			redirect('/admin/');
		}
		else 
		{
			if($args['PAGE']=='index')
			{
				if(!is_role_guest())
				//	echo ">> redirect users >>";
					redirect('users');
			}
			else
			{
				if(!is_role_admin())
				//	echo ">> redirect admin >>";
					redirect('/admin/');
			}
		}
	}
	
	
}
?>