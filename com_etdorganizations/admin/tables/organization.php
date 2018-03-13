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

class EtdOrganizationsTableOrganization extends JTable {

    /**
     * Constructor
     *
     * @param   JDatabaseDriver &$_db Database connector object
     *
     * @since   1.5
     */
    public function __construct(&$_db) {

        parent::__construct('#__etdorganizations_organizations', 'id', $_db);

        JTableObserverTags::createObserver($this, array('typeAlias' => 'com_etdorganizations.organization'));

        $date = JFactory::getDate();
        $this->created = $date->toSql();
        $this->setColumnAlias('published', 'state');
    }

    public function bind($src, $ignore = array()) {

        if(!parent::bind($src, $ignore)) {

            return false;
        }

        // Search for the {readmore} tag and split the text up accordingly.
        if (isset($src['organizationtext'])) {
            $pattern = '#<hr\s+id=("|\')system-readmore("|\')\s*\/*>#i';
            $tagPos  = preg_match($pattern, $src['organizationtext']);

            if ($tagPos == 0) {
                $this->introtext = $src['organizationtext'];
                $this->fulltext  = '';
            } else {
                list ($this->introtext, $this->fulltext) = preg_split($pattern, $src['organizationtext'], 2);
            }
        }

        return true;

    }

}
