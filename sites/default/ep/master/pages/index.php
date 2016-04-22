<?php 
addcss(xbrotherfileurl(__FILE__,'/css/style.css'));
title("PAGE INDEX");
addmeta('description', 'The my first page');

//jqready('alert("Hello");');
push_block('header', 'header');
push_block('right', 'auth');
//echo $_SERVER['DOCUMENT_ROOT'];
?>
<div id="name">123456 </div><div id="namex"><?php echo dirname(__FILE__); ?> </div>