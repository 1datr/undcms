<?php 

init_bootstrap();
addmeta("Content-Type","text/html; charset=utf-8");
//con_fluid();
?>
<div class="container">
<div class="row">
 <?php DRAW_REGION('header'); ?>
</div>
<div class="row">

  	<div class="col-xs-5"></div>
  	<div class="col-xs-2"><?php echo $_R_MAIN;?></div>
  	<div class="col-xs-5"></div>
  	

</div>
<div class="row">
 <?php DRAW_REGION('footer'); ?>
</div>
</div>
<?php 
//enddiv();
?>