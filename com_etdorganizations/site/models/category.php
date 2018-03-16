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
                'publish_up', 'a.publish_up',
                'publish_down', 'a.publish_down',
                'created', 'a.created',
                'hits','a.hits'
            );
        }

        parent::__construct($config);
    }

    /**
     * Method to get an array of data items.
     *
     * @return  mixed  An array of data items on success, false on failure.
     *
     * @since   1.6
     */
    public function getItems() {

        $items = parent::getItems();

        if (!empty($items)) {

            $config = JComponentHelper::getParams('com_etdorganizations');
            $sizes  = json_decode($config->get('sizes', '[]'));

            foreach ($items as &$item) {

                // Retrieve the identifiers of the contacts of the organization.
                $query = $this->_db->getQuery(true);

                $query->select('contact_id')
                    ->from($this->_db->quoteName('#__etdorganizations_organization_contacts'))
                    ->where('organization_id = ' . (int) $item->id);

                $this->_db->setQuery($query);
                $this->_db->execute();

                $contacts_id = $this->_db->loadAssoc();

                // Retrieve the information of the existing contacts.
                if (!empty($contacts_id)) {
                    foreach ($contacts_id as $contact_id) {

                        $query = $this->_db->getQuery(true);

                        $query->select('a.name, a.image')
                            ->from($this->_db->quoteName('#__contact_details') . ' AS a')
                            ->where('a.id = ' . (int) $contact_id);

                        $this->_db->setQuery($query);
                        $this->_db->execute();

                        $item->contacts[] = $this->_db->loadObject();
                    }
                }

                $images = json_decode($item->images);

                // Get the different sizes of image
                if ($images['logo'] || $images['image_fulltext']) {

                    if ($images['logo']) {

                        $logo = pathinfo($images['logo']);
                        $item->logo = new stdClass();

                        foreach ($sizes as $size) {
                            $item->logo->{$size->name} = $logo['dirname'] . "/" . $logo['basename'] . "_" . $size->name;
                        }
                    }

                    if ($images['image_fulltext']) {

                        $image_fulltext = pathinfo($images['image_fulltext']);
                        $item->image_fulltext = new stdClass();

                        foreach ($sizes as $size) {
                            $item->logo->{$size->name} = $image_fulltext['dirname'] . "/" . $image_fulltext['basename'] . "_" . $size->name;
                        }
                    }
                }
            }
        }

        return $items;
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
            ->from($db->quoteName('#__etdorganizations_organizations') . ' AS a')
            ->leftJoin($db->quoteName('#__categories') . ' AS C ON c.id = a.catid')
            ->where('a.catid = ' . $db->quote($this->getState('category.id')));

        // Filter by state
        $published = $this->getState('filter.published');
        if (is_numeric($published)) {
            $query->where('a.published = ' . (int) $published);
        }

        // Define null and now dates
        $nullDate = $db->quote($db->getNullDate());
        $nowDate  = $db->quote(JFactory::getDate()->toSql());

        // Filter by start and end dates.
        $query->where('(a.publish_up = ' . $nullDate . ' OR a.publish_up <= ' . $nowDate . ')')
            ->where('(a.publish_down = ' . $nullDate . ' OR a.publish_down >= ' . $nowDate . ')');

        // Add the list ordering clause.
        $query->order($db->escape($this->getState('list.ordering', 'a.created')) . ' ' . $db->escape($this->getState('list.direction', 'ASC')));

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


        $orderCol = $app->input->get('filter_order', $params->get('list_ordering', 'created'));
        if (!in_array($orderCol, $this->filter_fields)) {
            $orderCol = 'created';
        }

        $listOrder = $app->input->get('filter_order_Dir', $params->get('list_direction', 'ASC'));
        if (!in_array(strtoupper($listOrder), array('ASC', 'DESC', '')))
        {
            $listOrder = 'ASC';
        }
        $this->setState('list.direction', $listOrder);

        $orderby   = $params->get('orderby', 'random');
        $orderDate = $params->get('order_date');
        $orgaOrder = EtdOrganizationsHelperQuery::orderby($orderby, $orderDate) . ', ';

        $order = $orgaOrder . $this->_db->escape($orderCol) . ' ';
        $this->setState('list.ordering', $order);

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