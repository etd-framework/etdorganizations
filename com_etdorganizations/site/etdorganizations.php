<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_etdorganizations
 *
 * @version     1.1.1
 * @copyright	Copyright (C) 2017 - 2018 ETD Solutions. All rights reserved.
 * @license		GNU General Public License v3
 * @author		ETD Solutions http://www.etd-solutions.com
 */

// Protect from unauthorized access
defined('_JEXEC') or die('Restricted access to ETD Organizations');

JLoader::register('EtdOrganizationsHelperRoute', JPATH_SITE . '/components/com_etdorganizations/helpers/route.php');
JLoader::register('EtdOrganizationsHelperQuery', JPATH_SITE . '/components/com_etdorganizations/helpers/query.php');

$controller	= JControllerLegacy::getInstance('EtdOrganizations');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();
