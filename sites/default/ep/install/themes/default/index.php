<?php 
init_bootstrap();
addmeta("Content-Type","text/html; charset=windows-1251");
//con_fluid();
?>
<div class="container">
<div class="row">
 <?php DRAW_REGION('header'); ?>
<?php load_block('menu'); ?>
</div>
<div class="row">

  	<div class="col-xs-2"> <?php DRAW_REGION('left'); ?></div>
  	<div class="col-xs-8"><?php echo $_R_MAIN;?></div>
  	<div class="col-xs-2"> <?php DRAW_REGION('right'); ?></div>

</div>
<div class="row">
 <?php DRAW_REGION('footer'); ?>
</div>
</div>
<?php 
//enddiv();
?>