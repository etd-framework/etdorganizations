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
defined('_JEXEC') or die('Restricted access to ETDOrganizations');

$doc = JFactory::getDocument();

JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');
JHtml::_('behavior.formvalidator');
JHtml::_('formbehavior.chosen', 'select');

JHtml::_('jquery.framework');
JHtml::_('jquery.ui', array('core', 'sortable'));

$doc->addStyleSheet(JUri::root() . 'media/com_etdorganizations/dist/css/organization.min.css')
    ->addScript(JUri::root() . 'media/com_etdorganizations/dist/js/organization.min.js');
?>

<form action="<?php echo JRoute::_('index.php?option=com_etdorganizations&layout=edit&id=' . (int) $this->item->id); ?>" id="organization-form" method="post" name="adminForm" class="form-validate" enctype="multipart/form-data">

    <?php echo JLayoutHelper::render('joomla.edit.title_alias', $this); ?>

    <div class="form-horizontal">
        <?php echo JHtml::_('bootstrap.startTabSet', 'myTab', array('active' => 'general')); ?>

        <?php echo JHtml::_('bootstrap.addTab', 'myTab', 'general', JText::_('COM_ETDORGANIZATIONS_ORGANIZATION_DESCRIPTION', true)); ?>
        <div class="row-fluid">
            <div class="span9">
                <fieldset class="adminform">
                    <?php echo $this->form->getInput('organizationtext'); ?>
                </fieldset>
            </div>
            <div class="span3">
                <?php echo JLayoutHelper::render('joomla.edit.global', $this); ?>
                <fieldset class="form-vertical">
                    <?php echo $this->form->getControlGroup('created_by'); ?>
                </fieldset>
            </div>
        </div>
        <?php echo JHtml::_('bootstrap.endTab'); ?>

        <?php echo JHtml::_('bootstrap.addTab', 'myTab', 'details', JText::_('COM_ETDORGANIZATIONS_ORGANIZATION_DETAILS', true)); ?>
        <div class="row-fluid">
            <div class="span12">
                <div class="row-fluid form-horizontal-desktop">
                    <div class="span6">
                        <?php echo $this->form->renderField('address'); ?>
                        <?php echo $this->form->renderField('postcode'); ?>
                        <?php echo $this->form->renderField('suburb'); ?>
                        <?php echo $this->form->renderField('state'); ?>
                        <?php echo $this->form->renderField('country'); ?>
                    </div>
                    <div class="span6">
                        <?php echo $this->form->renderField('phone'); ?>
                        <?php echo $this->form->renderField('fax'); ?>
                        <?php echo $this->form->renderField('email_to'); ?>
                        <?php echo $this->form->renderField('website'); ?>
                    </div>
                </div>
            </div>
        </div>
        <?php echo JHtml::_('bootstrap.endTab'); ?>

        <?php // Do not show the images and links options if the edit form is configured not to. ?>
        <?php echo JHtml::_('bootstrap.addTab', 'myTab', 'images', JText::_('COM_ETDORGANIZATIONS_ORGANIZATION_IMAGES', true)); ?>
        <div class="row-fluid form-horizontal-desktop">
            <div class="span12">
                <?php echo $this->form->getControlGroup('images'); ?>
                <?php foreach ($this->form->getGroup('images') as $field) : ?>
                    <?php echo $field->getControlGroup(); ?>
                <?php endforeach; ?>
            </div>
        </div>
        <?php echo JHtml::_('bootstrap.endTab'); ?>

        <?php echo JHtml::_('bootstrap.addTab', 'myTab', 'contacts', JText::_('COM_ETDORGANIZATIONS_ORGANIZATION_CONTACTS', true)); ?>
        <div class="row-fluid form-horizontal-desktop">
            <div class="span12">
                <div id="contact-thumbnails">
                    <?php if (!empty($this->contacts)) : ?>
                        <?php foreach($this->contacts as $contact) : ?>
                            <?php $doc->addScriptDeclaration("jQuery(function($) {SqueezeBox.assign($('#contact-" . $contact->id . " a.modal').get(), {parse: 'rel'});});"); ?>
                            <div class="thumbnail" id="contact-<?php echo $contact->id; ?>">
                                <input type="hidden" name="jform[contacts][]" value="<?php echo $contact->id; ?>">
                                <?php if($contact->image) : ?><div class="img" style="background-image:url('<?php echo JUri::root() . $contact->image; ?>');"></div><?php endif ; ?>
                                <div class="caption">
                                    <h3><?php echo $contact->name; ?></h3>
                                    <p>
                                        <a href="index.php?option=com_etdorganizations&view=contact&tmpl=component&layout=edit&id=<?php echo $contact->id; ?>" class="modal btn" title="<?php echo JText::_('COM_ETDORGANIZATIONS_ORGANIZATION_EDIT_CONTACT') ?>" rel="{handler: 'iframe', size: {x: 800, y: 500}}"><span class="icon-apply"></span><?php echo JText::_('COM_ETDORGANIZATIONS_ORGANIZATION_EDIT_CONTACT') ?></a>
                                        <a href="#" class="btn btn-remove" role="button" data-id="<?php echo $contact->id; ?>"><span class="icon-cancel"></span><?php echo JText::_('COM_ETDORGANIZATIONS_ORGANIZATION_REMOVE_CONTACT') ?></a>
                                        <a href="#" class="btn btn-delete" role="button" data-id="<?php echo $contact->id; ?>"><span class="icon-trash"></span><?php echo JText::_('COM_ETDORGANIZATIONS_ORGANIZATION_DELETE_CONTACT') ?></a>
                                    </p>
                                </div>
                            </div>
                        <?php endforeach ; ?>
                    <?php endif ; ?>
                </div>
                <?php echo $this->form->getControlGroups('contacts'); ?>
            </div>
        </div>
        <?php echo JHtml::_('bootstrap.endTab'); ?>

        <?php echo JHtml::_('bootstrap.addTab', 'myTab', 'publishing', JText::_('JGLOBAL_FIELDSET_PUBLISHING', true)); ?>
        <div class="row-fluid form-horizontal-desktop">
            <div class="span12">
                <?php echo $this->form->getControlGroups('advanced'); ?>
            </div>
        </div>
        <?php echo JHtml::_('bootstrap.endTab'); ?>

        <?php echo JHtml::_('bootstrap.endTabSet'); ?>
    </div>

    <input type="hidden" name="task" value="" />
    <?php echo JHtml::_('form.token'); ?>
</form>
