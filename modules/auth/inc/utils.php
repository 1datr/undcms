<?php
// группы по их паренту
function get_group_by_parent($pid)
{
	$buf=Array();
	global $_DB;
	$res = $_DB->scheme->select('group',Array('id','name'))->where("parent=$pid")->exe();
	while($row = $_DB->scheme->res_row($res))
	{
		$buf[]=array('id'=>$row['id'],'name'=>$row['name'],'childs'=>get_group_by_parent($row['id']));
	}
	return $buf;
}

function get_group_ids($pid)
{
	$buf=Array();
	global $_DB;
	$res = $_DB->scheme->select('group',Array('id','name'))->where("parent=$pid")->exe();
	while($row = $_DB->scheme->res_row($res))
	{
		$buf[]=$row['id'];
		$childs=get_group_ids($row['id']);
		$buf=array_merge($buf,$childs);
	}
	return $buf;
}
// ids групп админов
function get_admins_group_ids()
{
	global $_DB;
	$buf=Array();
	$res = $_DB->scheme->select('group',Array('id','name'))->where("name='admins'")->exe();
	$row = $_DB->scheme->res_row($res);

	$buf=array_merge(Array($row['id']),get_group_ids($row['id']));

	return $buf;
}
 
function get_admins_groups()
{
	global $_DB;
	$buf=Array();
	$res = $_DB->scheme->select('group',Array('id','name'))->where("name='admins'")->exe();
	$row = $_DB->scheme->res_row($res);
	
	$buf[]=Array('id'=>$row['id'],'name'=>$row['name'],'childs'=>get_group_by_parent($row['id']));
	
	return $buf;		
}
// Админ ли сейчас
function is_role_admin($uid=null)
{
	global $_DB;
	if($uid==null)
		$uid=$_SESSION['uid'];
	if (is_role_guest()) return false;
	
	$admin_groups = get_admins_group_ids();
	$res=$_DB->scheme->select('groupmember',Array('user','group'))->where("user={uid} AND `group` IN (".implode(',', $admin_groups).")")->exe('q_admin',array('uid'=>$uid));
	return($_DB->scheme->result_count($res) > 0);	
}

?>