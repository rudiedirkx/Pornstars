<?php

require 'inc.bootstrap.php';

logincheck();

_header();

?>
<h1>Galactic News</h1>

<h2>Incoming</h2>

<table>
	<tr>
		<th>Origin</th>
		<th>Destination</th>
		<th>ETA</th>
	</tr>
	<? foreach ( $g_user->galaxy->incoming_fleets as $fleet ): ?>
		<tr class="<?= html($fleet->action) ?>ing fleet">
			<td>
				<?= html($fleet->owner_planet) ?>
				(<a href="galaxy.php?x=<?= $fleet->owner_planet->x ?>&y=<?= $fleet->owner_planet->y ?>"><?= implode(':', $fleet->owner_planet->coordinates) ?></a>)
			</td>
			<td>
				<?= html($fleet->destination_planet) ?>
				(<a href="galaxy.php?x=<?= $fleet->destination_planet->x ?>&y=<?= $fleet->destination_planet->y ?>"><?= implode(':', $fleet->destination_planet->coordinates) ?>)
			</td>
			<td><?= $fleet->travel_eta ?: $fleet->action_eta ?></td>
		</tr>
	<? endforeach ?>
</table>

<h2>Outgoing</h2>

<table>
	<tr>
		<th>Origin</th>
		<th>Destination</th>
		<th>ETA</th>
	</tr>
	<? foreach ( $g_user->galaxy->outgoing_fleets as $fleet ): ?>
		<tr class="<?= html($fleet->action) ?>ing fleet">
			<td>
				<?= html($fleet->owner_planet) ?>
				(<a href="galaxy.php?x=<?= $fleet->owner_planet->x ?>&y=<?= $fleet->owner_planet->y ?>"><?= implode(':', $fleet->owner_planet->coordinates) ?></a>)
			</td>
			<td>
				<?= html($fleet->destination_planet) ?>
				(<a href="galaxy.php?x=<?= $fleet->destination_planet->x ?>&y=<?= $fleet->destination_planet->y ?>"><?= implode(':', $fleet->destination_planet->coordinates) ?>)
			</td>
			<td><?= $fleet->travel_eta ?: $fleet->action_eta ?></td>
		</tr>
	<? endforeach ?>
</table>

<?php

_footer();
