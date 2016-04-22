
				<?php
				$res_lookup['user']=$_DB->scheme->select('user','*')->exe();
				?>
				<?php
		use_template('add_good',Array('res_lookup'=>$res_lookup));
		/* ADD FORM END */
		?>