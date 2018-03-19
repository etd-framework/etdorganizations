<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_etdorganizations_category
 *
 * @version     1.2.1
 * @copyright	Copyright (C) 2017 - 2018 ETD Solutions. All rights reserved.
 * @license		GNU General Public License v3
 * @author		ETD Solutions http://www.etd-solutions.com
 **/

// no direct access
defined('_JEXEC') or die('Restricted access to ETD Organizations');

JModelLegacy::addIncludePath(JPATH_SITE . '/components/com_etdorganizations/models', 'EtdOrganizationsModel');

abstract class ModEtdOrganizationsCategoryHelper {

    /**
     * Retrieve a list of article
     *
     * @param   \Joomla\Registry\Registry &$params module parameters
     *
     * @return  mixed
     */
    public static function getList(&$params) {

        // Get an instance of the generic articles model
        $model = JModelLegacy::getInstance('Category', 'EtdOrganizationsModel', array('ignore_request' => true));

        // Set application parameters in model
        $app       = JFactory::getApplication();
        $appParams = $app->getParams();
        $model->setState('params', $appParams);

        // Set the filters based on the module params
        $model->setState('list.start', 0);
        $model->setState('list.limit', (int) $params->get('count', 10));
        $model->setState('filter.state', 1);

        // Filter by category
        $catid = $params->get('id', 0, 'uint');

        if ($catid > 0) {
            $model->setState('category.id', $catid);
        }

        $items = $model->getItems();

        return $items;
    }
}