<?php 
$_SUCC_MESS='������ ������� ��������.';
$_REDIR_TIMEOUT=0;
$_ACTION_STOPPED=false;

// �������� csrf
function check_csrf()
{
	try {
	
		if(empty($_SESSION['csrf_token'][$_POST['csrf_key']]))
		{
		// if session expired
			//redirect($_SERVER['HTTP_REFERER']); //back to form 
			error403();
			echo ">>SESSION EXPIRED<<";
		}
		elseif($_SESSION['csrf_token'][$_POST['csrf_key']]!=$_POST['csrf_token'])
		{
			error403();
		}
		else 
			unset($_SESSION['csrf_token'][$_POST['csrf_key']]);
	}
	catch (Exception $exc)
	{
		error403();
	}
}
// execute action
function inc_action($act,$json=false)
{
	check_csrf();
	global $_BASE_PATH,$_ACTION_STOPPED,$_DBS,$_DB,$_SITE,$_MODULES,$_PAGE,$_QUERY,$_EP,$_ACT_ERROR,$_SUCC_MESS,$_REDIR_TIMEOUT;
	$_SESSION['actmess']=Array();
	$_SESSION['q_last_post']=$_POST;
	if($_POST[''])
	{
		
	}
	
	$_module='';
	$tail='';
	
	if(get_module($act,$_module,$tail))
	{
	//echo "| $ >> >>";
		
		if(!empty($_MODULES[$_module]) || in_array($_module,$_MODULES))
		{
			//echo ">> $page ? $_module : $tail>>";
			$_FILEPATH_HEAD=$_BASE_PATH."/modules/$_module/ep/$_EP"; // ���� � ����� ������ � ������
			$act=$tail;
				
		}
		else
			$_FILEPATH_HEAD=$_BASE_PATH."/sites/$_SITE/ep/$_EP"; // ���� � ����� ������
	}
	else
	{
		$_FILEPATH_HEAD=$_BASE_PATH."/sites/$_SITE/ep/$_EP"; // ���� � ����� ������
	}
	
	if($json)
	{
		ob_start();

		if(folder_exists("$_FILEPATH_HEAD/actions/$act"))
		{
			include "$_FILEPATH_HEAD/actions/$act/index.php";
		}
		else
		{
			include "$_FILEPATH_HEAD/actions/$act.php";
		}
		$thebody = ob_get_contents();
		ob_end_clean();

		//return 
		echo json_encode(
				Array(
						'body'=>$thebody,
						'messages'=>$_TITLE,
						'redirect'=>$_JS,						
				)
		);
	}
	else
	{
	/*	ob_start();*/


		if(folder_exists("$_FILEPATH_HEAD/actions/$act"))
		{
			include "$_FILEPATH_HEAD/actions/$act/index.php";
		}
		else
		{
			include "$_FILEPATH_HEAD/actions/$act.php";
		}
	/*	$thebody = ob_get_contents();
		ob_end_clean();*/
	//	return $thebody;
	}

	if(!$_ACTION_STOPPED)
	{
		if($_ACT_ERROR)	// �������� ����������� � �������
		{
			redirect($_POST['urlfrom']);
		}
		else 
		{
			global $_REDIR_TIMEOUT;
			unset($_SESSION['q_last_post']);
			if($_REDIR_TIMEOUT)
			{		
				echo get_page('redirect',false,Array(
						'URL'=>$_POST['urlto'],
						'MESS'=>$_SUCC_MESS,
						'TIMEOUT'=>$_REDIR_TIMEOUT,
						)
					);
			}
			else
				redirect($_POST['urlto']);
		}
	}
	exit();
}
// ���������� � �������
function inc_srv($act,$json=false)
{
	global $_BASE_PATH,$_ACTION_STOPPED,$_DBS,$_DB,$_SITE,$_MODULES,$_PAGE,$_QUERY,$_EP,$_ACT_ERROR,$_SUCC_MESS,$_REDIR_TIMEOUT;
	$_SESSION['actmess']=Array();
	$_SESSION['q_last_post']=$_POST;

	$_module='';
	$tail='';

	if(get_module($act,$_module,$tail))
	{
		//echo "| $ >> >>";

		if(!empty($_MODULES[$_module]) || in_array($_module,$_MODULES))
		{
			//echo ">> $page ? $_module : $tail>>";
			$_FILEPATH_HEAD=$_BASE_PATH."/modules/$_module"; // ���� � ����� ������ � ������
			$act=$tail;

		}
		else
			$_FILEPATH_HEAD=$_BASE_PATH."/sites/"; // ���� � ����� ������
	}
	else
	{
		$_FILEPATH_HEAD=$_BASE_PATH."/sites/"; // ���� � ����� ������
	}

	if($json)
	{
		ob_start();

		if(folder_exists("$_FILEPATH_HEAD/services/$act"))
		{
			include "$_FILEPATH_HEAD/services/$act/index.php";
		}
		else
		{
			include "$_FILEPATH_HEAD/services/$act.php";
		}
		$thebody = ob_get_contents();
		ob_end_clean();

		//return
		echo json_encode(
				Array(
						'body'=>$thebody,
						'messages'=>$_TITLE,						
				)
		);
	}
	else
	{
		/*	ob_start();*/


		if(folder_exists("$_FILEPATH_HEAD/services/$act"))
		{
			include "$_FILEPATH_HEAD/services/$act/index.php";
		}
		else
		{
			include "$_FILEPATH_HEAD/services/$act.php";
		}
		/*	$thebody = ob_get_contents();
		 ob_end_clean();*/
		//	return $thebody;
	}
	
	exit();
}

function stop_action()
{
	global $_ACTION_STOPPED;
	$_ACTION_STOPPED=true;
}

function getref($ref)
{
	global $_PSEUDOFOLDER;
	if($_PSEUDOFOLDER)
		return $ref;
	else 
		return "index.php?q=$ref";
}

function last_post_val($varname)
{
	global $_BASE_PATH;
	if(empty($_SESSION['q_last_post'][$varname]))
		return '';
	return $_SESSION['q_last_post'][$varname];
}

function set_lpv($varname,$val)
{
	global $_BASE_PATH;
	if(empty($_SESSION['q_last_post']))
		$_SESSION['q_last_post']=Array();
	$_SESSION['q_last_post'][$varname]=$val;
}

// ������ ������� � �������
function serv_url($srv,$addparams=null)
{
	global $_BASE_PATH;
	$str=$_BASE_PATH."/index.php?srv=$srv";
	if($addparams!=null)
	{
		if(is_string($addparams))
			$str="$str&$addparams";
		else 
			$str=$str."&".hash2url($addparams);
	}
	return $str;
}

// begin form
function form_begin($act,$opts=null,$redir_url=null)
{
	global $_BASE_PATH;
	xdefarray(Array('class'=>'','confirm'=>false),$opts);
	if($opts['confirm']!=false)
	{
		jqready_gather("
			\$('.".$opts['class']."').submit(function() {
		if(confirm('".$opts['confirm']."'))
				return true;
			return false;
		}
	);
							
"); 
	}
	$_class="";
	if($frmclass=='')
		$_class=" class=\"$frmclass\"";
	global $_URL_BASE;
	
	if($_URL_BASE[0]!='/')
		$_URL_BASE="/$_URL_BASE";
	if($_URL_BASE[strlen($_URL_BASE)-1]!='/')
		$_URL_BASE="$_URL_BASE/";
	
	$csrf = randstr();
	$csrf_key = randstr();

	$_SESSION['csrf_token'][$csrf_key]=$csrf;
	
	
	echo "<form method=\"post\" class=\"form-horizontal ".$opts['class']."\" action=\"{$_URL_BASE}index.php?act=$act\">";
	echo "<input type=\"hidden\" name=\"urlfrom\" value=\"".$_SERVER['REQUEST_URI']."\" />";
	echo "<input type=\"hidden\" name=\"csrf_key\" value=\"$csrf_key\" />";
	echo "<input type=\"hidden\" name=\"csrf_token\" value=\"$csrf\" />";
	if($redir_url==null)
	{
		echo "<input type=\"hidden\" name=\"urlto\" value=\"".$_SERVER['REQUEST_URI']."\" />";
	}
	else 
	{
		echo "<input type=\"hidden\" name=\"urlto\" value=\"$redir_url\" />";
	}
}

function form_end()
{
	echo "</form>";
}
// get the form with name
function get_form($_form,$_PARAMS=null,$json=false)
{
	global $_BASE_PATH,$_SITE,$_DB,$_OBJSTACK,$_PAGE, $_QUERY, $_EP, $_FORM_FILE_PATH,$_MODULES;
	
	$_OBJSTACK[count($_OBJSTACK)-1]=Array('type'=>'form','path'=>$_FORM_FILE_PATH);
	
	if($json)
	{
		ob_start();
	
		$_module='';
		$tail='';
		
		if(get_module($_form,$_module,$tail))
		{
			//	echo ">> $_page : $_module : $tail >>";
			if(!empty($_MODULES[$_module]) || in_array($_module,$_MODULES))
			{
				global $_CURRMODULE;
				$_CURRMODULE=$_module;
				$_FILEPATH_HEAD=$_BASE_PATH."/modules/$_module/ep/$_EP"; // ���� � ����� ������ � ������
				$_form=$tail;
			}
			else
				$_FILEPATH_HEAD=$_BASE_PATH."/sites/$_SITE/ep/$_EP";
		}
		else
		{
			$_FILEPATH_HEAD=$_BASE_PATH."/sites/$_SITE/ep/$_EP";
		}
		if(folder_exists("$_FILEPATH_HEAD/forms/$_form"))
		{
			$_FORM_FILE_PATH = "$_FILEPATH_HEAD/forms/$_form/index.php";
				
		}
		else
		{
			$_FORM_FILE_PATH ="$_FILEPATH_HEAD/forms/$_form.php";
		}
		
		ob_start();
		include $_FORM_FILE_PATH;
		$thebody = ob_get_contents();
		ob_end_clean();
		
		unset($_OBJSTACK[count($_OBJSTACK)-1]);
		return json_encode(
				Array(
						'body'=>$thebody,
						'title'=>$_TITLE,
						'js'=>$_JS,
						'css'=>$_CSS,
						'meta'=>$_META,
						'jsforms'=>$_SCRIPT_forms,
						'jsif'=>$_JSIF,
				)
		);
	}
	else
	{
		$_module='';
		$tail='';
		
		if(get_module($_form,$_module,$tail))
		{
			//	echo ">> $_page : $_module : $tail >>";
			if(!empty($_MODULES[$_module]) || in_array($_module,$_MODULES))
			{
				global $_CURRMODULE;
				$_CURRMODULE=$_module;
				$_FILEPATH_HEAD=$_BASE_PATH."/modules/$_module/ep/$_EP"; // ���� � ����� ������ � ������
				$_form=$tail;
			}
			else 
				$_FILEPATH_HEAD=$_BASE_PATH."/sites/$_SITE/ep/$_EP";
		}
		else 
		{
			$_FILEPATH_HEAD=$_BASE_PATH."/sites/$_SITE/ep/$_EP";
		}
		if(folder_exists("$_FILEPATH_HEAD/forms/$_form"))
		{
			$_FORM_FILE_PATH = "$_FILEPATH_HEAD/forms/$_form/index.php";
			
		}
		else
		{
			$_FORM_FILE_PATH ="$_FILEPATH_HEAD/forms/$_form.php";
		}
		
		ob_start();
		include $_FORM_FILE_PATH;		
		$thebody = ob_get_contents();
		ob_end_clean();
		$_FORM_FILE_PATH='';
		unset($_OBJSTACK[count($_OBJSTACK)-1]);
		return $thebody;
	}
}

function error404()
{
	ob_end_clean();
	header("HTTP/1.0 404 Not Found");
	global $_BASE_PATH;
	include "$_BASE_PATH/errors/404.php";
	exit;
}

function error403()
{	
	ob_end_clean();
	header("HTTP/1.0 403 Access denied");
	global $_BASE_PATH;
	include "$_BASE_PATH/errors/403.php";
	exit;
}
// get the view
function get_view($_view,$_PARAMS=null,$json=false)
{
	global $_BASE_PATH,$_SITE,$_DB,$_OBJSTACK,$_MODULES;
	global $_PAGE;
	global $_QUERY;
	global $_EP;
	global $_VIEW_FILE_PATH;
	$_OBJSTACK[count($_OBJSTACK)-1]=Array('type'=>'view','path'=>$_VIEW_FILE_PATH);
	
	ob_start();
	$_module='';
	$tail='';
	if(get_module($_view,$_module,$tail))
	{
		
			if(!empty($_MODULES[$_module]) || in_array($_module,$_MODULES))
			{
				global $_CURRMODULE;
				$_CURRMODULE=$_module;
				$_FILEPATH_HEAD=$_BASE_PATH."/modules/$_module/ep/$_EP"; // ���� � ����� ������ � ������
				$_view=$tail;
			}
			else 
				$_FILEPATH_HEAD=$_BASE_PATH."/sites/$_SITE/ep/$_EP"; // ���� � ����� ������				
	}
	else
	{
			$_FILEPATH_HEAD=$_BASE_PATH."/sites/$_SITE/ep/$_EP"; // ���� � ����� ������
	}
	
	if(folder_exists("$_FILEPATH_HEAD/views/$_view"))
	{
		$_VIEW_FILE_PATH ="$_FILEPATH_HEAD/views/$_view/index.php";
	}
	else
	{
		$_VIEW_FILE_PATH ="$_FILEPATH_HEAD/views/$_view.php";
			
	}
		
	include $_VIEW_FILE_PATH;
		
	$thebody = ob_get_contents();
	ob_end_clean();
	$_VIEW_FILE_PATH='';
	unset($_OBJSTACK[count($_OBJSTACK)-1]);
	if($json)
	{
		return json_encode(
			Array(
						'body'=>$thebody,
						'title'=>$_TITLE,
						'js'=>$_JS,
						'css'=>$_CSS,
						'meta'=>$_META,
						'jsviews'=>$_SCRIPT_views,
						'jsif'=>$_JSIF,
			)
		);
	}
	else
	{
			
		return $thebody;
	}
}
?>