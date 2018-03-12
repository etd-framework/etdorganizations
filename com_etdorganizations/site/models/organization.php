<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_etdorganizations
 *
 * @version     1.0.3
 * @copyright	Copyright (C) 2017 - 2018 ETD Solutions. All rights reserved.
 * @license		GNU General Public License v3
 * @author		ETD Solutions http://www.etd-solutions.com
 */

// Protect from unauthorized access
defined('_JEXEC') or die('Restricted access to ETD Organizations');

class EtdOrganizationsModelOrganization extends JModelItem {

    protected $_name = 'organization';

    /**
     * Method to auto-populate the model state.
     *
     * Note. Calling getState in this method will result in recursion.
     *
     * @since   1.6
     *
     * @return void
     */
    protected function populateState() {
        $app = JFactory::getApplication('site');

        // Load state from the request.
        $pk = $app->input->getInt('id');
        $this->setState($this->_name . '.id', $pk);
    }

    /**
     * Method to get organization data.
     *
     * @param   integer  $pk  The id of the organization.
     *
     * @return  mixed  Menu item data object on success, false on failure.
     */
    public function getItem($pk = null) {

        $this->populateState();

        $pk = (!empty($pk)) ? $pk : (int) $this->getState($this->_name . '.id', 0);

        if ($this->_item === null) {
            $this->_item = array();
        }

        if (!isset($this->_item[$pk])) {
            try {
                $query = $this->_db->getQuery(true);

                $query->select('a.*')
                    ->from($this->_db->quoteName('#__etdorganizations_organizations') . ' AS a')
                    ->where('a.id = ' . (int) $pk);

                $this->_db->setQuery($query);

                $data = $this->_db->loadObject();

                if (empty($data)) {
                    return JError::raiseError(404, JText::_('COM_ETDORGANIZATIONS_ERROR_ORGANIZATION_NOT_FOUND'));
                } else {
                    $data->contacts = $this->getContacts($data->id);
                }

                $this->_item[$pk] = $data;
            }
            catch (Exception $e) {
                if ($e->getCode() == 404) {
                    // Need to go thru the error handler to allow Redirect to work.
                    JError::raiseError(404, $e->getMessage());
                } else {
                    $this->setError($e);
                    $this->_item[$pk] = false;
                }
            }
        }

        return $this->_item[$pk];
    }

    /**
     * Increment the hit counter for the image.
     *
     * @param   integer  $pk  Optional primary key of the image to increment.
     *
     * @return  boolean  True if successful; false otherwise and internal error set.
     */
    public function hit($pk) {

        $table = $this->getTable();
        $table->load($pk);
        $table->hit($pk);

        return $table->hits;
    }

    /**
     * @param null $id
     * @return mixed
     */
    public function getContacts($id = null) {

        $id = (empty($id)) ? $this->getState($this->context . '.id') : $id;

        $query = $this->_db->getQuery(true);

        $query->select('a.*')
            ->from($this->_db->quoteName('#__contact_details') . ' AS a')
            ->leftJoin($this->_db->quoteName("#__etdorganizations_organization_contacts") . " AS b ON a.id = b.contact_id")
            ->where("b.organization_id = " . (int) $id);

        $this->_db->setQuery($query);

        return $this->_db->loadObjectList();
    }

    /**
     * Returns a JTable object, always creating it.
     *
     * @param   string $type The table type to instantiate. [optional]
     * @param   string $prefix A prefix for the table class name. [optional]
     * @param   array $config Configuration array for model. [optional]
     *
     * @return  JTable  A database object
     *
     * @since   1.6
     */
    public function getTable($type = 'Organization', $prefix = 'EtdOrganizationsTable', $config = array()) {

        JTable::addIncludePath(JPATH_COMPONENT_ADMINISTRATOR . "/com_etdorganizations/tables");
        return JTable::getInstance($type, $prefix, $config);
    }

}