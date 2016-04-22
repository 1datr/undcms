<?php 
$_SITE='default';
$_PAGE = "index";
$_QUERY=Array();
$_EP='front';	// entry point
$_THEME='default';
$_VARBUF=Array();
$_BLOCKMAP=Array();
$_PAGE_FILE_PATH='';
$_DBS =null;
$_LANGS=Array();
// язык по умолчанию
$_DEF_LANG="eng";
$_LANG=$_DEF_LANG;
$__LANG=NULL;

$_DB=null;
$_DBID=null;
$_MODULES=Array();
$_CRUD=true;
$_URL_BASE='';
$_CURRMODULE='';
$_BLOCK_FILE_PATH='';
$_VIEW_FILE_PATH='';
$_FORM_FILE_PATH='';
$_OBJSTACK=Array();

$_MODULES_INFO=Array();

require_once  dirName(__FILE__).'/utils.php';
require_once  dirName(__FILE__).'/template.php';
require_once  dirName(__FILE__).'/primitives.php';
require_once  dirName(__FILE__).'/cache.php';
require_once  dirName(__FILE__).'/stdroles.php';
require_once  dirName(__FILE__).'/op.php';
if($_CRUD)
	require_once  dirName(__FILE__).'/crud.php';
// load the modules
function load_modules()
{
	try{
		GLOBAL $_MODULES,$_BASE_PATH;
		foreach($_MODULES as $modkey => $modval)
		{
			if(is_string($modkey))
			{
				$_PARAMS=$modval;
				require_once "$_BASE_PATH/modules/$modkey/index.php";
				
			}
			else 
			{
				$_PARAMS=Array();
				require_once "$_BASE_PATH/modules/$modval/index.php";
				//echo "$_BASE_PATH/modules/$modval/index.php";
			}
		}
	}
	catch(Exception $ex)
	{
		echo "<div class=\"alert alert-danger\">".$ex->getMessage()."</div>";
	}
}

function call_mod_func($mod,$fun,$params=null)
{
	
}

function init_module($modname,$minfo=null)
{
	global $_MODULES_INFO;
	
	if(empty($minfo))
	{		
		$minfo=Array();
		def_init_module($modname,$minfo);
	}
	$_MODULES_INFO[$modname]=$minfo;
} 	
//default init module procedure
function def_init_module($_mod,&$minfo)
{
	global $_BASE_PATH,$_MODULES,$_EP,$_SITE,$_MODULES_INFO;
	if(folder_exists($_BASE_PATH."/modules/$_mod/ep/$_EP"))
	{		
		$eptree=dirtree($_BASE_PATH."/modules/$_mod/ep/$_EP");
	//	var_dump($eptree);
		$_blocks=Array();
		foreach ($eptree['blocks'] as $thefile)
		{
			$_blocks[]=$thefile;
		}
		
		$_MODULES_INFO["mod_$_mod"]=Array('blocks'=>$_blocks);
	}
}

// select database
function select_db_profile($dbid='default')
{
	try{
		global $_DB,$_DBID,$_SITE,$_CRUD,$_MODULES;
		$_DBID=$dbid;
		if(empty($_DBS[$dbid]))
		{		
			global $_DBS;
			global $_PROFILES;
			$GLOBALS['_LANGS']=$_PROFILES[$dbid]['langs'];
			$GLOBALS['_CURR_LANGUAGE']=$_PROFILES[$dbid]['currlang'];
			$_DBS[$dbid]=new db($_PROFILES[$dbid]['connection']);
		}
		$_DB=$_DBS[$dbid];
		
		global $_BASE_PATH,$_MODULES;
		
		$file_custom=$_BASE_PATH."/sites/$_SITE/db/$_DBID/custom.php";
		$file_ser=$_BASE_PATH."/sites/$_SITE/db/$_DBID/db.ser";	
				
		$t_custom=filemtime($file_custom);
		
		
		if($_DB->scheme->_DRV->_DBCREATED)
		{
			// �������� � ��������������
			include $_BASE_PATH."/sites/$_SITE/db/$_DBID/dbbase.php";
			include $file_custom;
			//	echo ">>$file_ser>>";
			// проход по модулям за структурой данных
			exe_event('dbinit', Array('db'=>&$_DB));
			$_DB->scheme->dbcommit();
			$_DB->scheme->export($file_ser);
			exe_event('after_dbinit', Array('db'=>&$_DB));
			//echo "::MAKE CRUDS::";
			make_cruds();
			
		}		
		else
		{
			if(file_exists($file_ser))	// ���� ��������������� ����
			{
					
					
				$t_ser=filemtime($file_ser);
				if($t_custom>$t_ser)	// ������� ������ ������
				{
					$_DB->scheme->import($file_ser);
					include $file_custom;
					// проход по модулям за структурой данных
					exe_event('dbinit', Array('db'=>&$_DB));				
					
					$_DB->scheme->dbcommit();
					$_DB->scheme->export($file_ser);
					exe_event('after_dbinit', Array('db'=>&$_DB));
					//echo "::MAKE CRUDS::";
					make_cruds();
				}
				else
				{
					$_DB->scheme->import($file_ser);
					// проход по модулям за структурой данных
					exe_event('dbinit', Array('db'=>&$_DB));
					if($_DB->scheme->changed())
					{
							
						$_DB->scheme->dbcommit();
						$_DB->scheme->export($file_ser);
						exe_event('after_dbinit', Array('db'=>&$_DB));
						//echo "::MAKE CRUDS::";
						make_cruds();
					}
				}
			}
			else
			{
					
				// �������� � ��������������
				include $_BASE_PATH."/sites/$_SITE/db/$_DBID/dbbase.php";
				include $file_custom;
				//	echo ">>$file_ser>>";
				// проход по модулям за структурой данных
				exe_event('dbinit', Array('db'=>&$_DB));
				$_DB->scheme->dbcommit();
				$_DB->scheme->export($file_ser);
				exe_event('after_dbinit', Array('db'=>&$_DB));
				//echo "::MAKE CRUDS::";
				make_cruds();
			}
		}
		
		//var_dump($_DB);
	}
	catch(Exception $ex)
	{
		echo "<div class=\"alert alert-danger\">".$ex->getMessage()."</div>";
	}
}
// ������� �����
function make_cruds()
{
	global $_DB,$_CRUD,$_SITE,$_EP,$_BASE_PATH, $def_addata;
	if(!$_CRUD) return;
	$controllers=Array();
	$cons=Array();
	foreach($_DB->scheme->_SCHEME as $tbl => $obj)
	{
		if($tbl=='group')
		{
			$u=2;
		}
		
		$CI = new ConInfo($obj->_ADDATA,$tbl);
		if(xarray_key_exists($CI->name, $cons))
			$cons[$CI->name]->attach($CI);
		else
			$cons[$CI->name]=$CI;		
	}	
	//  создаем объекты из генераторов
	foreach ($cons as $conname => $con)
	{
		$res=$con->get_primitives();
		
		
		foreach ($res as $prim_type => $prim_list)
		{
			foreach ($prim_list as $fname => $code)
			{
				xfile_put_contents("$fname.php",$code);
			}
		}
		
		
	}		
}
// load the library
function load_libs($liblist)
{
	global $_BASE_PATH;
	foreach($liblist as $lib)
	{
		if(folder_exists("$_BASE_PATH/lib/$lib"))
		{
			require_once "$_BASE_PATH/lib/$lib/index.php";
		}
		else 
			require_once "$_BASE_PATH/lib/$lib.php";
	}
}

function load_components($comlist)
{
	global $_BASE_PATH;
	foreach($comlist as $com)
	{
		require_once "$_BASE_PATH/components/$com/index.php";
	}
}

// load the page
function get_page($_page=NULL,$json=false,$_PARAMS=Null)
{	
	global $_PAGE,$_QUERY,$_EP,$_PAGE_FILE_PATH,$_THEME,$_SITE,$_MODULES;
	global $_BASE_PATH;
	global $_DBS, $_DB;
	if($_page==NULL)
		$_page=$_PAGE;
	$loadpage=false;
	
	//echo "./ep/$_EP/index.php";
	$_module='';
	$tail='';
//	echo "PAGE: $_page";
	if(get_module($_page,$_module,$tail))
	{
	//	echo ">> $_page : $_module : $tail >>";
		if(!empty($_MODULES[$_module]) || in_array($_module,$_MODULES))
		{
			global $_CURRMODULE;
			$_CURRMODULE=$_module;
			$_FILEPATH_HEAD=$_BASE_PATH."/modules/$_module/ep/$_EP"; // ���� � ����� ������ � ������
			$_page=$tail;
		}
		else
			$_FILEPATH_HEAD=$_BASE_PATH."/sites/$_SITE/ep/$_EP"; // ���� � ����� ������
	}
	else
	{
		$_FILEPATH_HEAD=$_BASE_PATH."/sites/$_SITE/ep/$_EP"; // ���� � ����� ������
	}
	//
	if(folder_exists("$_FILEPATH_HEAD/pages/$_page"))
		{
			$_PAGE_FILE_PATH=  "$_FILEPATH_HEAD/pages/$_page/index.php";
			$loadpage=true;
		}
	else
	{
		$_PAGE_FILE_PATH="$_FILEPATH_HEAD/pages/$_page.php";
		$loadpage=true;
	}
	
	if($loadpage)
	{
		$_PREFIX='';
		exe_event('before_make_page', Array('PREFIX'=>&$_PREFIX ));
		global $_BLOCKMAP;
		global $_REGIONS;
		include "$_BASE_PATH/sites/$_SITE/ep/$_EP/index.php";		
		
		$_REGIONS = get_block_map();
	//	var_dump($_REGIONS);
	
		// include the page
		$_PAGE_BODY_PREFIX = '';
		// ������� ����� ������� ���� ��������
		exe_event('before_get_page', Array('PREFIX'=>&$_PAGE_PREFIX, 'PAGE'=>$_PAGE ));
		// ������� ����� ������ ���� ��������
		ob_start();
		include $_PAGE_FILE_PATH;
		$_R_MAIN = ob_get_contents();
		ob_end_clean();
		
		exe_event('after_get_page', Array('BODY'=>&$_R_MAIN));
		
		$_R_MAIN=$_PAGE_BODY_PREFIX.$_R_MAIN;
		// process the theme
		// ���� �� �����������
		ob_start();
		$in_theme_page =strtr($_page,Array('/'=>'_'));
		if($in_theme_page[0] =='_')
			$in_theme_page=substr($in_theme_page,1);
		if(file_exists("$_BASE_PATH/sites/$_SITE/ep/$_EP/themes/$_THEME/_$in_theme_page.php"))
			include "$_BASE_PATH/sites/$_SITE/ep/$_EP/themes/$_THEME/_$in_theme_page.php";
		else 
			include  "$_BASE_PATH/sites/$_SITE/ep/$_EP/themes/$_THEME/index.php";
		$thebody = ob_get_contents();
		ob_end_clean();
		
		global $_CSS;
		global $_JS;
		global $_JSIF;
		global $_META;
		global $_SCRIPT_BLOCKS;
		global $_TITLE;
		
		exe_event('before_getpage',
			Array(
				'step'=>'before_drawpage',
			)
		);
		
		// fill all regions by blocks
		if($json)
		{
			return json_encode(
					Array(
							'body'=>$thebody,
							'title'=>$_TITLE,
							'js'=>$_JS,
							'css'=>$_CSS,
							'meta'=>$_META,
							'jsblocks'=>$_SCRIPT_BLOCKS,
							'jsif'=>$_JSIF,
					)
			);
		}
		else
		{		
			$html='
			<!DOCTYPE unspecified PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
			<head>
			';
			
			foreach($_CSS as $_css)
			{
				$html=$html."<LINK href=\"$_css\" type=text/css rel=stylesheet></LINK>
";		
			}
			
			
			foreach($_JS as $_js)
			{
				$html=$html."<script type=\"text/javascript\" src=\"$_js\"></script>
";
			}
			
			
			
			foreach($_JSIF as $IF => $jslist)
			{
			
			$html=$html."<!--[if lt <?php echo $IF; ?>]>
";
				 
				foreach($jslist as $_js)
				{
			
					$html=$html."<script type=\"text/javascript\" src=\"$_js\"></script>
";
			
				}
				     		
	    	$html=$html."<![endif]-->
";		
			}
			
			foreach($_META as $_key => $_val)
			{
				$html=$html."<meta http-equiv=\"$_key\" content=\"$_val\" />
";
			}
			
			foreach($_SCRIPT_BLOCKS as $idx => $sb)
			{
				if(is_string($idx)) // comment
					$html=$html."<!-- $idx -->";
				$html=$html."<script type=\"text/javascript\">
";
				$html=$html.$sb;
				$html=$html. "</script>
";
			}
			
			global $_SCR_READY_HEAD,$_SCR_READY_END,$_SCRIPT_BLOCKS_READY;
			$html=$html."<script type=\"text/javascript\">
					$_SCR_READY_HEAD
";
			$html=$html.implode("
", $_SCRIPT_BLOCKS_READY);
			$html=$html. "
$_SCR_READY_END					
</script>";
			
			$html=$html."<title> $_TITLE </title>
			</head>
";
			$html=$html."
			<body>
			$thebody		
			</body>
			";
			exe_event('after_make_page',
				Array(
					'html'=>&$html,
					'page'=>$_PAGE,					
				));
			translate_text($html);
			return $_PREFIX.$html;
		}
	}
}


// load the page

// push to array in begin
function push_element(&$arr,$val,$idx=NULL)
{
	global $_BASE_PATH;
	if($idx==NULL)
	{
		$newarray=Array();
		$newarray[0]=$val;
		$idx=1;
		foreach($arr as $i => $v)
		{
			if(is_int($i))
			{
				$newarray[$idx]=$v;
				
			}
			else 
			{
				$newarray[$i]=$v;
			}
			$idx++;
		}
		
	}
	else 
	{
		$newarray=Array();
		$newarray[$idx]=$val;
		$idx=1;
		foreach($arr as $i => $v)
		{
			if(is_int($i))
			{
				$newarray[$idx]=$v;
		
			}
			else
			{
				$newarray[$i]=$v;
			}
			$idx++;
		}
	}
	$arr = $newarray;
}

function signature($siginfo)
{
	/** 
	 * 				/id:integer|0/ 
	 * */
	
	global $_QUERY;
	if(is_string($siginfo))
	{
		$_siginfo=explode('/',$siginfo);
		$_z_array=Array();
		foreach($_siginfo as $str)
		{
			$_z=Array();
			$_z['varname']=$str;
			$epl1=explode(':',$str);
			if(count($epl1)>1)
			{
				$_z['varname']=$epl1[0];
				$_z['type']=$epl1[1];
				$epl2=explode('|',$epl1[1]);
				if(count($epl2)>1)
				{
					$_z['type']=$epl2[0];
					$_z['defval']=$epl2[1];
				}
			}
			$_z_array[]=$_z;
		}
		$siginfo=$_z_array;
	}
//	var_dump($siginfo);
	try
	{
		
		foreach($siginfo as $idx=> $sig)
		{
		//	echo "::$idx";
			//var_dump($sig);
			
			if(array_key_exists($sig['varname'],$_QUERY))
			{
				// allready there is this key
				
			}	
			elseif(array_key_exists($idx,$_QUERY))
			{
				switch($sig['type'])
				{
					case 'int':
					case 'integer':$_QUERY[$sig['varname']]=( int )$_QUERY[$idx]; break;
					case 'bool':$_QUERY[$sig['varname']]=( bool )$_QUERY[$idx]; break;
					default: $_QUERY[$sig['varname']]=$_QUERY[$idx];
				}
				
				unset($_QUERY[$idx]);
			
			}
			elseif(array_key_exists('defval',$sig))
			{
				$_QUERY[$sig['varname']]=$sig['defval'];
	
			}
			else 
			{
				return false;
			}
		}
	} 
	catch (Exception $ex)
	{
		return false;
	}
	
	return true;
}

$_PAGE_ROUTE=Array();
// get route
function detect_route($querystr=NULL)
{
	global $_BASE_PATH;
//	var_dump($_GET);
	//var_dump($_POST);
	if(!empty($_GET['act'])) // the action query
	{
	
		inc_action($_GET['act']);
	}
	elseif(!empty($_GET['srv'])) // the service query
	{
		inc_srv($_GET['srv']);
	}
	elseif(!empty($_GET['q']))
	{
		global $_EP;
		global $_PAGE_ROUTE;
		if($querystr==NULL)
			$querystr =$_GET['q'];
		$pieces=Array();
	
		$qpieces = preg_split("#[\\/]{1}#", $querystr);
		
		$pieces_x =Array();
		$page="./";
		$queries=Array();
		$page_detected=false;
		//var_dump($qpieces);
		
		$pieces_x = $qpieces;
		$queries=Array();
		// detect the page
		for($i=count($qpieces)-1;$i>=0;$i--)
		{		
				$page="./".implode('/',$pieces_x);
				$_PAGE_ROUTE = $pieces_x;
				foreach($_PAGE_ROUTE as $idx => $val)
				{
					if($val=='')
						unset($_PAGE_ROUTE[$idx]);
				}
					//echo ">>".$page;
				$_q_splices =explode(':',$pieces_x[$i]);
				//var_dump($_q_splices);
				if(count($_q_splices)>1) // parameter is variable as var1:val1
				{
					//
					$var = $_q_splices[0];
					$val=$_q_splices[1];
					if(count($_q_splices)>2)
					{
						unset($_q_splices[0]);
						$val=implode(':',$_q_splices);
					}				
					push_element($queries,$val,$var);
					unset($pieces_x[$i]);
				}
				else
				{
					//$_queries[$idx]=$q;
				//	echo ">> $page >>";
					if(page_exists($page))
					{
						//echo ">> $page >>";
						$page_detected=true;
						break;
					}
					else
					{					
						push_element($queries,$qpieces[$i]);
						unset($pieces_x[$i]);
					}
				}	
				
				
		}
		
		global $_PAGE;
		global $_QUERY;
		// make queries array 
		$_PAGE=$page;
		$_QUERY=$queries;
		
		$qkeys=array_keys($_QUERY);
		foreach ($qkeys as $idx => $val)
		{
			if($_QUERY[$val]=='')
				unset($_QUERY[$val]);
		}
		
/*		echo "page:$_PAGE";
		var_dump($_QUERY);*/
	}
	
}

// load the entrypoint
function load_ep($the_ep)
{
	session_id ($the_ep.'sid');
	session_start();
	global $_LANG, $_DEF_LANG;
	if(empty($_SESSION['_LANG']))
	{
		$_SESSION['_LANG']=$_LANG;
	}
	else
	{
		$_LANG=$_SESSION['_LANG'];
	}
	global $_EP;
	global $_BASE_PATH,$_SITE,$_MODULES;
	$_EP=$the_ep;
	
	include $_BASE_PATH."/sites/$_SITE/ep/$_EP/index.php";
	load_modules();
}

function init_site($site)
{
	global $_BASE_PATH,$_SITE,$_MODULES;
	$_SITE=$site;
	include $_BASE_PATH."/sites/$_SITE/index.php";
}
// check if page exists
function page_exists($page)
{
	$dummy_pages=Array('303','404','redirect');
	
	if($page=="./")
		$page="index";
	if($page=="")
		$page="index";
	global $_EP,$_BASE_PATH,$_SITE,$_MODULES;
	$_module='';
	$tail='';
	
	if(get_module($page,$_module,$tail))
	{
		
		if(!empty($_MODULES[$_module]) || in_array($_module,$_MODULES))
		{
		//echo ">> $page ? $_module : $tail>>";
			$_FILEPATH_HEAD=$_BASE_PATH."/modules/$_module/ep/$_EP"; // ���� � ����� ������ � ������
			$page=$tail;
			
		}
		else
			$_FILEPATH_HEAD=$_BASE_PATH."/sites/$_SITE/ep/$_EP"; // ���� � ����� ������
	}
	else
	{
		$_FILEPATH_HEAD=$_BASE_PATH."/sites/$_SITE/ep/$_EP"; // ���� � ����� ������
	}
	if(in_array($page, $dummy_pages))
		return true;
	// echo ">> $_FILEPATH_HEAD/pages/$page.php >>";
	if(folder_exists("$_FILEPATH_HEAD/pages/$page"))
	{
		if(file_exists("$_FILEPATH_HEAD/pages/$page/index.php"))
			return true;
		else 
			return false;
	}
	elseif(file_exists("$_FILEPATH_HEAD/pages/$page.php")) 
	{
		return true;
	}
	return false;
}

// check if block exists
function block_exists($block)
{
	global $_BASE_PATH,$_SITE,$_EP,$_MODULES;
	if($page=="./")
		$page="index";	
	$_module='';
	$tail='';
	if(get_module($block,$_module,$tail))
	{
		if(!empty($_MODULES[$_module]) || in_array($_module,$_MODULES))
		{
			$_FILEPATH_HEAD=$_BASE_PATH."/modules/$_module/ep/$_EP"; // ���� � ����� ������ � ������
			$block=$tail;
		}
		else
			$_FILEPATH_HEAD=$_BASE_PATH."/sites/$_SITE/ep/$_EP"; // ���� � ����� ������
	}
	else
	{
		$_FILEPATH_HEAD=$_BASE_PATH."/sites/$_SITE/ep/$_EP"; // ���� � ����� ������
	}
	
	if(folder_exists("$_FILEPATH_HEAD/blocks/$block"))
	{
		if(file_exists("$_FILEPATH_HEAD/blocks/$block/index.php"))
			return true;
		else 
			return false;
	}
	elseif(file_exists("$_FILEPATH_HEAD/blocks/$block.php")) 
	{
		return true;
	}
	return false;
}
// include the block
function load_block($blck,$json=false)
{
	echo get_block($blck,$json);
}


// get the block code
function get_block($blck,$json=false)
{
	global $_BASE_PATH,$_SITE,$_PAGE,$_QUERY,$_MODULES,$_EP,$_BLOCK_FILE_PATH,$_DB;
	$the_block = $blck;
	$_module='';
	$tail='';
	require_once "$_BASE_PATH/sites/$_SITE/index.php";
	if(get_module($blck,$_module,$tail))
	{
		
		if(!empty($_MODULES[$_module]) || in_array($_module,$_MODULES))	
		{		
			global $_CURRMODULE;
			$_CURRMODULE=$_module;
			$_FILEPATH_HEAD=$_BASE_PATH."/modules/$_module/ep/$_EP"; // ���� � ����� ������ � ������
			$blck=$tail;
		}
		else
			$_FILEPATH_HEAD=$_BASE_PATH."/sites/$_SITE/ep/$_EP"; // ���� � ����� ������
	}
	else
	{
		$_FILEPATH_HEAD=$_BASE_PATH."/sites/$_SITE/ep/$_EP"; // ���� � ����� ������
	}
	
	
		$blck_prefix ="";
		exe_event('before_get_block',Array('PREFIX'=>&$blck_prefix,'BLOCKNAME'=>$blck));
		
		ob_start();
		
		
		if(folder_exists("$_FILEPATH_HEAD/blocks/$blck"))
		{
			$_BLOCK_FILE_PATH=$_FILEPATH_HEAD."/blocks/$blck/index.php";
			include $_BLOCK_FILE_PATH;
		}
		else
		{
			$_BLOCK_FILE_PATH=$_FILEPATH_HEAD."/blocks/$blck.php";
			include $_BLOCK_FILE_PATH;
		}
		
		$thebody = ob_get_contents();
		ob_end_clean();
		
		$thebody = $blck_prefix. $thebody ;
		
		exe_event('after_get_block',Array('BODY'=>&$thebody,'BLOCKNAME'=>$the_block));
		if($json)
		{	
			return json_encode(
					Array(
							'body'=>$thebody,
							'title'=>$_TITLE,
							'js'=>$_JS,
							'css'=>$_CSS,
							'meta'=>$_META,
							'jsblocks'=>$_SCRIPT_BLOCKS,
							'jsif'=>$_JSIF,
					)
			);
		}
		else 
		{
			return $thebody;
		}
	
		return $thebody;
}
function make_query_params($signatures)
{
	global $_BASE_PATH;
	global $_QUERY;
	global $_PAGE;
	global $_EP;
	foreach ($_QUERY as $idx => $val)
	{
		
	}
}
// load the theme
function load_theme($theme=null,$page=null)
{
	global $_BASE_PATH;
	global $_QUERY;
	global $_PAGE;
	global $_EP;
	global $_THEME;
	if($theme==null)
		$theme=$_THEME;
	if($page==null)
		$page=$_PAGE;
}
?>