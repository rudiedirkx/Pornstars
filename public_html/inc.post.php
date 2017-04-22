<table>
	<? if ( !$post->parent_thread_id ): ?>
		<tr>
			<td colspan="2"><a href="?thread=<?= $post->id ?>"><?= html($post->title ?: '...') ?></a></td>
		</tr>
	<? endif ?>
	<tr>
		<td><?= $post->creator_planet ?></td>
		<td><?= date('Y-m-d H:i:s', $post->created_on) ?></td>
	</tr>
	<tr>
		<td colspan="2"><?= nl2br(html($post->message)) ?></td>
	</tr>
</table>
