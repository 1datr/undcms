<?php 
global $_EP;
global $_SITE;
$eptree=pagetree();

function make_menu($arr,$menuclass,$rolemenu=false,$baseurl='./')
{
	$syspages=Array('redirect','templates');
	$_role="";
	if($rolemenu)
		$_role="role=\"menu\"";
	echo "<ul $_role class=\"$menuclass\">";
	$i=0;
	foreach($arr as $key => $element)
	{		
		if($i==0)
			$classes="active";
		else 
			$classes="";
		if(is_array($element))
		{
			if(in_array($key,$syspages))
				continue;
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
				echo "<li class=\"$classes\"><a href=\"/$baseurl$pagename\">$pagename</a></li>";
		}
		$i++;
	}
	echo "</ul>";
	
}
/*jqready("
	\$('.dropdown-toggle .dropdown-menu').dropdown();	
		");*/
make_menu($eptree, 'nav nav-pills');
?>

