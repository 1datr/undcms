
				<?php
				$res_lookup['autor']=$_DB->scheme->select('user','*')->exe();
				?>
				<?php
		use_template('add_problem',Array('res_lookup'=>$res_lookup));
		/* ADD FORM END */
		?>