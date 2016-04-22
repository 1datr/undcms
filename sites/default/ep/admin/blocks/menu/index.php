<?php 
global $_EP, $_DB;
global $_SITE;
global $_PAGE;
$eptree=pagetree();

function make_menu($arr,$menuclass,$rolemenu=false,$baseurl='./')
{
	$syspages=Array('redirect');
	$_role="";
	global $_PAGE;
	if($rolemenu)
		$_role="role=\"menu\"";
	echo "<ul $_role class=\"$menuclass\">";
	$i=0;
	foreach($arr as $key => $element)
	{		
		if($_PAGE=="./$key/")
			$classes="active";
		else 
			$classes="";
		if(is_array($element))
		{
			
			echo "<li class=\"dropdown $classes\" >
	<a  class=\"dropdown-toggle\" data-toggle=\"dropdown\" href=\"#\">$key
			<span class=\"caret\"></span>
	</a>";
			make_menu($element,'dropdown-menu',true,$baseurl."$key/");
			echo "</li>";
		}
		else 
		{
			$info = pathinfo($baseurl.$element);
			$pagename=$info["filename"];
			if(!in_array($pagename,$syspages))
			{
				if($pagename!='index')
					echo "<li class=\"$classes\"><a href=\"/admin/$baseurl$pagename\">$pagename</a></li>";
				else
					echo "<li class=\"$classes\"><a href=\"/admin/$baseurl\">$pagename</a></li>";
				
			}
		}
		$i++;
	}
	echo "</ul>";
	
}

//echo "// $_PAGE //";
/*jqready("
	\$('.dropdown-toggle .dropdown-menu').dropdown();	
		");*/
//make_menu($eptree, 'nav nav-pills navbar ');/* nav*/
foreach($_DB->scheme->_SCHEME as $tblkey => $tbl)
{
	if($_PAGE=="./".$tblkey."s")
		echo "<li class=\"active\"><a href=\"/admin/".$tblkey."s\">{$tblkey}s</a></li>";
	else
		echo "<li><a href=\"/admin/".$tblkey."s\">{$tblkey}s</a></li>";
}

?>

  
