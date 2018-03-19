<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_etdorganizations
 *
 * @version     1.2.0
 * @copyright	Copyright (C) 2017 - 2018 ETD Solutions. All rights reserved.
 * @license		GNU General Public License v3
 * @author		ETD Solutions http://www.etd-solutions.com
 **/

// no direct access
defined('_JEXEC') or die('Restricted access to ETD Organizations');

abstract class EtdOrganizationsHelperRoute {

	/**
	 * Get the category route.
	 *
	 * @param   integer  $catid     The category ID.
	 * @param   integer  $language  The language code.
	 *
	 * @return  string  The category route.
	 *
	 * @since   1.5
	 */
	public static function getCategoryRoute($catid, $language = 0) {

		if ($catid instanceof JCategoryNode) {
			$id = $catid->id;
		} else {
			$id = (int) $catid;
		}

		if ($id < 1) {
			$link = '';
		} else {
			$link = 'index.php?option=com_etdorganizations&view=category&id=' . $id;

			if ($language && $language !== '*' && JLanguageMultilang::isEnabled()) {
				$link .= '&lang=' . $language;
			}
		}

		return $link;
	}
}
