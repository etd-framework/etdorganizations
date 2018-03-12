<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_etdorganizations
 *
 * @version     1.0.3
 * @copyright	Copyright (C) 2017 - 2018 ETD Solutions. All rights reserved.
 * @license		GNU General Public License v3
 * @author		ETD Solutions http://www.etd-solutions.com
 */

// Protect from unauthorized access
defined('_JEXEC') or die('Restricted access to ETDOrganizations');

JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');

JHtml::_('bootstrap.tooltip');
//JHtml::_('behavior.multiselect');
JHtml::_('formbehavior.chosen', 'select');

$params     = $this->state->get('params');
$user		= JFactory::getUser();
$userId		= $user->get('id');
$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));
$canOrder	= $user->authorise('core.edit.state', 'com_etdorganizations');
$archived	= $this->state->get('filter.state') == 2 ? true : false;
$trashed	= $this->state->get('filter.state') == -2 ? true : false;

?>

<form action="<?php echo JRoute::_('index.php?option=com_etdorganizations&view=organizations'); ?>" method="post" name="adminForm" id="adminForm" >
	<div id="j-sidebar-container" class="span2">
		<?php echo $this->sidebar; ?>
	</div>
	<div id="j-main-container" class="span10">
		<?php
		// Search tools bar
		echo JLayoutHelper::render('joomla.searchtools.default', array('view' => $this));
		?>
		<?php if (empty($this->items)) : ?>
			<div class="alert alert-no-items">
				<?php echo JText::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
			</div>
		<?php else : ?>
			<table class="table table-striped" id="articleList">
				<thead>
					<tr>
						<th width="1%" class="center">
							<?php echo JHtml::_('grid.checkall'); ?>
						</th>
						<th width="1%" class="nowrap center">
							<?php echo JHtml::_('searchtools.sort', 'JSTATUS', 'a.published', $listDirn, $listOrder); ?>
						</th>
						<th>
							<?php echo JHtml::_('searchtools.sort', 'COM_ETDORGANIZATIONS_ORGANIZATIONS_HEADING_TITLE', 'a.title', $listDirn, $listOrder); ?>
						</th>
						<th width="1%" class="nowrap center hidden-phone">
							<?php echo JHtml::_('searchtools.sort', 'JGRID_HEADING_ID', 'a.id', $listDirn, $listOrder); ?>
						</th>
						<th width="10%" class="nowrap hidden-phone">
							<?php echo JHtml::_('searchtools.sort', 'JDATE', 'a.created', $listDirn, $listOrder); ?>
						</th>
						<th width="1%" class="nowrap hidden-phone">
							<?php echo JHtml::_('searchtools.sort', 'JGLOBAL_HITS', 'a.hits', $listDirn, $listOrder); ?>
						</th>
					</tr>
				</thead>
				<tfoot>
					<tr>
						<td colspan="6">
							<?php echo $this->pagination->getListFooter(); ?>
						</td>
					</tr>
				</tfoot>
				<tbody>
					<?php foreach ($this->items as $i => $item) :
						$ordering  = ($listOrder == 'id');
						$canCreate  = $user->authorise('core.create',     'com_etdorganizations');
						$canEdit    = $user->authorise('core.edit',       'com_etdorganizations');
						$canChange  = $user->authorise('core.edit.state', 'com_etdorganizations');
						$isContact  = in_array($item->id, $this->user_organizations);
						?>
						<tr class="row<?php echo $i % 2; ?>">
							<td class="center">
								<?php echo JHtml::_('grid.id', $i, $item->id); ?>
							</td>
							<td class="center">
								<div class="btn-group">
									<?php echo JHtml::_('jgrid.published', $item->published, $i, 'organizations.', $canChange, 'cb', $item->publish_up, $item->publish_down); ?>
									<?php
									// Create dropdown items
									$action = $archived ? 'unarchive' : 'archive';
									JHtml::_('actionsdropdown.' . $action, 'cb' . $i, 'organizations');

									$action = $trashed ? 'untrash' : 'trash';
									JHtml::_('actionsdropdown.' . $action, 'cb' . $i, 'organizations');

									// Render dropdown list
									echo JHtml::_('actionsdropdown.render', $this->escape($item->title));
									?>
								</div>
							</td>
							<td class="has-context">
								<div class="pull-left">
									<?php if ($canEdit || $isContact) : ?>
										<a href="<?php echo JRoute::_('index.php?option=com_etdorganizations&task=organization.edit&id=' . (int) $item->id); ?>">
											<?php if (empty($item->title)) : ?>Sans titre<?php else: ?><?php echo $item->title; ?><?php endif; ?></a>
									<?php else : ?>
										<?php if (empty($item->title)) : ?>Sans titre<?php else: ?><?php echo $item->title; ?><?php endif; ?>
									<?php endif; ?>
								</div>
							</td>
							<td class="center hidden-phone">
								<?php echo $item->id; ?>
							</td>
							<td class="nowrap small hidden-phone">
								<?php echo JHtml::_('date', $item->created, JText::_('DATE_FORMAT_LC4')); ?>
							</td>
							<td class="hidden-phone">
								<?php echo (int) $item->hits; ?>
							</td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		<?php endif; ?>
		<?php //Load the batch processing form. ?>
		<?php //echo $this->loadTemplate('batch'); ?>

		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>
