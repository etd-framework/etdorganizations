<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_etdorganizations
 *
 * @version     1.2.0
 * @copyright	Copyright (C) 2017 - 2018 ETD Solutions. All rights reserved.
 * @license		GNU General Public License v3
 * @author		ETD Solutions http://www.etd-solutions.com
 */

// Protect from unauthorized access
defined('_JEXEC') or die('Restricted access to ETD Organizations');

class EtdOrganizationsController extends JControllerLegacy {

    // The default view for the display method.
    protected $default_view = 'organizations';

    public function display($cachable = false, $urlparams = false) {

        require_once JPATH_COMPONENT . '/helpers/etdorganizations.php';

        $view   = $this->input->get('view', 'organizations');
        $layout = $this->input->get('layout', 'default');
        $id     = $this->input->getInt('id');

        // Check for edit form.
        if ($view == 'organization' && $layout == 'edit' && !$this->checkEditId('com_etdorganizations.edit.organization', $id)) {

            // Somehow the person just went to the form - we don't allow that.
            $this->setError(JText::sprintf('JLIB_APPLICATION_ERROR_UNHELD_ID', $id));
            $this->setMessage($this->getError(), 'error');
            $this->setRedirect(JRoute::_('index.php?option=com_etdorganizations&view=organizations', false));

            return false;
        }

        parent::display();

        return $this;
    }
}
