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
<h1>Languages</h1>
<?php
echo $this->Html->link('Create', array(
	'controller' => 'languages',
	'action' => 'create'
		), array('class' => 'btn'));

echo $this->element('pagination/pagination');
?>

<table class="table table-bordered table-striped" style="margin-top: 10px;">
	<thead>
		<tr>
			<th><?= $this->Paginator->sort('id'); ?></th>
			<th><?= $this->Paginator->sort('name'); ?></th>
			<th><?= $this->Paginator->sort('code'); ?></th>		
			<th><?= $this->Paginator->sort('created'); ?></th>
			<th><?= $this->Paginator->sort('modified'); ?></th>
			<th>Actions</th>
		</tr>
	</thead>
	<tbody>
		<?php
		foreach ($data as $one) {
			$language = $one['Language'];
			?>
			<tr>
				<td><?= $language['id']; ?></td>
				<td><?= $this->Html->link($language['name'], array('action' => 'edit', $language['id'])); ?></td>
				<td><?= $language['code']; ?></td>
				<td title="<?= $language['created']; ?>"><?= $this->Time->timeAgoInWords($language['created']); ?></td>
				<td title="<?= $language['modified']; ?>"><?= $this->Time->timeAgoInWords($language['modified']); ?></td>
				<td>
					<div class="btn-group"><button class="btn dropdown-toggle" data-toggle="dropdown"><i class="icon-tasks"></i><span class="caret"></span></button>
						<ul class="dropdown-menu pull-right">
							<?= $this->Html->tag('li', $this->Html->link('Edit', array('action' => 'edit', $language['id']))); ?>
							<?=
							$this->Html->tag('li', $this->Html->link('Delete', array('action' => 'delete', $language['id']), array(
										'class' => 'btn-danger'
											), "Are you sure want to delete {$language['name']} language?")
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
