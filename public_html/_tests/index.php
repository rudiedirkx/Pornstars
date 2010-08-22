<?

$dir = "./";

$THISFILE = basename($_SERVER['SCRIPT_NAME']);
$THISMAP = basename(str_replace("\\","/",realpath("./")));

?>
<html>

<head>
<title><?=$THISMAP?></title>
</head>

<body>
<?

/* DIRS */

$map = opendir($dir);
$i=0;
while ($file = readdir($map))
{
	$exp = explode(".",$file);
	$ext = strtolower($exp[count($exp)-1]);

	if (is_dir($dir.$file) && strtolower(realpath("./").$THISFILE) != strtolower(realpath($dir).$file) && $file!='.' && $file!='..')
	{
		$files_bestandsnaam[$i] = $file;
		$files_ordernaam[$i] = strtolower($file);
		$files_extensie[$i] = $ext;
		$i++;
	}
}
if ($i)
{
	asort($files_ordernaam); reset($files_ordernaam);
	foreach ($files_ordernaam AS $num => $value)
	{
		echo "<img src='/icons/folder.gif' height=16 width=16> <a href='".$dir.$files_bestandsnaam[$num]."'>"./*ucfirst*/($files_bestandsnaam[$num])."</a><br>";
	}
}




/* BESTANDEN */

$map = opendir($dir);
$i=0;
while ($file = readdir($map))
{
	$exp = explode(".",$file);
	$ext = strtolower($exp[count($exp)-1]);

	if (!is_dir($dir.$file) && strtolower(realpath("./").$THISFILE) != strtolower(realpath($dir).$file) && $file!='.' && $file!='..')
	{
		$files_bestandsnaam[$i] = $file;
		$files_ordernaam[$i] = strtolower($file);
		$files_extensie[$i] = $ext;
		$i++;
	}
}
if ($i)
{
	asort($files_ordernaam); reset($files_ordernaam);
	foreach ($files_ordernaam AS $num => $value)
	{
		echo "<img src='/icons/text.gif' height=16 width=16> <a href='".$dir.$files_bestandsnaam[$num]."'>"./*ucfirst*/($files_bestandsnaam[$num])."</a><br>";
	}
}


