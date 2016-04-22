<?php 
require_once dirName(__FILE__).'/core/index.php';
$bl = new byliner();

function exe_line(&$obj){
	switch($obj['bl']->GetStep())
	{
		case 0:
			echo "STEP 1<br />";
			echo "i=".$obj['bl']->GetVar('i');
			$obj['bl']->InitVar('i',0);			
			
			$obj['bl']->SetVar('i',$obj['bl']->GetVar('i')+1);// inc i
			if($obj['bl']->GetVar('i')>=5)
				$obj['bl']->SetStep(1);
			break;
		case 1:
			echo "STEP 2<br />";
			echo "i=".$obj['bl']->GetVar('i');
			$obj['bl']->SetVar('i',$obj['bl']->GetVar('i')+1);// inc i
			if($obj['bl']->GetVar('i')>=13)
			{
				$obj['bl']->SetStep(0);
				$obj['bl']->SetVar('i',0);
			}
			break;
	}
}

$bl->_onstep = 'exe_line';
$bl->exe_step();

?>