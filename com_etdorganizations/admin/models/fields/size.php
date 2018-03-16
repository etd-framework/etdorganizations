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

JFormHelper::loadFieldType('List');

class JFormFieldSize extends JFormFieldList {

    /**
     * The form field type.
     *
     * @var        string
     * @since   1.6
     */
    protected $type = 'Size';

    protected function getOptions() {

        $options = array();
        $config  = JComponentHelper::getParams('com_etdorganizations');

        if ($config->exists('sizes')) {

            $sizes = json_decode($config->get('sizes', '[]'));

            foreach ($sizes as $size) {
                $options[] = JHtml::_('select.option', $size->name, $size->name, 'value', 'text');
            }

        }

        return array_merge(parent::getOptions(), $options);
    }

}
