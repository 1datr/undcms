<?php 
init_bootstrap();
addmeta("Content-Type","text/html; charset=windows-1251");
//con_fluid();
?>
<div class="navbar navbar-inverse navbar-fixed-top" role="navigation">
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="#">Project name</a>
        </div>
        <div class="collapse navbar-collapse">
          <ul class="nav navbar-nav">
            <li class="active"><a href="#">Home</a></li>
            <li><a href="/pages">Страницы</a></li>
            <li><a href="/contact">Contact</a></li>
          </ul>
        </div><!--/.nav-collapse -->
      </div>
    </div>
 </div>

<div class="container">
<div class="row">
<?php load_block('menu'); ?>
</div>
<div class="row">

  	<div class="col-xs-2"> <?php DRAW_REGION('left'); ?></div>
  	<div class="col-xs-10"><?php echo $_R_MAIN;?></div>
  	

</div>
<div class="row">
 <?php DRAW_REGION('footer'); ?>
</div>
</div>
<?php 
//enddiv();
?>