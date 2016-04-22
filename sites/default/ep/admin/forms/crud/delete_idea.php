
<?php
form_begin('crud/delete_',Array('class'=>'frm_delete_','confirm'=>'Are you realy want to delete this item?'));
use_template('delete_',Array('row'=>$row));
form_end();			
?>						
