<?php

require_once('inc.config.php');

if ( logincheck(false) ) {
	db_update('planets', "unihash = ''", 'id = '.PLANET_ID);
	unset($_SESSION[$sessionname]);
}

?>
<script type="text/javascript">top.location = './';</script>