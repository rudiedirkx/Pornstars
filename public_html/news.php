<?php

use rdx\ps\News;

require 'inc.bootstrap.php';

logincheck();

if ( isset($_GET['delete'], $_GET['all']) )
{
	db_update('news', "deleted = '1'", 'planet_id = '.PLANET_ID.' AND seen = \'1\'');
	Go();
}
else if ( isset($_GET['delete'], $_GET['id']) )
{
	db_update('news', "deleted = '1'", 'planet_id = '.PLANET_ID.' AND seen = \'1\' AND id = '.(int)$_GET['id']);
	Go();
}
else if ( isset($_GET['delete'], $_GET['subject']) )
{
	db_update('news', "deleted = '1'", 'planet_id = '.PLANET_ID.' AND seen = \'1\' AND news_subject_id = '.(int)$_GET['subject']);
	Go();
}

_header();

$arrNewsItems = News::all("planet_id = ? AND (deleted = '0' OR seen = '0') ORDER BY id DESC", [PLANET_ID]);

?>
<div class="header">Planetary News</div>

<br />

<?php

if ( 0 < count($arrNewsItems) )
{
	echo '<a href="?delete=1&all=1">delete all news</a><br />';
	foreach ( $arrNewsItems AS $arrItem )
	{
		echo '<br /><table border="0" cellpadding="3" cellspacing="0" width="450" align="center" style="border:solid 1px #222;border-width:0 1px 1px 0;">';
		echo '<tr>';
		echo '	<td style="padding:0;width:30px;" width="30"><img title="'.$arrItem['name'].'" alt="'.$arrItem['name'].'" src="images/'.$arrItem['image'].'" height="55" width="55" /></td>';
		echo '	<td nowrap="1" wrap="off" bgcolor='.((!$arrItem['seen'])?"#cc0000":"#222222").'>'.date('Y-m-d H:i:s', $arrItem['utc_time']).', <b>Tick: '.$arrItem['myt'].'</b></td>';
		echo '	<td align="right" bgcolor="' . ( !$arrItem['seen'] ? '#cc0000' : '#222222' ) . '">';
		echo '		<a href="news.php?delete=1&id='.$arrItem['id'].'">delete</a> -';
		echo '		<a href="news.php?delete=1&subject='.$arrItem['news_subject_id'].'">/'.$arrItem['name'].'</a>';
		echo '	</td>';
		echo '</tr>';
		echo '<tr>';
		echo '	<td></td>';
		echo '	<td colspan=2>'.$arrItem['message'].'</td>';
		echo '</tr>';
		echo '</table><br />';
	}
	echo '<a href="?delete=1&all=1">delete all news</a>';
}
else {
	echo 'You have no news!';
}

echo '<br />';

$db->update('news', "seen = '1'", 'planet_id = ? AND utc_time < ?', [PLANET_ID, time()]);

_footer();
