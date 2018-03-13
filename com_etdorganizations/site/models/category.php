<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_etdorganizations
 *
 * @version     1.0.4
 * @copyright	Copyright (C) 2017 - 2018 ETD Solutions. All rights reserved.
 * @license		GNU General Public License v3
 * @author		ETD Solutions http://www.etd-solutions.com
 **/

// no direct access
defined('_JEXEC') or die('Restricted access to ETD Organizations');

class EtdOrganizationsModelCategory extends JModelList {

    public function __construct($config = array()) {

        if (empty($config['filter_fields'])) {
            $config['filter_fields'] = array(
                'id', 'a.id',
                'title', 'a.title',
                'state', 'a.state',
                'ordering', 'a.ordering',
                'publish_up', 'a.publish_up',
                'publish_down', 'a.publish_down',
                'created', 'a.created',
                'hits','a.hits'
            );
        }

        parent::__construct($config);
    }

    /**
     * Method to build an SQL query to load the list data.
     *
     * @return  string    An SQL query
     *
     * @since   1.6
     */
    protected function getListQuery() {

        // Create a new query object.
        $db    = $this->getDbo();
        $query = $db->getQuery(true);

        // Select required fields from the categories.
        $query->select($this->getState('list.select', 'a.*, c.alias AS cat_alias'))
            ->from($db->quoteName('#__etdorganizations') . ' AS a')
            ->leftJoin($db->quoteName('#__categories') . ' AS C ON c.id = a.catid')
            ->where('a.catid = ' . $db->quote($this->getState('category.id')));

        // Filter by state
        $state = $this->getState('filter.state');
        if (is_numeric($state)) {
            $query->where('a.state = ' . (int) $state);
        }

        // Define null and now dates
        $nullDate = $db->quote($db->getNullDate());
        $nowDate  = $db->quote(JFactory::getDate()->toSql());

        // Filter by start and end dates.
        $query->where('(a.publish_up = ' . $nullDate . ' OR a.publish_up <= ' . $nowDate . ')')
              ->where('(a.publish_down = ' . $nullDate . ' OR a.publish_down >= ' . $nowDate . ')');

        // Add the list ordering clause.
        $query->order($db->escape($this->getState('list.ordering', 'a.ordering')) . ' ' . $db->escape($this->getState('list.direction', 'ASC')));

        return $query;
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

        $app    = JFactory::getApplication();
        $params = $app->getParams();

        $pk = $app->input->getInt('id');
        $this->setState('category.id', $pk);

        // List state information
        $limit = $app->getUserStateFromRequest('global.list.limit', 'limit', $params->get('list_limit', $app->get('list_limit')), 'uint');
        $this->setState('list.limit', $limit);

        $limitstart = $app->input->get('limitstart', 0, 'uint');
        $this->setState('list.start', $limitstart);

        $orderCol = $app->input->get('filter_order', $params->get('list_ordering', 'ordering'));
        if (!in_array($orderCol, $this->filter_fields)) {
            $orderCol = 'ordering';
        }
        $this->setState('list.ordering', $orderCol);

        $listOrder = $app->input->get('filter_order_Dir', $params->get('list_direction', 'ASC'));
        if (!in_array(strtoupper($listOrder), array(
            'ASC',
            'DESC',
            ''
        ))
        ) {
            $listOrder = 'ASC';
        }
        $this->setState('list.direction', $listOrder);

        $user = JFactory::getUser();
        if ((!$user->authorise('core.edit.state', 'com_etdorganizations')) && (!$user->authorise('core.edit', 'com_etdorganizations'))) {
            // Limit to published for people who can't edit or edit.state.
            $this->setState('filter.state', 1);

            // Filter by start and end dates.
            $this->setState('filter.publish_date', true);
        }

        // Load the parameters
        $this->setState('params', $params);
    }
}
