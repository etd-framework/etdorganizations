<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_etdorganizations
 *
 * @version     1.0.4
 * @copyright	Copyright (C) 2017 - 2018 ETD Solutions. All rights reserved.
 * @license		GNU General Public License v3
 * @author		ETD Solutions http://www.etd-solutions.com
 */

// Protect from unauthorized access
defined('_JEXEC') or die('Restricted access to ETD Organizations');

JFormHelper::loadFieldClass('list');

class JFormFieldEtdUser extends JFormFieldList {

    // The form field type.
    public $type = 'EtdUser';

    /**
     * Method to get the field options for category
     * Use the extension attribute in a form to specify the.specific extension for
     * which categories should be displayed.
     * Use the show_root attribute to specify whether to show the global category root in the list.
     *
     * @return  array    The field option objects.
     *
     * @since   11.1
     */
    protected function getOptions() {

        $options = array();

        $db = JFactory::getDbo();

        $query = $db->getQuery(true);

        $query->select("a.name as text, a.id as value")
              ->from("#__users AS a");

        $query->leftJoin("#__user_usergroup_map AS b ON a.id = b.user_id");
        $query->leftJoin("#__usergroups AS c ON c.id = b.group_id");

        $query->where("c.title = 'Membres BNI'");

        $db->setQuery($query);

        $options = $db->loadAssocList();

        // Merge any additional options in the XML definition.
        $options = array_merge(parent::getOptions(), $options);

        return $options;
    }
}
