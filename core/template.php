<?php 
$_CSS = Array();
$_JS=Array();
$_JSIF=Array();
$_META=Array();
$_SCRIPT_BLOCKS=Array();
$_SCR_READY_HEAD='';
$_SCR_READY_END='';
$_SCRIPT_BLOCKS_READY=Array();

$_TITLE='';
$_THEME='default';
$_REGIONS=Array();
$_HEAD_READY=false;
// add the css to head
function addcss($_css)
{
	// return if accepted
	global $_HEAD_READY;
	global $_BASE_PATH;
	if($_HEAD_READY) return;
		
	global $_CSS;
	if(!in_array($_css, $_CSS))
		$_CSS[]=$_css;
}
// translate single string
function transl_str($str)
{
	global $__LANG,$_LANG,$_EP,$_SITE,$_BASE_PATH;
	$lang_file_path="$_BASE_PATH/sites/$_SITE/ep/$_EP/lang/$_LANG.php";
	if($__LANG==NULL)
	{
		include $lang_file_path;
	/*	echo ">> ";
		echo print_php_code($__LANG);*/
	}
	
	if(xarray_key_exists($str, $__LANG))
		return $__LANG[$str];
	else 
	{
		$__LANG[$str]=$str;
		$__lang_str="<?php 
\$__LANG=".print_php_code($__LANG)."
?>";
		file_put_contents($lang_file_path, $__lang_str);
		return $str;
	}
}

function translate_text(&$thetext)
{
	$arr=Array();
	global $__LANG,$_LANG,$_EP,$_SITE,$_BASE_PATH;
	preg_match_all('#\[t\@(.+)\]#Uis', $thetext,$arr);
	$buf=Array();
	foreach ($arr[0] as $idx => $tag)
	{
		$thestring=$arr[1][$idx];
		$translated='';
		exe_event('translate', Array('lang'=>$_LANG,'string'=>&$translated));
		if($translated=='')
			$buf[$tag]=transl_str($thestring);	
		else 
			$buf[$tag]=$translated;
	}
	$thetext = strtr($thetext,$buf);
}

// get the blockmap for current page and theme and e.p.
function get_block_map($route=NULL,$theme=null,$ep=null)
{
	global $_EP,$_SITE,$_THEME,$_PAGE;
	global $_BLOCKMAP;
	global $_PAGE_ROUTE;
	global $_BASE_PATH;
	if($theme==null) $theme=$_THEME;
	if($ep==null) $ep=$_EP;
	if($route==null) $route=$_PAGE_ROUTE;
//	if($route==null) $route=$_PAGE;
	//echo ">>";
	//var_dump($_BLOCKMAP[$theme][$thepage]);
	include "$_BASE_PATH/sites/$_SITE/ep/$ep/blockmap.php";
	//var_dump($_BLOCKMAP);
	//echo ":: $_BASE_PATH/sites/$_SITE/ep/$_EP/blockmap.php";
	
//echo ">>$thepage";
	$_route=$route;
	$thepage=implode('/', $_route);
	
	$thepage=$_PAGE;
	
	if(!empty($_BLOCKMAP[$theme][$thepage]))
		return $_BLOCKMAP[$theme][$thepage];
	if(!empty($_BLOCKMAP[$theme]["$thepage/*"]))
		return $_BLOCKMAP[$theme]["$thepage/*"];
	for($i=count($route);$i>=0;$i--)
	{
		
		$_route[count($_route)-1]='*';
		$thepage=implode('/', $_route);
		
		if(!empty($_BLOCKMAP[$theme][$thepage]))
			return $_BLOCKMAP[$theme][$thepage];	
		
		unset($_route[count($_route)-1]);
	}
	
}
// push the block $block into the region $reg
function push_block($reg,$block)
{
	global $_BASE_PATH;
	global $_REGIONS;
	if(empty($_REGIONS[$reg]))
		$_REGIONS[$reg][]=$block;
}
// draw the region
function draw_region($reg,$begin='',$end='',$splitter='')
{
	global $_BASE_PATH;
	global $_REGIONS;
	//var_dump($_REGIONS);
	if(empty($_REGIONS[$reg]))
		return '';
	$blocks=Array();
	$i=0;
	foreach ($_REGIONS[$reg] as $idx=>$blck)
	{
		if($i)
			echo $splitter;
		echo $begin;
		echo get_block($blck);
		echo $end;
		$i++;
	}
}

// ���������� �������
function get_template_vars($tpl)
{
	$vars=Array();
	preg_match_all("/\{(.+)\}/Uis",$tpl, $vars);
	$vars=$vars[1];

	return $vars;

}
// ����������� ������ (� ��������� �������)
function x_lookup_template($tpl,$tbl)
{
	$vars=get_template_vars($tpl);
	$brackets=Array();
	foreach($vars as $v)
	{
		$brackets["{".$v."}"]="{".$tbl."_".$v."}";
	}
	return strtr($tbl,$brackets);
}

function template_parse($vars,$tpl)
{
	$br_hash=Array();
	foreach($vars as $k=>$v)
	{
		$br_hash["{".$k."}"]=$v;
	}
	return strtr($tpl,$br_hash);
}

// add the javascript to head
function addjs($_js,$IF=NULL)
{
	global $_BASE_PATH;
	// return if accepted
	if($IF==null)
	{		
		global $_JS;
		if(!in_array($_js, $_JS))
			$_JS[]=$_js;
	}
	else 
	{
		global $_JSIF;
		if(empty($_JSIF[$IF]))
			$_JSIF[$IF]=Array();
		if(!in_array($_js, $_JSIF[$IF]))
			$_JSIF[$IF][]=$_js;
	}
}


function addjsready($_js)
{
	global $_SCRIPT_BLOCKS_READY;
	// return if accepted
	$_SCRIPT_BLOCKS_READY[]=$_js;
	
}

// add the meta keyword
function addmeta($key,$val)
{
	// return if accepted
	global $_HEAD_READY;
	global $_BASE_PATH;
	if($_HEAD_READY) return;
	
	global $_META;
	$_META[$key]=$val;
}
// set title
function title($t)
{
	// return if accepted
	global $_HEAD_READY;
	global $_BASE_PATH;
	if($_HEAD_READY) return;
	
	global $_TITLE;
	$_TITLE=$t;
}
// accept head data
function accept_head()
{
	global $_HEAD_READY;
	global $_BASE_PATH;
	$_HEAD_READY =true;
}
// add script block
function addscript_block($sb,$comment=NULL)
{
	global $_SCRIPT_BLOCKS;
	global $_BASE_PATH;
	if(is_string($comment)) 
		$_SCRIPT_BLOCKS[$comment]=$sb;
	else 
		$_SCRIPT_BLOCKS[]=$sb;
}

// ���� �� ������ � ������
function tpl_in_module($tpl)
{
	global $_CURRMODULE,$_BASE_PATH;
	$_tpl_path = "$_BASE_PATH/modules/$_CURRMODULE";
	return $_tpl_path."/templates/$tpl.php";
}

// ���� �� ������ � ����
function tpl_in_theme($tpl)
{
	global $_BASE_PATH, $_SITE, $_THEME, $_PAGE, $_BLOCK, $_PAGE_FILE_PATH, $_EP;
	$_tpl_path = "$_BASE_PATH/sites/$_SITE/ep/$_EP/themes/$_THEME";
	return $_tpl_path."/templates/$tpl.php";
}

// ���� �� ������ � ����
function tpl_in_ep($tpl)
{
	global $_BASE_PATH, $_SITE, $_THEME, $_PAGE, $_BLOCK, $_PAGE_FILE_PATH, $_EP;
	$_tpl_path = "$_BASE_PATH/sites/$_SITE/ep/$_EP";
	$res = $_tpl_path."/templates/$tpl.php";
	return $res;
}

// ������������ ������
function use_template($tpl,$params=Array())
{
	$tpl_file = '';
	
	if(file_exists(tpl_in_ep($tpl)))
	{
		$tpl_file = tpl_in_ep($tpl);
	}
	else 
	{
		if(file_exists(tpl_in_theme($tpl)))
		{
			$tpl_file =tpl_in_theme($tpl);
		}
	}
	exe_event('before_use_template', Array('file'=>&$tpl_file));
	if($tpl_file!='')
	{
		// �������� ���� ���� �������
		foreach ($params as $key => $val )
		{
			$$key=$val;
		}
		global $_DB,$_PAGE,$_QUERY;	
		include $tpl_file;
	}
}

// ������������ ������
function use_mod_template($tpl,$params=Array(),$retstr=false)
{
	$tpl_file = '';
	
	$tpl_file =tpl_in_module($tpl);
	
	global $_THEME, $_CURRMODULE, $_EP, $_BASE_PATH,$_SITE;
	$redefine_tpl = $_BASE_PATH."/sites/$_SITE/ep/$_EP/themes/$_THEME/templates/mod_".$_CURRMODULE.".$tpl.php";
	//echo $redefine_tpl;
	if(file_exists($redefine_tpl))
	{
		//echo "ReDEf file";
		
		$tpl_file = $redefine_tpl;
	}
	else
//	echo ">>".$redefine_tpl.">>";
	if(file_exists(tpl_in_module($tpl)))
	{
		$tpl_file =tpl_in_module($tpl);
	
	}
	
	
	exe_event('before_use_template', Array('file'=>&$tpl_file));
	if($tpl_file!='')
	{
		// �������� ���� ���� �������
		foreach ($params as $key => $val )
		{
			$$key=$val;
		}
		ob_start();
	//	echo ">> $tpl_file >>";
		include $tpl_file;
		$_BODY = ob_get_contents();
		ob_end_clean();
		if(!$retstr)
			echo $_BODY;
		return $_BODY;
	}
}
?>