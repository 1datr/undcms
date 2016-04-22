<?php 
init_bootstrap();
addmeta("Content-Type","text/html; charset=utf-8");
//con_fluid();
?>
<div class="container">
<div class="row">
<?php //load_block('menu'); ?>
<nav class="navbar " role="navigation">
<ul class="nav nav-pills">
	<?php DRAW_REGION('header'); ?>
</ul>
</nav>
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