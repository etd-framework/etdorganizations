<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_etdorganizations
 *
 * @version     1.0.4
 * @copyright	Copyright (C) 2017 - 2018 ETD Solutions. All rights reserved.
 * @license		GNU General Public License v3
 * @author		ETD Solutions http://www.etd-solutions.com
 */

// Protect from unauthorized access
defined('_JEXEC') or die('Restricted access to ETDOrganizations');

class EtdOrganizationsViewOrganization extends JViewLegacy {

    // The name of the view
    protected $_name = "organization";

    protected $form;
    protected $item;
    protected $state;
    protected $contacts;

    /**
     * Display the view
     *
     * @param   string $tpl The name of the template file to parse; automatically searches through the template paths.
     *
     * @return  void
     */
    public function display($tpl = null) {

        $model = $this->getModel();

        // Initialiase variables.
        $this->form     = $this->get('Form');
        $this->item     = $this->get('Item');
        $this->state    = $this->get('State');
        $this->contacts = $model->getContacts($this->item->id);

        // Check for errors.
        if (count($errors = $this->get('Errors'))) {
            JError::raiseError(500, implode("\n", $errors));

            return false;
        }

        $this->addToolbar();

        parent::display($tpl);
    }

    /**
     * Add the page title and toolbar.
     *
     * @return  void
     *
     * @since   1.6
     */
    protected function addToolbar() {

        JFactory::getApplication()->input->set('hidemainmenu', true);

        $user = JFactory::getUser();
        $isNew = ($this->item->id == 0);

        // Since we don't track these assets at the item level, use the category id.
        $canDo = JHelperContent::getActions('com_etdorganizations');

        JToolbarHelper::title($isNew ? JText::_('COM_ETDORGANIZATIONS_MANAGER_' . strtoupper($this->_name) . '_NEW') : JText::_('COM_ETDORGANIZATIONS_MANAGER_' . strtoupper($this->_name) . '_EDIT'), 'pencil');

        // If not checked out, can save the item.
        if ($canDo->get('core.edit') || $canDo->get('core.edit.own')) {
            JToolbarHelper::apply($this->_name . '.apply');
            JToolbarHelper::save($this->_name . '.save');

            if ($canDo->get('core.create')) {
                JToolbarHelper::save2new($this->_name . '.save2new');
            }
        }

        // If an existing item, can save to a copy.
        if (!$isNew && $canDo->get('core.create')) {
            JToolbarHelper::save2copy($this->_name . '.save2copy');
        }

        if (empty($this->item->id)) {
            JToolbarHelper::cancel($this->_name . '.cancel');
        } else {

            JToolbarHelper::cancel($this->_name . '.cancel', 'JTOOLBAR_CLOSE');
        }
    }
}
