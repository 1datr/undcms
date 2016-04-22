<?php 
$_BASE_PATH ='.';

require_once './core/index.php';
require_once './config.php';

load_libs($_LIBS);
load_components($_COMPONENTS);
init_site('default');
load_ep('front');
select_db_profile('default');


detect_route();
echo get_page($_PAGE);
?>