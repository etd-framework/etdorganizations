<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_etdorganizations
 *
 * @version     1.1.1
 * @copyright	Copyright (C) 2017 - 2018 ETD Solutions. All rights reserved.
 * @license		GNU General Public License v3
 * @author		ETD Solutions http://www.etd-solutions.com
 */

// Protect from unauthorized access
defined('_JEXEC') or die('Restricted access to ETD Organizations');

// Include the component HTML helpers.
JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');

JHtml::_('behavior.formvalidator');
JHtml::_('formbehavior.chosen', 'select');

JHtml::_('jquery.framework');

$app = JFactory::getApplication();

if ($app->getUserState('hide_modal') === true) {
	$app->setUserState('hide_modal', false);
	$js = "jQuery(document).ready(function (){";
	$js .= "parent.editContact(" . (int)$this->item->id . ",'" . addslashes($this->item->name) . "','" . addslashes($this->item->image) . "');";
	$js .= "parent.jModalClose()";
	$js .= "});";
	JFactory::getDocument()->addScriptDeclaration($js);
}

?>

<form action="<?php echo JRoute::_('index.php?option=com_etdorganizations&layout=edit&id=' . (int) $this->item->id); ?>" method="post" name="adminForm" id="contact-form" class="form-validate">

	<div class="pull-right">
		<button class="btn btn-success button-save-selected" type="submit" data-dismiss="modal"><?php echo JText::_('JTOOLBAR_APPLY') ?></button>
		<button class="btn button-cancel" type="button" onclick="window.parent.jQuery('.modal.in').modal('hide');<?php if (!$this->state->get('field.id')) : ?>parent.jModalClose();<?php endif ?>" data-dismiss="modal"><?php echo JText::_('JCANCEL') ?></button>
	</div>

	<?php echo JLayoutHelper::render('joomla.edit.title_alias', $this); ?>

	<div class="form-horizontal">
		<div class="row-fluid">
			<div class="span12">
				<div class="row-fluid form-horizontal-desktop">
					<div class="span6">
						<?php echo $this->form->renderField('image'); ?>
						<?php echo $this->form->renderField('con_position'); ?>
						<?php echo $this->form->renderField('email_to'); ?>
					</div>
					<div class="span6">
						<?php echo $this->form->renderField('telephone'); ?>
						<?php echo $this->form->renderField('mobile'); ?>
						<?php echo $this->form->renderField('fax'); ?>
					</div>
				</div>
			</div>
		</div>
	</div>
	<input type="hidden" name="task" value="contact.save" />
	<?php echo JHtml::_('form.token'); ?>
</form>
