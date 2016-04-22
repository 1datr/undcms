<?php 
bs_act_mess('err_auth');
?>

<div class="form-group ">
			<label for="inp_name" class="col-md-6 control-label">[t@login]</label>
			<div class="input-group col-md-6">
			<input id="inp_name" type="text" class="form-control" name="login" placeholder="login" value="<?php echo last_post_val('name'); ?>">
			</div>
</div>

<div class="form-group ">
			<label for="inp_name" class="col-md-6 control-label">[t@password]</label>
			<div class="input-group col-md-6">
			<input id="inp_avatar" type="password" class="form-control" name="password" placeholder="password" value="<?php echo last_post_val('avatar'); ?>">
			</div>
</div>
  		
<div class="form-group">

	<input type="submit" class="btn btn-default" value="[t@submit]" />
</div>

<div class="input-group input-group-sm">
</div>
<?php 

?>