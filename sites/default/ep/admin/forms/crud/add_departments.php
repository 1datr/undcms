
				<?php
				$res_lookup['parent']=$_DB->scheme->select('departments','*')->exe();
				?>
				
				<?php
				$res_lookup['leader']=$_DB->scheme->select('user','*')->exe();
				?>
				<?php
		use_template('add_departments',Array('res_lookup'=>$res_lookup));
		/* ADD FORM END */
		?>