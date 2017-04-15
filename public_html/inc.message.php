<?php

if ( isset($_SESSION['ps_msg']) ) {
	list($type, $message) = $_SESSION['ps_msg'];
	echo '<div class="message ' . $type . '">' . $message . '</div>';
}
