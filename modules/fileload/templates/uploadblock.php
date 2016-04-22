$("#multipleupload").uploadFile({
url:"<?php echo serv_url('mod_fileload/fileload',$addparams); ?>",
multiple:true,
fileName:"myfile"
});