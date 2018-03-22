<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_etdorganizations
 *
 * @version     1.2.2
 * @copyright	Copyright (C) 2017 - 2018 ETD Solutions. All rights reserved.
 * @license		GNU General Public License v3
 * @author		ETD Solutions http://www.etd-solutions.com
 */

// Protect from unauthorized access
defined('_JEXEC') or die('Restricted access to ETD Organizations');

class EtdOrganizationsRouter extends JComponentRouterBase {

    /**
     * Build the route for the com_etdorganizations component
     *
     * @param   array &$query An array of URL arguments
     *
     * @return  array  The URL arguments to use to assemble the subsequent URL.
     */
    public function build(&$query) {

        $segments = array();

        $menu = JFactory::getApplication()->getMenu();

        // We need a menu item.  Either the one specified in the query, or the current active one if none specified
        if (empty($query['Itemid'])) {
            $menuItem = $menu->getActive();
            $menuItemGiven = false;
        } else {
            $menuItem = $menu->getItem($query['Itemid']);
            $menuItemGiven = true;
        }

        // Check again
        if ($menuItemGiven && isset($menuItem) && $menuItem->component != 'com_etdorganizations') {
            $menuItemGiven = false;
            unset($query['Itemid']);
        }

        if (isset($query['view'])) {

            $view = $query['view'];

            if ($view == 'category') {

                if (!$menuItemGiven) {
                    $segments[] = $view;
                }

                unset($query['view']);

                if (isset($query['id'])) {
                    $catid = $query['id'];
                } else {
                    // We should have id set for this view.  If we don't, it is an error
                    return;
                }

                if ($menuItemGiven && isset($menuItem->query['id'])) {
                    $mCatid = $menuItem->query['id'];
                } else {
                    $mCatid = 0;
                }

                $categories = JCategories::getInstance('EtdOrganizations');
                $category = $categories->get($catid);

                if (!$category) {
                    // We couldn't find the category we were given.  Bail.
                    return;
                }

                $path = array_reverse($category->getPath());

                $array = array();

                foreach ($path as $id) {
                    if ((int) $id == (int) $mCatid) {
                        break;
                    }

                    list($tmp, $id) = explode(':', $id, 2);

                    $array[] = $id;
                }

                $array = array_reverse($array);

                if (count($array))
                {
                    $array[0] = (int) $catid . ':' . $array[0];
                }

                $segments = array_merge($segments, $array);

                unset($query['id'], $query['catid']);
            }

            if (isset($menuItem->query['view']) && $view == $menuItem->query['view']) {
                unset($query['view']);
            }
        }

        if (isset($query['type']) && isset($menuItem->query['type']) && $query['type'] == $menuItem->query['type']) {
            unset($query['type']);
        }

        if (isset($query['task'])) {
            $segments[] = $query['task'];
            unset($query['task']);
        }

        $total = count($segments);

        for ($i = 0; $i < $total; $i++) {
            $segments[$i] = str_replace(':', '-', $segments[$i]);
        }

        return $segments;
    }

    /**
     * Parse the segments of a URL.
     *
     * @param   array &$segments The segments of the URL to parse.
     *
     * @return  array  The URL attributes to be used by the application.
     */
    public function parse(&$segments) {

        $total = count($segments);
        $vars  = array();

        for ($i = 0; $i < $total; $i++) {
            $segments[$i] = preg_replace('/-/', ':', $segments[$i], 1);
        }

        // View is always the first element of the array
        $count = count($segments);

        $menu     = JFactory::getApplication()->getMenu();
        $params   = JComponentHelper::getParams('com_etdorganizations');
        $advanced = $params->get('sef_advanced_link', 0);
        $item     = $menu->getActive();
        $db       = JFactory::getDbo();

        /*
         * Standard routing for organizations.  If we don't pick up an Itemid then we get the view from the segments
         * the first segment is the view and the last segment is the id of the organization or category.
         */
        if (!isset($item)) {
            $vars['view'] = $segments[0];
            $vars['id'] = $segments[$count - 1];

            return;
        }

        /*
         * If there is only one segment, then it points to either an organization or a category.
         * We test it first to see if it is a category.  If the id and alias match a category,
         * then we assume it is a category.  If they don't we assume it is an organization
         */
        if ($count == 1) {

            // We check to see if an alias is given.  If not, we assume it is an organization
            if (strpos($segments[0], ':') === false) {
                $vars['view'] = 'organization';
                $vars['id'] = (int) $segments[0];

                return;
            }

            list($id, $alias) = explode(':', $segments[0], 2);

            // First we check if it is a category
            $category = JCategories::getInstance('EtdOrganizations')->get($id);

            if ($category && $category->alias == $alias) {
                $vars['view'] = 'category';
                $vars['id'] = $id;

                return $vars;
            } else {
                $query = $db->getQuery(true)
                    ->select($db->quoteName(array('alias', 'catid')))
                    ->from($db->quoteName('#__etdorganizations_organizations'))
                    ->where($db->quoteName('id') . ' = ' . (int) $id);
                $db->setQuery($query);
                $organization = $db->loadObject();

                if ($organization) {

                    if ($organization->alias == $alias) {
                        $vars['view'] = 'organization';
                        $vars['catid'] = (int) $organization->catid;
                        $vars['id'] = (int) $id;

                        return $vars;
                    }
                }
            }
        }

        /*
         * If there was more than one segment, then we can determine where the URL points to
         * because the first segment will have the target category id prepended to it.  If the
         * last segment has a number prepended, it is an organization, otherwise, it is a category.
         */
        if (!$advanced) {

            $cat_id = (int) $segments[0];

            $organization_id = (int) $segments[$count - 1];

            if ($organization_id > 0) {
                $vars['view'] = 'organization';
                $vars['catid'] = $cat_id;
                $vars['id'] = $organization_id;
            }
            else
            {
                $vars['view'] = 'category';
                $vars['id'] = $cat_id;
            }

            return $vars;
        }

        // We get the category id from the menu item and search from there
        $id = $item->query['id'];
        $category = JCategories::getInstance('EtdOrganizations')->get($id);

        if (!$category)
        {
            JError::raiseError(404, JText::_('COM_CONTENT_ERROR_PARENT_CATEGORY_NOT_FOUND'));

            return $vars;
        }

        $categories = $category->getChildren();
        $vars['catid'] = $id;
        $vars['id'] = $id;
        $found = 0;

        foreach ($segments as $segment)
        {
            $segment = str_replace(':', '-', $segment);

            foreach ($categories as $category)
            {
                if ($category->alias == $segment)
                {
                    $vars['id'] = $category->id;
                    $vars['catid'] = $category->id;
                    $vars['view'] = 'category';
                    $categories = $category->getChildren();
                    $found = 1;
                    break;
                }
            }

            if ($found == 0)
            {
                if ($advanced)
                {
                    $db = JFactory::getDbo();
                    $query = $db->getQuery(true)
                        ->select($db->quoteName('id'))
                        ->from('#__etdorganizations_organizations')
                        ->where($db->quoteName('catid') . ' = ' . (int) $vars['catid'])
                        ->where($db->quoteName('alias') . ' = ' . $db->quote($segment));
                    $db->setQuery($query);
                    $cid = $db->loadResult();
                }
                else
                {
                    $cid = $segment;
                }

                $vars['id'] = $cid;

                if ($item->query['view'] == 'archive' && $count != 1)
                {
                    $vars['year'] = $count >= 2 ? $segments[$count - 2] : null;
                    $vars['month'] = $segments[$count - 1];
                    $vars['view'] = 'archive';
                }
                else
                {
                    $vars['view'] = 'organization';
                }
            }

            $found = 0;
        }

        return $vars;
    }
}