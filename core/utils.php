<?php 
$_ACT_ERROR = false;

function folder_exists($dir)
{
	if($dir[strlen($dir)-1]!='/')
		$dir="$dir/";
	return file_exists($dir) && is_dir($dir);
}

function xfile_put_contents($filename,$text)
{
	$filename=strtr($filename,Array("\\"=>"/"));
	$pieces=explode('/', $filename);
	$cdir=Array();
	for($i=0;$i<count($pieces);$i++)
	{
		$cdir[]=$pieces[$i];
		$cpath=implode('/',$cdir);
		if($i==count($pieces)-1)
		{
			file_put_contents($cpath, $text);
			//chmod($cpath, 0755);
		}
		else 
		{
			
			if(!folder_exists($cpath))
			{
				mkdir($cpath,0755);
			}
		}
	}
}

function print_php_code($var)
{
	$str='';
	switch(gettype($var))
	{
		case "boolean":
			if($var)
				$str.="TRUE";
			else 
				$str.="FALSE";
			break;
		case "integer":
		case "double" :
			$str.="$var";
			break;
		case "string":
			$str.="'$var'";
			break;
		case "array":
			$str.="array(\n";
			foreach ($var as $key => $item)
			{
			   $code_key = print_php_code($key);
			   $code_item = print_php_code($item);
			   $str.="$code_key => $code_item,\n";
			}
			$str.=")";
			break;
		case "object":break;
		case "resource":break;
		case "NULL": break;
		case "unknown type": break;
	}
	return $str;
}


function xmkdir($dir)
{
	if($dir[strlen($dir)-1]!='/')
		$dir="$dir/";
}

function xdefarray($defarray,&$_array)
{
	foreach($defarray as $key => $val)
	{
		if(is_array($val))
		{
			xdefarray($val, $_array[$key]);
		}
		else 
		{
			if(empty($_array[$key]))
				$_array[$key]=$val;
		}
	}
}

function get_min_index($arr)
{
	$keys=array_keys($arr);
	$min=$arr[$keys[0]];
	$findkey=$keys[0];
	foreach ($keys as $k)
	{
		if($arr[$k]<$min)
		{
			$findkey=$k;
			$min=$arr[$k];
		}
	}
	return $findkey;
}
// ��������� ������
function randstr($caplen=20)
{
	
	$letters = '0123456789ABCDEFGKIJKLMNOPQRSTUVWXYZ'; // �������
	$captcha = '';//�������� �����
	for ($i = 0; $i < $caplen; $i++)
	{
		$captcha .= $letters[ rand(0, strlen($letters)-1) ]; // ���������� ��������� ������ �� ��������	
	}
	return $captcha;
}

function sort_hash($hash)
{
	$newhash=array();
	$cnt=count($hash);
	for($i=0;$i<$cnt;$i++)
	{
		$idx=get_min_index($hash);
		$newhash[$idx]=$hash[$idx];
		unset($hash[$idx]);
	}
	return $newhash;
}

function url($theurl)
{
	global $_URL_BASE;
	$STR_RES='';
	if(is_string($theurl))
	{
		if($_URL_BASE[0]!='/')
			$_URL_BASE="/$_URL_BASE";
		if($_URL_BASE[strlen($_URL_BASE)-1]!='/')
			$_URL_BASE="$_URL_BASE/";
		$STR_RES = $_URL_BASE.$theurl;
	}
	$STR_RES = strtr($STR_RES,Array('//'=>'/'));
	return $STR_RES;
}
// �������� ������ � ����������
function truncstr($str,$tlen)
{
	if(strlen($str)>$tlen)
		return substr($str,0,$tlen)."...";
	else 
		return $str;
}

function ep_exists($ep)
{
	global $_EP;
	global $_BASE_PATH,$_SITE,$_MODULES;
	
	return folder_exists($_BASE_PATH."/sites/$_SITE/ep/$ep/");
}

function get_ep_list()
{
	global $_EP;
	global $_BASE_PATH,$_SITE,$_MODULES;
	$d = dirtree($_BASE_PATH."/sites/$_SITE/ep/$ep/");
	//return folder_exists($_BASE_PATH."/sites/$_SITE/ep/$ep/");
}

function dirtree($fldr)
{
	$d = dir($fldr);
	$fldrlist=Array();
	while (false !== ($entry = $d->read())) {
		if(!in_array($entry,Array('.','..')))
		{
			if(is_dir("$fldr/$entry"))
			{
				$fldrlist[$entry]=dirtree("$fldr/$entry");
			}
			else
				$fldrlist[]=$entry;
		}
	}
	$d->close();
	return $fldrlist;
}
// execute event
function exe_event($ev,$params)
{
	global $_BASE_PATH,$_SITE,$_MODULES,$_PAGE,$_MODULES,$_PAGE_FILE_PATH,$_QUERY;
	
	foreach($_MODULES as $modkey => $modval)
	{
		$evproc="";
		if(is_string($modkey))
		{
			$_PARAMS=$modval;
					//require_once "$_BASE_PATH/modules/$modkey/index.php";
			$evproc="on_$modkey_$ev";
			
		}
		else 
		{
			$_PARAMS=Array();
					//require_once "$_BASE_PATH/modules/$modval/index.php";
			
			$evproc="on_".$modval."_$ev";
		//	
		}
		
		//echo "::$evproc::";
		$functions =get_defined_functions();
	//	var_dump($functions["user"]);
		if(function_exists($evproc))
		{
		//	echo "::$evproc::";
			$evproc($params);
		}
	}
}

function get_module($str,&$modname,&$tail)
{
	if(is_str_begin($str,"mod_"))
	{
		$splices=explode('/', $str);
		$modname=str_tail($splices[0],"mod_");
		unset($splices[0]);
		$tail=implode('/',$splices);
		
		return true;
	}
	if(is_str_begin($str,"./mod_"))
	{
		$splices=explode('/', $str);
		unset($splices[0]);
		$modname=str_tail($splices[1],"mod_");
		unset($splices[1]);
		$tail=implode('/',$splices);
	
		return true;
	}
	return false;
}
// �������� ��� ������ ���������� � 
function is_str_begin($str,$str_begin)
{
	$strlen=strlen($str_begin);
	return substr($str,0,$strlen)==$str_begin;
}
// ����� ������ ��� ���������� ������
function str_tail($str,$str_begin)
{
	$strlen=strlen($str_begin);
	return substr($str,$strlen);
}

function get_act_mess($mid)
{
	global $_BASE_PATH;

	if(!empty($_SESSION['actmess']))
	{
		//var_dump($_SESSION['actmess'][$mid]);
		return $_SESSION['actmess'][$mid];
	}
	else 
		return "";
}
// set message
function set_act_mess($mid,$text,$err=false)
{
	global $_BASE_PATH;
	if(empty($_SESSION['actmess']))
	{
		$_SESSION['actmess']=Array();
	}
	$_SESSION['actmess'][$mid]=$text;
	global $_ACT_ERROR;
	$_ACT_ERROR=true;
}

function pagetree($site=NULL,$ep=NULL)
{
	global $_EP;
	global $_SITE, $_BASE_PATH;
	if($site==NULL)
		$site=$_SITE;
	if($ep==NULL)
		$ep=$_EP;
	return dirtree("$_BASE_PATH/sites/$site/ep/$ep/pages");
}
// ������ ��������� ��� �������� ���������� ��������
function success($succmess,$timeout=0)
{
	global $_BASE_PATH;
	global $_SUCC_MESS;
	$_SUCC_MESS=$succmess;
	global $_REDIR_TIMEOUT;
	$_REDIR_TIMEOUT=$timeout;
}

function redirect($url,$timeout=0)
{
	if($url=='') $url='/';
	global $_BASE_PATH;
	if(!$timeout)
	{
		echo "
		<script type=\"text/javascript\">
			document.location=\"$url\";		
		</script>";		
	}
	else // � ����������
	{
		echo "<script type=\"text/javascript\">
		window.setTimeout(function(){
			document.location=\"$url\";
				}, $timeout);
		</script>";
		
		exit();
	}
}

function xarray_key_exists($key,$srch)
{
	if(is_array($srch))
		return array_key_exists($key,$srch);
	else
		return false;

}

function xfilepath($_file)
{
	$path =dirName($_file);
	$srvdir=$_SERVER['DOCUMENT_ROOT'];

	$path= strtr($path,Array($srvdir=>''));
	$srvdir=strtr($srvdir,Array("/"=>'\\'));
	$path= strtr($path,Array($srvdir=>''));
	if($path[0]=='/')
		$path=substr($path,1);
	if($path[0]!='\\')
		$path="\\$path";

	return $path;
}
// url ����� �� �������� �����
function xbrotherfileurl($myname,$url_rel)
{
	$_url=xfilepath($myname);
	$_url=$_url."$url_rel";
	$_url=strtr($_url,Array("\\"=>'/'));
	/*	if($_url[0]=='/')
		$_url=substr($_url,1);*/
	return "$_url";
}


function ximplode($delimeter,$hasharray,$template,$_array=Null)
{
	$newhash=Array();
	foreach ($hasharray as $idx => $val)
	{
		$tpl=Array();
		$tpl["{idx}"]=$idx;
		$tpl["{key}"]=$idx;
		$tpl["{0}"]=$val;
		if(is_array($val))
		{
			foreach($val as $key => $v)
			{
				$tpl["{".$key."}"]=$v;
			}
		}
		$val = strtr($template,$tpl);

		if($_array!=null)
		{
			$val = strtr($val,$_array);
		}
		$newhash[]=$val;
	}

	return implode($delimeter,$newhash);
}

// mark mutex occupied
function mutex_mark($mtxname)
{
	global $_USEMUTEX;
	if(!$_USEMUTEX) return ;
	$_mtx_file =".mtx_$mtxname";
	file_put_contents($_mtx_file,time());
}

// wait when the will be free
function mutex_wait($mtxname)
{
	global $_USEMUTEX;
	if(!$_USEMUTEX) return ;
	$_mtx_file =".mtx_$mtxname";
	while(file_exists($_mtx_file ))
	{

	}
	mutex_mark($mtxname);
}


// free the mutex
function mutex_free($mtxname)
{
	$_mtx_file =".mtx_$mtxname";
	@unlink($_mtx_file);
}

function hash2url($hash)
{
	return ximplode('&', $hash, "{key}={0}");
}

function xsplit_array($arr, $item_count)
{
	$_res = Array();
	$buf = Array();
	$i=0;
	foreach ($arr as $key => $val)
	{
		if(is_string($key))
			$buf[$key]=$val;
		else
			$buf[]=$val;
		$i++;
		$i%=$item_count;
		if($i==0)
			$_res[]=$buf;
	}
	return $_res;
}

function inc_ser($filename)
{
	$file_ser = "$filename.php.ser";
	$file_php = "$filename.php";
}

?>