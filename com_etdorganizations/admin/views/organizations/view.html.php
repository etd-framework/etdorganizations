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

class EtdOrganizationsViewOrganizations extends JViewLegacy {

    // The name of the item view
    protected $_itemName = 'organization';

    // The name of the view
    protected $_name = 'organizations';

    protected $items;

    protected $user_organizations;

    protected $pagination;

    protected $state;

    /**
     * Method to display the view.
     *
     * @param   string $tpl A template file to load. [optional]
     *
     * @return  mixed  A string if successful, otherwise a JError object.
     *
     * @since   1.6
     */
    public function display($tpl = null) {

        require_once JPATH_COMPONENT_ADMINISTRATOR . '/helpers/etdorganizations.php';

        $this->items          = $this->get('Items');
        $this->pagination     = $this->get('Pagination');
        $this->state          = $this->get('State');
        $this->filterForm     = $this->get('FilterForm');
        $this->activeFilters  = $this->get('ActiveFilters');
        $this->user_organizations = $this->get('UserOrganizations');

        // Check for errors.
        if (count($errors = $this->get('Errors'))) {
            JError::raiseError(500, implode("\n", $errors));

            return false;
        }

        EtdOrganizationsHelper::addSubmenu($this->_name);

        $this->addToolbar();
        $this->sidebar = JHtmlSidebar::render();

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

        $canDo = JHelperContent::getActions('com_etdorganizations');
        $user  = JFactory::getUser();

        // Get the toolbar object instance
        $bar = JToolBar::getInstance('toolbar');

        JToolbarHelper::title(JText::_('COM_ETDORGANIZATIONS_MANAGER_' . strtoupper($this->_name)), 'home');

        if ($canDo->get('core.create')) {
            JToolbarHelper::addNew($this->_itemName . '.add');
        }

        if ($canDo->get('core.edit')) {
            JToolbarHelper::editList($this->_itemName . '.edit');
        }

        if ($canDo->get('core.edit.state')) {
            if ($this->state->get('filter.published') != 2) {
                JToolbarHelper::publish($this->_name . '.publish', 'JTOOLBAR_PUBLISH', true);
                JToolbarHelper::unpublish($this->_name . '.unpublish', 'JTOOLBAR_UNPUBLISH', true);
            }

            if ($this->state->get('filter.published') != -1) {
                if ($this->state->get('filter.published') != 2) {
                    JToolbarHelper::archiveList($this->_name . '.archive');
                } elseif ($this->state->get('filter.published') == 2) {
                    JToolbarHelper::unarchiveList($this->_name . '.publish');
                }
            }
        }

        // Add a batch button
        if ($user->authorise('core.create', 'com_etdorganizations') && $user->authorise('core.edit', 'com_etdorganizations') && $user->authorise('core.edit.state', 'com_etdorganizations')) {
            JHtml::_('bootstrap.modal', 'collapseModal');
            $title = JText::_('JTOOLBAR_BATCH');

            // Instantiate a new JLayoutFile instance and render the batch button
            $layout = new JLayoutFile('joomla.toolbar.batch');

            $dhtml = $layout->render(array('title' => $title));
            $bar->appendButton('Custom', $dhtml, 'batch');
        }

        if ($canDo->get('core.delete')) {
            JToolbarHelper::deleteList('', $this->_name . '.delete');
        }

        if ($user->authorise('core.admin', 'com_etdorganizations')) {
            JToolbarHelper::preferences('com_etdorganizations@');
        }
    }

    /**
     * Returns an array of fields the table can be sorted by
     *
     * @return  array  Array containing the field name to sort by as the key and display text as value
     *
     * @since   3.0
     */
    protected function getSortFields() {

        return array(
            'ordering'     => JText::_('JGRID_HEADING_ORDERING'),
            'a.published'      => JText::_('JSTATUS'),
            'a.id'         => JText::_('JGRID_HEADING_ID')
        );
    }
}
