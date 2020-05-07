<?php

use rdx\ps\Planet;

require 'inc.bootstrap.php';

if ( logincheck(false) ) {
	return do_redirect('index');
}

// $arrRaces = $db->select_fields('d_races', 'id, race', '1 ORDER BY id ASC');

if ( isset($_POST['email'], $_POST['rulername'], $_POST['planetname'], $_POST['password']) ) {
	$_POST = array_map('trim', $_POST);

	foreach ( ['rulername', 'planetname', 'password'] as $field ) {
		if ( strlen($_POST[$field]) < 4 ) {
			exit("$field is too short");
		}
	}

	$exists = function($column, $value) use ($db) {
		return $db->count('planets', [$column => $value]) > 0;
	};

	foreach ( ['rulername', 'planetname'] as $field ) {
		if ( $exists($field, $_POST[$field]) ) {
			exit("$field already exists");
		}
	}

	// if ( empty($_POST['race']) || !isset($arrRaces[ $_POST['race'] ]) ) {
	// 	exit('invalid race');
	// }

	if ( $exists('email', $_POST['email']) ) {
		sessionError('This e-mail already exists..?');
		return do_redirect();
	}

	Planet::create($_POST);

	// $g_prefs->sendEmail(
	// 	$_POST['email'],
	// 	'Signup',
	// 	'You are now the ruler of a planet! Log in to start ruling.'
	// );

	sessionSuccess('You can log in now.');
	return do_redirect('login');
}

include 'tpl.anon-menu.php';

?>
<html>

<head>
<? include 'tpl.head.php' ?>
<title><?= html($GAMENAME) ?></title>
</head>

<body style="text-align: center">

<?php include 'inc.message.php'; ?>

<h1>SIGNUP</h1>

<form method="post" action autocomplete="off">
	<!-- Distract browser from prefilling form fields -->
	<input style="position: absolute; visibility: hidden" name="fake_email" type="email" />
	<input style="position: absolute; visibility: hidden" name="fake_password" type="password" />

	<p>
		E-mail:<br />
		<input name="email" type="email" autofocus required />
	</p>

	<p>
		Password:<br />
		<input name="password" type="password" required />
	</p>

	<p>
		Ruler name:<br />
		<input name="rulername" required />
	</p>

	<p>
		Planet name:<br />
		<input name="planetname" required />
	</p>

	<? /* <p>
		Race:<br />
		<select name="race" size="<?= count($arrRaces) ?>" required>
			<?= html_options($arrRaces, '') ?>
		</select>
	</p> */ ?>

	<p>
		<button>Sign up</button>
	</p>
</form>

</body>

</html>
<?php

unset($_SESSION['ps_msg']);

?>
