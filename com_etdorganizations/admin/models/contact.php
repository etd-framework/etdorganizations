<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_etdorganizations
 *
 * @version     1.0.1
 * @copyright	Copyright (C) 2017 - 2018 ETD Solutions. All rights reserved.
 * @license		GNU General Public License v3
 * @author		ETD Solutions http://www.etd-solutions.com
 */

// Protect from unauthorized access
defined('_JEXEC') or die('Restricted access to ETD Organizations');

require_once JPATH_ADMINISTRATOR . "/components/com_contact/models/contact.php";

class EtdOrganizationsModelContact extends ContactModelContact {

    /**
     * The type alias for this content type.
     *
     * @var      string
     * @since    3.2
     */
    public $typeAlias = 'com_etdorganizations.contact';


    /**
     * Method to get the record form.
     *
     * @param   array $data Data for the form. [optional]
     * @param   boolean $loadData True if the form is to load its own data (default case), false if not. [optional]
     *
     * @return  mixed  A JForm object on success, false on failure
     *
     * @since   1.6
     */
    public function getForm($data = array(), $loadData = true) {

        // Get the form.
        $form = $this->loadForm($this->typeAlias, 'contact', array('control' => 'jform', 'load_data' => $loadData));

        if (empty($form)) {
            return false;
        }

        return $form;
    }

    /**
     * Method to check if you can edit your own existing record.
     *
     * Extended classes can override this if necessary.
     *
     * @param   array   $data  An array of input data.
     * @param   string  $key   The name of the key for the primary key; default is id.
     *
     * @return  boolean
     *
     * @since   12.2
     */
    protected function allowEditOwn($data = array(), $key = 'id') {

        return JFactory::getUser()->authorise('core.edit.own', 'com_contact');
    }

    /**
     * Method to check if you can delete an existing record.
     *
     * Extended classes can override this if necessary.
     *
     * @param   array   $data  An array of input data.
     * @param   string  $key   The name of the key for the primary key; default is id.
     *
     * @return  boolean
     *
     * @since   12.2
     */
    public function canDelete($data = array(), $key = 'id') {

        return JFactory::getUser()->authorise('core.delete', 'com_contact')  || $this->allowEditOwn($data, $key);
    }

    /**
     * Method to save the form data.
     *
     * @param   array  $data  The form data.
     *
     * @return  boolean  True on success.
     *
     * @since   3.0
     */
    public function save($data) {

        $data['published'] = 1;

        return parent::save($data);
    }
}
