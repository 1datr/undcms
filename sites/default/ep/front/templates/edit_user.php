<?php
		
		form_begin('crud/edit_user');
		
		title("Edit user #".$row['id']);
		?>
		<input type="hidden" name="row[id]" value="<?php echo $_QUERY['id']; ?>" />
		
						<div class="form-group">
						<label for="inp_login" class="col-md-2 control-label">login</label>
						<div class="input-group col-md-5">
						<input id="inp_login" type="text" class="form-control" name="row[login]" placeholder="login"  value="<?php echo $row['login']; ?>">
						</div>
						</div>
						<div class="form-group">
						<label for="inp_password" class="col-md-2 control-label">password</label>
						<div class="input-group col-md-5">
						<input id="inp_password" type="text" class="form-control" name="row[password]" placeholder="password"  value="<?php echo $row['password']; ?>">
						</div>
						</div>
						<div class="form-group">
						<label for="inp_name" class="col-md-2 control-label">name</label>
						<div class="input-group col-md-5">
						<input id="inp_name" type="text" class="form-control" name="row[name]" placeholder="name"  value="<?php echo $row['name']; ?>">
						</div>
						</div>
						<div class="form-group">
						<label for="inp_avatar" class="col-md-2 control-label">avatar</label>
						<div class="input-group col-md-5">
						<input id="inp_avatar" type="text" class="form-control" name="row[avatar]" placeholder="avatar"  value="<?php echo $row['avatar']; ?>">
						</div>
						</div>
						<div class="input-group input-group-sm">
						<input type="submit" class="btn btn-default" value="���������" />
						</div>
						<?php
						form_end();
		
?>