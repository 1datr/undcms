<?php 
function is_role_guest()
{
	return empty($_SESSION['uid']);
}

function is_role_user()
{
	return !is_role_guest();
}


?>