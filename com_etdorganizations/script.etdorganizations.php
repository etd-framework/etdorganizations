<?php
/**
 * @package     ETDOrgaanizations
 *
 * @version     1.0.3
 * @copyright   Copyright (C) 2017 - 2018 ETD Solutions. All rights reserved.
 * @license     GNU General Public License v3
 * @author      ETD Solutions http://www.etd-solutions.com
 **/

// Protect from unauthorized access
defined('_JEXEC') or die('Restricted access to ETD Organizations');

class Com_EtdOrganizationsInstallerScript {

    /**
     * The component's name
     *
     * @var   string
     */
    protected $componentName = 'com_etdorganizations';

    /**
     * The title of the component (printed on installation and uninstallation messages)
     *
     * @var string
     */
    protected $componentTitle = 'ETD Organizations';

    /**
     * The minimum PHP version required to install this extension
     *
     * @var   string
     */
    protected $minimumPHPVersion = '5.4.0';

    /**
     * The minimum Joomla! version required to install this extension
     *
     * @var   string
     */
    protected $minimumJoomlaVersion = '3.5.0';

    /**
     * Obsolete files and folders to remove from both paid and free releases. This is used when you refactor code and
     * some files inevitably become obsolete and need to be removed.
     *
     * @var   array
     */
    protected $removeFilesAllVersions = array(
        'files'   => array(
        ),
        'folders' => array(
        )
    );

    /**
     * Runs on installation
     *
     * @param   JInstallerAdapterComponent $parent The parent object
     *
     * @return  void
     */
    public function install($parent) {

    }

    /**
     * Joomla! pre-flight event. This runs before Joomla! installs or updates the component. This is our last chance to
     * tell Joomla! if it should abort the installation.
     *
     * @param   string                     $type   Installation type (install, update, discover_install)
     * @param   JInstallerAdapterComponent $parent Parent object
     *
     * @return  boolean  True to let the installation proceed, false to halt the installation
     */
    public function preflight($type, $parent) {

    }

    /**
     * Runs after install, update or discover_update. In other words, it executes after Joomla! has finished installing
     * or updating your component. This is the last chance you've got to perform any additional installations, clean-up,
     * database updates and similar housekeeping functions.
     *
     * @param   string                     $type   install, update or discover_update
     * @param   JInstallerAdapterComponent $parent Parent object
     */
    function postflight($type, $parent) {

    }
}