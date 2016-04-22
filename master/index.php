<?php 
require_once '../core/index.php';
require_once '../config.php';

$_BASE_PATH='..';
load_libs($_LIBS);
load_components($_COMPONENTS);
detect_route();
load_ep('master');
echo get_page($_PAGE);
?>