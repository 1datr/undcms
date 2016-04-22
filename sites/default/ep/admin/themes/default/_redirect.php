<?php 
init_bootstrap();
title('Переход на страницу после успешно выполненной операции');
addmeta("Content-Type","text/html; charset=windows-1251");
?>
<center>
<div class="jumbotron">
  <h1>Переход на другую страницу</h1>
  <p></p>
  <p><?php  echo $_PARAMS['MESS'];?></p>
</div>

<script type="text/javascript">
			window.setTimeout(function(){
		    document.location="<?php  echo $_PARAMS['URL'];?>";	
		}, <?php  echo $_PARAMS['TIMEOUT'];?>);
</script>
</center>