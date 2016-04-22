<?php 
function init_bootstrap()
{
	jqinit();
	addjs(xbrotherfileurl(__FILE__,'/bootstrap3/js/bootstrap.js'));
	addcss(xbrotherfileurl(__FILE__,'/bootstrap3/css/bootstrap.min.css'));	
	addcss(xbrotherfileurl(__FILE__,'/bootstrap3/css/bootstrap-theme.min.css'));
	
	addjs('https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js','IE 9');
	addjs('https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js','IE 9');	
}

function init_datetimepicker($selectors=".form_datetime",$lang="ru")
{
	jqinit();
	addjs(xbrotherfileurl(__FILE__,'/bootstrap3/js/dtp/bootstrap-datetimepicker.min.js'));
	addjs(xbrotherfileurl(__FILE__,'/bootstrap3/js/dtp/locales/bootstrap-datetimepicker.'.$lang.'.js'));
	addcss(xbrotherfileurl(__FILE__,'/bootstrap3/css/dtp/bootstrap-datetimepicker.min.css'));
	jqready_gather("$('$selectors').datetimepicker({
        language:  '$lang',
        weekStart: 1,
        todayBtn:  1,
		autoclose: 1,
		todayHighlight: 1,
		startView: 2,
		forceParse: 0,
        showMeridian: 1
    });");
}

function con_fluid($role='main',$id='',$classes='')
{
	$classstr="";
	if(is_array($classes))
		$classstr=" \"".implode(' ',$classes)."\"";
	else
		$classstr=$classes;
	
	if($id=='')
		$idstr="";
	else 
		$idstr="id=\"$id\"";
	echo "<div class=\"container-fluid $classstr\" role=\"$role\" $idstr>";
}
// ����� ��������� �� �������� ��������� ������ �������, �� ��������� ��������� ������
function bs_act_mess($mesid,$mestype='danger')
{
	/*
	<div class="alert alert-success">...</div>
<div class="alert alert-info">...</div>
<div class="alert alert-warning">...</div>
<div class="alert alert-danger">...</div>
	 */
	$_mes=get_act_mess($mesid);
	if($_mes!='')
	{
		//var_dump($_mes);
		use_template('act_mess',Array('mestype'=>$mestype,'_mes'=>$_mes));
	//	echo "<div class=\"alert alert-$mestype\">$_mes</div>";
	}
}

function menu()
{
	
}

function enddiv()
{
	echo "</div>";
}

?>