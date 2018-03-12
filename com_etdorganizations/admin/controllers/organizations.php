<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_etdorganizations
 *
 * @version     1.0.2
 * @copyright	Copyright (C) 2017 - 2018 ETD Solutions. All rights reserved.
 * @license		GNU General Public License v3
 * @author		ETD Solutions http://www.etd-solutions.com
 */

// Protect from unauthorized access
defined('_JEXEC') or die('Restricted access to ETD Organizations');

class EtdOrganizationsControllerOrganizations extends JControllerAdmin {

    // The prefix to use with controller messages.
    protected $text_prefix = 'COM_ETDORGANIZATIONS_ORGANIZATIONS';

    /**
     * Proxy for getModel.
     *
     * @param   string $name   The model name. Optional.
     * @param   string $prefix The class prefix. Optional.
     * @param   array  $config Configuration array for model. Optional.
     *
     * @return  object  The model.
     *
     * @since   1.6
     */
    public function getModel($name = 'Organization', $prefix = 'EtdOrganizationsModel', $config = array('ignore_request' => true)) {

        return parent::getModel($name, $prefix, $config);
    }
}
