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
defined('_JEXEC') or die('Restricted access to ETD Organizations');

class EtdOrganizationsModelOrganizations extends JModelList {

    /**
     * Constructor.
     *
     * @param   array $config An optional associative array of configuration settings.
     *
     * @see     JController
     * @since   1.6
     */
    public function __construct($config = array()) {

        if (empty($config['filter_fields'])) {
            $config['filter_fields'] = array(
                'id', 'a.id',
                'title', 'a.title',
                'alias', 'a.alias',
                'catid', 'a.catid',
                'introtext', 'a.introtext',
                'fulltext', 'a.fulltext',
                'address', 'a.address',
                'suburb', 'a.suburb',
                'state', 'a.state',
                'country', 'a.country',
                'postcode', 'a.postcode',
                'telephone', 'a.telephone',
                'fax', 'a.fax',
                'email_to', 'a.email_to',
                'published', 'a.published',
                'images', 'a.images',
                'created', 'a.created',
                'created_by', 'a.created_by',
                'publish_up', 'a.publish_up',
                'publish_down', 'a.publish_down',
                'hits', 'a.hits'
            );
        }

        parent::__construct($config);
    }

    /**
     * Build an SQL query to load the list data.
     *
     * @return  JDatabaseQuery
     *
     * @since   1.6
     */
    protected function getListQuery() {

        $user  = JFactory::getUser();
        $db    = $this->getDbo();
        $query = $db->getQuery(true);

        // Select the required fields from the table.
        $query->select('a.*');
        $query->from('#__etdorganizations_organizations AS a');

        // Filter by category.
        $categoryId = $this->getState('filter.category_id');

        if (is_numeric($categoryId) && $categoryId > 0) {
            $query->where('a.catid = ' . (int) $categoryId);
        }

        // Filter by published state
        $published = $this->getState('filter.published');

        if (is_numeric($published)) {
            $query->where('a.state = ' . (int) $published);
        } elseif ($published === '') {
            $query->where('(a.state IN (0, 1))');
        }

        // Filter by search in title
        $search = $this->getState('filter.search');

        if (!empty($search)) {
            if (stripos($search, 'id:') === 0) {
                $query->where('a.id = ' . (int)substr($search, 3));
            } else {
                $search = $db->quote('%' . str_replace(' ', '%', $db->escape(trim($search), true) . '%'));
                $query->where('(a.title LIKE ' . $search . ')');
            }
        }

        // Add the list ordering clause.
        $orderCol  = $this->state->get('list.ordering', 'title');
        $orderDirn = $this->state->get('list.direction', 'ASC');

        $query->order($db->escape($orderCol . ' ' . $orderDirn));

        return $query;
    }

    /**
     * Method to get a store id based on model configuration state.
     *
     * This is necessary because the model is used by the component and
     * different modules that might need different sets of data or different
     * ordering requirements.
     *
     * @param   string $id A prefix for the store id.
     *
     * @return  string  A store id.
     *
     * @since   1.6
     */
    protected function getStoreId($id = '') {

        // Compile the store id.
        $id .= ':' . $this->getState('filter.search');
        $id .= ':' . $this->getState('filter.state');
        $id .= ':' . $this->getState('filter.catid');

        return parent::getStoreId($id);
    }

    /**
     * Returns a reference to the a Table object, always creating it.
     *
     * @param   string $type   The table type to instantiate
     * @param   string $prefix A prefix for the table class name. Optional.
     * @param   array  $config Configuration array for model. Optional.
     *
     * @return  JTable    A database object
     *
     * @since   1.6
     */
    public function getTable($type = 'Organization', $prefix = 'EtdOrganizationsTable', $config = array()) {

        return JTable::getInstance($type, $prefix, $config);
    }

    /**
     * Method to auto-populate the model state.
     *
     * Note. Calling getState in this method will result in recursion.
     *
     * @param   string $ordering  An optional ordering field.
     * @param   string $direction An optional direction (asc|desc).
     *
     * @return  void
     *
     * @since   1.6
     */
    protected function populateState($ordering = null, $direction = null) {

        // Load the filter state.
        $search = $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
        $this->setState('filter.search', $search);

        $state = $this->getUserStateFromRequest($this->context . '.filter.state', 'filter_state', '', 'string');

        // Load the parameters.
        $params = JComponentHelper::getParams('com_etdorganizations');
        $this->setState('params', $params);

        // List state information.
        parent::populateState('a.id', 'desc');

        $this->setState('list.limit', 0 );
    }
}
