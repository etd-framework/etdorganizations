<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_etdorganizations_category
 *
 * @version     1.2.2
 * @copyright	Copyright (C) 2017 - 2018 ETD Solutions. All rights reserved.
 * @license		GNU General Public License v3
 * @author		ETD Solutions http://www.etd-solutions.com
 **/

// no direct access
defined('_JEXEC') or die('Restricted access to ETD Organizations');

// Include the helper functions only once
JLoader::register('ModEtdOrganizationsCategoryHelper', __DIR__ . '/helper.php');

$app = JFactory::getApplication();

$cacheparams               = new stdClass;
$cacheparams->cachemode    = 'id';
$cacheparams->class        = 'ModEtdOrganizationsCategoryHelper';
$cacheparams->methodparams = $params;

$storeid                 = $module->id.':getList';
$cacheparams->method     = 'getList';
$cacheparams->modeparams = md5($storeid);
$list = JModuleHelper::moduleCache($module, $params, $cacheparams);

$moduleclass_sfx = htmlspecialchars($params->get('moduleclass_sfx'));
require JModuleHelper::getLayoutPath('mod_etdorganizations_category', $params->get('layout', 'default'));
