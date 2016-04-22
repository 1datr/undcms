function loadblock(conid,block)
{
	jQuery.post("index.php", {'load[block]' : block});
}