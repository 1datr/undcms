
				<?php
				$res_lookup['parent']=$_DB->scheme->select('department','*')->exe();
				?>
				
				<?php
				$res_lookup['leader']=$_DB->scheme->select('user','*')->exe();
				?>
				<?php
		use_template('add_department',Array('res_lookup'=>$res_lookup));
		/* ADD FORM END */
		?>