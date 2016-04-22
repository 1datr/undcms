
				<?php
				$res_lookup['from']=$_DB->scheme->select('user','*')->exe();
				?>
				
				<?php
				$res_lookup['to']=$_DB->scheme->select('user','*')->exe();
				?>
				<?php
		use_template('add_pmessage',Array('res_lookup'=>$res_lookup));
		/* ADD FORM END */
		?>