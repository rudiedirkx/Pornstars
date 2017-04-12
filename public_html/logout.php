<?php

require 'inc.bootstrap.php';

if ( logincheck(false) ) {
	$g_user->update(['unihash' => '']);
	$_SESSION = [];
}

return do_redirect('index');
