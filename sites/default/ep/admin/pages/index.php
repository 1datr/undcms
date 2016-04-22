<?php 
addcss(xbrotherfileurl(__FILE__,'/css/style.css'));
title("PAGE INDEX");
addmeta('description', 'The my first page');
//jqready('alert("Hello");');
push_block('right', 'auth');
//echo $_SERVER['DOCUMENT_ROOT'];
echo get_form("mod_auth/auth");
?>