<?php
/**
 * Author: imsamurai <im.samuray@gmail.com>
 * Date: Jan 14, 2014
 * Time: 6:28:14 PM
 * Format: http://book.cakephp.org/2.0/en/views.html
 * 
 */
/* @var $this View */
?>
<h1>Messages</h1>
<?php 
echo $this->element('form/localization/messages/search');
echo $this->Html->link('Create', array(
	'controller' => 'messages',
	'action' => 'create'
		), array('class' => 'btn'));
echo $this->Html->link('Export all', array(
	'controller' => 'messages',
	'action' => 'export'
		), array('class' => 'btn btn-inverse'), "Are you sure want to export all messages?");

echo $this->element('pagination/pagination');
?>

<table class="table table-bordered table-striped" style="margin-top: 10px;">
	<thead>
		<tr>
			<th><?= $this->Paginator->sort('id'); ?></th>	
			<th><?= $this->Paginator->sort('name'); ?></th>	
			<th><?= $this->Paginator->sort('js'); ?></th>		
			<th>Not translated to</th>
			<th><?= $this->Paginator->sort('created'); ?></th>
			<th><?= $this->Paginator->sort('modified'); ?></th>
			<th>Actions</th>
		</tr>
	</thead>
	<tbody>
		<?php
		foreach ($data as $one) {
			$message = $one['Message'];
			$translatedLangguages = Hash::extract($one, 'Translations.{n}[translated=1].language_id');
			$notTranslatedLangguages = array_diff_key($languages, array_flip($translatedLangguages));
			?>
			<tr>
				<td><?= $message['id']; ?></td>
				<td><?= $this->Html->link($message['name'], array('action' => 'edit', $message['id'])); ?></td>
				<td><?= $message['js'] ? 'yes' : 'no'; ?></td>
				<td><?= implode(', ', $notTranslatedLangguages); ?></td>
				<td title="<?= $message['created']; ?>"><?= $this->Time->timeAgoInWords($message['created']); ?></td>
				<td title="<?= $message['modified']; ?>"><?= $this->Time->timeAgoInWords($message['modified']); ?></td>
				<td>
					<div class="btn-group"><button class="btn dropdown-toggle" data-toggle="dropdown"><i class="icon-tasks"></i><span class="caret"></span></button>
						<ul class="dropdown-menu pull-right">
							<?= $this->Html->tag('li', $this->Html->link('Edit', array('action' => 'edit', $message['id']))); ?>
							<?=
							$this->Html->tag('li', $this->Html->link('Delete', array('action' => 'delete', $message['id']), array(
										'class' => 'btn-danger'
											), "Are you sure want to delete ".Configure::read('Localization.prefix').$message['id']." message?")
							);
							?>
						</ul>
					</div>

				</td>
			</tr>
			<?php
		}
		?>
	</tbody>
</table>

<?php
echo $this->element('pagination/pagination');