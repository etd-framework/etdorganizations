<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_etdorganizations
 *
 * @version     1.0.0
 * @copyright	Copyright (C) 2017 - 2018 ETD Solutions. All rights reserved.
 * @license		GNU General Public License v3
 * @author		ETD Solutions http://www.etd-solutions.com
 */

// Protect from unauthorized access
defined('_JEXEC') or die('Restricted access to ETD Organizations');

class EtdOrganizationsControllerOrganization extends JControllerForm {

    // The prefix to use with controller messages.
    protected $text_prefix = 'COM_ETDORGANIZATIONS_ORGANIZATION';

    protected function allowEdit($data = array(), $key = 'id') {

        $allowed_users = $this->getModel()->getContacts($data[$key], array("a.user_id"));

        return parent::allowEdit($data) || in_array(JFactory::getUser()->id, $allowed_users);
    }
}