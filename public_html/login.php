<?php

use rdx\ps\Planet;

require 'inc.bootstrap.php';

if ( logincheck(false) ) {
	return do_redirect('index');
}

// LOG IN
if ( isset($_POST['u'], $_POST['p']) ) {
	$objPlanet = Planet::first(['email' => $_POST['u']]);

	// Invalid login
	if ( !$objPlanet || !$objPlanet->checkPassword($_POST['p']) ) {
		exit('Invalid login combination!');
	}

	$save = array(
		'planet_id'	=> $objPlanet->id,
		'unihash'	=> rand_string(16),
	);
	$_SESSION = $save + $_SESSION;

	if ( $objPlanet->sleep > time() ) {
		$objPlanet->update(['nextsleep' => time() + 14*3600]);
	}

	$objPlanet->update([
		'sleep' => 0,
		'lastaction' => time(),
		'lastlogin' => time(),
		'unihash' => $save['unihash'],
	]);

	return do_redirect('overview');
}

// PASSWORD RESET
if ( isset($_POST['reset_email']) ) {
	$objPlanet = Planet::first(['email' => $_POST['reset_email']]);

	sessionSuccess('An e-mail has been sent to ' . $_POST['reset_email'] . ' with instructions.');

	if ( !$objPlanet ) {
		$g_prefs->sendEmail(
			$_POST['reset_email'],
			'Password reset',
			'There is no account registered with this e-mail address.'
		);
		return do_redirect();
	}

	$password = rand_string();
	$objPlanet->update(compact('password'));

	$g_prefs->sendEmail(
		$_POST['reset_email'],
		'Password reset',
		'New password for this account: ' . $password
	);

	return do_redirect();
}

?>
<html>

<head>
<? include 'tpl.head.php' ?>
<title><?= html($GAMENAME) ?></title>
</head>

<body style="text-align: center">

<? include 'tpl.anon-menu.php' ?>

<?php include 'inc.message.php'; ?>

<h1>LOGIN</h1>

<form method="post" action>
	<p>
		E-mail<br />
		<input type="email" autofocus name="u" />
	</p>

	<p>
		Password<br>
		<input type="password" name="p" />
	</p>

	<p><button>Log in</button></p>
</form>

<br />

<h2>Reset password</h2>

<form method="post" action>
	<p>
		E-mail:<br />
		<input type="email" name="reset_email" required />
	</p>

	<p><button>Reset password</button></p>
</form>

</body>

</html>
<?php

unset($_SESSION['ps_msg']);
