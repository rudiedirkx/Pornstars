<?php

unset($_SESSION['ps_msg']);

?>

	</div><!-- #right -->
</div><!-- .flex -->

<details>
	<summary>Bad queries (<?= count($_bqueries = $GLOBALS['db']->bad_queries()) ?>)</summary>
	<ul>
		<? foreach ( $_bqueries as $template => $queries ): ?>
			<li>
				<details>
					<summary>[<?= count($queries) ?>x] <?= html($template) ?></summary>
					<ul>
						<? foreach ( $queries as $query ): ?>
							<li><?= html($query) ?></li>
						<? endforeach ?>
					</ul>
				</details>
			</li>
		<? endforeach ?>
	</ul>
</details>

<details>
	<summary>Queries (<?= count($GLOBALS['db']->queries) ?>)</summary>
	<ul>
		<? foreach ( $GLOBALS['db']->queries as $query ): ?>
			<li><?= html($query) ?></li>
		<? endforeach ?>
	</ul>
</details>

</body>

</html>
