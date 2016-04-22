
				<?php
				$res_lookup['parent']=$_DB->scheme->select('group','*')->exe();
				?>
				<?php
		use_template('add_group',Array('res_lookup'=>$res_lookup));
		/* ADD FORM END */
		?>