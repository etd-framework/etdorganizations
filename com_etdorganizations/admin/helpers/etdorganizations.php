<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_etdorganizations
 *
 * @version     1.0.3
 * @copyright	Copyright (C) 2017 - 2018 ETD Solutions. All rights reserved.
 * @license		GNU General Public License v3
 * @author		ETD Solutions http://www.etd-solutions.com
 */

// Protect from unauthorized access
defined('_JEXEC') or die('Restricted access to ETD Organizations');

class EtdOrganizationsHelper extends JHelperContent {

	/**
	 * Configure the Linkbar.
	 *
	 * @param   string  $vName  The name of the active view.
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	public static function addSubmenu($vName) {

		JHtmlSidebar::addEntry(
			JText::_('COM_ETDORGANIZATIONS_SUBMENU_ORGANIZATIONS'),
			'index.php?option=com_etdorganizations&view=organizations',
			$vName == 'organizations'
		);

		JHtmlSidebar::addEntry(
			JText::_('COM_ETDORGANIZATIONS_SUBMENU_CATEGORIES'),
			'index.php?option=com_categories&extension=com_etdorganizations',
			$vName == 'categories'
		);
	}

    /**
     * Adds Count Items for Category Manager.
     *
     * @param   stdClass[]  &$items  The banner category objects
     *
     * @return  stdClass[]
     *
     * @since   3.5
     */
    public static function countItems(&$items) {

        $db = JFactory::getDbo();

        foreach ($items as $item) {

            $item->count_trashed = 0;
            $item->count_archived = 0;
            $item->count_unpublished = 0;
            $item->count_published = 0;

            $query = $db->getQuery(true);
            $query->select('state, count(*) AS count')
                ->from($db->qn('#__etdorganizations'))
                ->where('catid = ' . (int) $item->id)
                ->group('state');
            $db->setQuery($query);
            $images = $db->loadObjectList();

            foreach ($images as $image) {

                if ($image->state == 1) {
                    $item->count_published = $image->count;
                }

                if ($image->state == 0) {
                    $item->count_unpublished = $image->count;
                }

                if ($image->state == 2) {
                    $item->count_archived = $image->count;
                }

                if ($image->state == -2) {
                    $item->count_trashed = $image->count;
                }
            }
        }

        return $items;
    }
}
