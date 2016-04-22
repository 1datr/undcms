<img id="<?php echo $capcha_id;?>" src="<?php echo serv_url('mod_captcha/captcha',$addparams); ?>" />
	<button  type="button" class="glyphicon glyphicon-refresh" onclick="$('#<?php echo $capcha_id;?>').attr('src','<?php echo serv_url('mod_captcha/captcha',$addparams); ?>');"></button>
	<input type="text" class="form-control" name="<?php echo $capcha_id; ?>" placeholder="captcha" value="">
	