
				<?php
				$res_lookup['user']=$_DB->scheme->select('user','*')->exe();
				?>
				<?php
		use_template('add_article',Array('res_lookup'=>$res_lookup));
		/* ADD FORM END */
		?>