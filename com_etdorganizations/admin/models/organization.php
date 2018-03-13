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

use Joomla\Registry\Registry;

class EtdOrganizationsModelOrganization extends JModelAdmin {

    /**
     * @var    string  The prefix to use with controller messages.
     * @since  1.6
     */
    protected $text_prefix = 'COM_ETDORGANIZATIONS_ORGANIZATION';

    /**
     * The type alias for this content type.
     *
     * @var      string
     * @since    3.2
     */
    public $typeAlias = 'com_etdorganizations.organization';

    /**
     * The context used for the associations table
     *
     * @var    string
     * @since  3.4.4
     */
    protected $associationsContext = 'com_etdorganizations.item';

    /**
     * Batch copy/move command. If set to false,
     * the batch copy/move command is not supported
     *
     * @var string
     */
    protected $batch_copymove = false;

    /**
     * Returns a JTable object, always creating it.
     *
     * @param   string $type The table type to instantiate. [optional]
     * @param   string $prefix A prefix for the table class name. [optional]
     * @param   array $config Configuration array for model. [optional]
     *
     * @return  JTable  A database object
     *
     * @since   1.6
     */
    public function getTable($type = 'Organization', $prefix = 'EtdOrganizationsTable', $config = array()) {

        JTable::addIncludePath(JPATH_ADMINISTRATOR . "/components/com_etdorganizations/tables");
        return JTable::getInstance($type, $prefix, $config);
    }

    /**
     * Method to get a single record.
     *
     * @param   integer  $pk  The id of the primary key.
     *
     * @return  mixed  Object on success, false on failure.
     */
    public function getItem($pk = null) {

        if ($item = parent::getItem($pk)) {

            // Convert the images field to an array.
            $registry = new Registry;
            $registry->loadString($item->images);
            $item->images = $registry->toArray();

            $item->organizationtext = trim($item->fulltext) != '' ? $item->introtext . "<hr id=\"system-readmore\" />" . $item->fulltext : $item->introtext;
        }

        return $item;
    }

    /**
     * Method to get the record form.
     *
     * @param   array $data Data for the form. [optional]
     * @param   boolean $loadData True if the form is to load its own data (default case), false if not. [optional]
     *
     * @return  mixed  A JForm object on success, false on failure
     *
     * @since   1.6
     */
    public function getForm($data = array(), $loadData = true) {

        // Get the form.
        $form = $this->loadForm($this->typeAlias, 'organization', array('control' => 'jform', 'load_data' => $loadData));

        if (empty($form)) {
            return false;
        }

        return $form;
    }

    /**
     * Method to get the data that should be injected in the form.
     *
     * @return  mixed  The data for the form.
     *
     * @since   1.6
     */
    protected function loadFormData() {

        // Check the session for previously entered form data.
        $app = JFactory::getApplication();
        $data = $app->getUserState('com_etdorganizations.edit.organization.data', array());

        if (empty($data)) {
            $data = $this->getItem();

            // On récupère les contacts.
            $data->users = $this->getContacts($data->id);
        }

        $this->preprocessData($this->typeAlias, $data);

        return $data;
    }

    /**
     * A protected method to get a set of ordering conditions.
     *
     * @param   JTable $table A record object.
     *
     * @return  array  An array of conditions to add to add to ordering queries.
     *
     * @since   1.6
     */
    protected function getReorderConditions($table) {

        $condition = array();
        $condition[] = 'catid = ' . (int) $table->catid;
        $condition[] = 'published >= 0';

        return $condition;
    }

    /**
     * Method to save the form data.
     *
     * @param   array  $data  The form data.
     *
     * @return  boolean  True on success.
     *
     * @since   1.6
     */
    public function save($data) {

        $app   = JFactory::getApplication();
        $input = $app->input;

        // Alter the title for save as copy
        if ($input->get('task') == 'save2copy') {
            $origTable = clone $this->getTable();
            $origTable->load($input->getInt('id'));

            if ($data['title'] == $origTable->title) {
                list($title, $alias) = $this->generateNewTitle(0, $data['alias'], $data['title']);
                $data['title'] = $title;
                $data['alias'] = $alias;
            } else {
                if ($data['alias'] == $origTable->alias) {
                    $data['alias'] = '';
                }
            }

            $data['published'] = 0;
        }

        // Automatic handling of alias for empty fields
        if (in_array($input->get('task'), array('apply', 'save', 'save2new')) && (!isset($data['id']) || (int) $data['id'] == 0)) {
            if ($data['alias'] == null || $data['alias'] == '') {
                if (JFactory::getConfig()->get('unicodeslugs') == 1) {
                    $data['alias'] = JFilterOutput::stringURLUnicodeSlug($data['title']);
                } else {
                    $data['alias'] = JFilterOutput::stringURLSafe($data['title']);
                }

                $table = $this->getTable();

                if ($table->load(array('alias' => $data['alias']))) {
                    $msg = JText::_('COM_ETDORGANIZATIONS_ORGANIZATION_SAVE_WARNING');
                }

                list($title, $alias) = $this->generateNewTitle(0, $data['alias'], $data['title']);
                $data['alias'] = $alias;

                if (isset($msg)) {
                    $app->enqueueMessage($msg, 'warning');
                }
            }
        }

        if (isset($data['images']) && is_array($data['images'])) {

            // On récupère les dossiers de destination.
            $config    = JComponentHelper::getParams('com_etdorganizations');
            $imagesDir = "images/" . $config->get('images_dir', 'etdorganizations');

            // Si l'image appartient à une catégorie.
            if ($data["catid"] > 0) {

                // On récupère l'alias de la catégorie.
                $category  = $this->getCategory($data["catid"]);
                $cat_alias = $category->alias;

                if ($cat_alias) {

                    $imagesDir .= '/' . $cat_alias;

                    // On crée le dossier de destination.
                    if (!is_dir($imagesDir)) {
                        JFolder::create(JPATH_ROOT . '/' . $imagesDir);
                    }
                }
            }

            $imagesDir .= '/' . $data['alias'];

            // On crée le répertoire correspondant à l'alias de l'organisation, s'il n'existe pas.
            if (!is_dir($imagesDir)) {
                JFolder::create(JPATH_ROOT . '/' . $imagesDir);
            }

            $logo           = pathinfo($data['images']['logo']);
            $image_fulltext = pathinfo($data['images']['image_fulltext']);

            // Si l'image existe déjà et a donc déjà été uploadée.
            // Et si la catégorie a changée.
            // Il faut donc changer les images de dossier.
            if ($data['id']) {

                $item   = $this->getItem($data["id"]);
                $images = json_decode($item->images);

                if ($item->catid != $data['catid']) {

                    if(isset($images['logo'])) {

                        $actual_logo = pathinfo($images['logo']);

                        // On renome le fichier original pour faire apparaitre l'id de l'image.
                        $old_path      = JPATH_ROOT . "/" . $images['logo'];
                        $original_path = JPATH_ROOT . "/" . $imagesDir . "/" . $actual_logo['basename'];

                        JFile::move($old_path, $original_path);
                    }

                    if(isset($images['image_fulltext'])) {

                        $actual_image_fulltext = pathinfo($images['image_fulltext']);

                        // On renome le fichier original pour faire apparaitre l'id de l'image.
                        $old_path      = JPATH_ROOT . "/" . $images['image_fulltext'];
                        $original_path = JPATH_ROOT . "/" . $imagesDir . "/" . $actual_image_fulltext['basename'];

                        JFile::move($old_path, $original_path);
                    }
                }
            }

            // Si le fichier de logo a été enregistré mais qu'il n'est pas dans le bon répertoire.
            if(isset($logo['dirname']) && $logo['dirname'] != $imagesDir) {

                // Chemin absolu vers le répertoire dans lequel le logo devrait être.
                $logo_dirname = JPATH_ROOT . "/" .$imagesDir . "/" . $logo['basename'];

                // Si un fichier avec un nom identique existe déjà.
                if(file_exists($logo_dirname)) {
                    // On met un message pour en informer l'utilisateur.
                    $app->enqueueMessage(JText::sprintf('COM_ETDORGANIZATIONS_ORGANIZATION_SAVE_WARNING_FILENAME_ALREADY_EXISTS', $logo['basename'], $imagesDir), 'warning');
                    $logo_dirname = $imagesDir . "/" . $this->genRandomFilename() . "." . $logo['extension'];
                } else {
                    $logo_dirname = $imagesDir . "/" . $logo['basename'];
                }

                JFile::move(JPATH_ROOT . "/" . $data['images']['logo'], JPATH_ROOT . "/" . $logo_dirname);
                $data['images']['logo'] = $logo_dirname;
            }

            // Si le fichier d'image de fond a été enregistré mais qu'il n'est pas dans le bon répertoire.
            if(isset($image_fulltext['dirname']) && $image_fulltext['dirname'] != $imagesDir) {

                // Absolute path of the directory in which the file is expected to be.
                $image_fulltext_dirname = JPATH_ROOT . "/" . $imagesDir . "/" . $image_fulltext['basename'];

                // Check if a identical name of file already exists.
                if (file_exists($image_fulltext_dirname)) {
                    $app->enqueueMessage(JText::sprintf('COM_ETDORGANIZATIONS_ORGANIZATION_SAVE_WARNING_FILENAME_ALREADY_EXISTS', $image_fulltext['basename'], $imagesDir), 'warning');
                    $image_fulltext_dirname = $imagesDir . "/" . $this->genRandomFilename() . "." . $image_fulltext['extension'];
                } else {
                    $image_fulltext_dirname = $imagesDir . "/" . $image_fulltext['basename'];
                }

                JFile::move(JPATH_ROOT . "/" . $data['images']['image_fulltext'], JPATH_ROOT . "/" . $image_fulltext_dirname);
                $data['images']['image_fulltext'] = $image_fulltext_dirname;
            }

            // Cast all the images paramaters into a string.
            $registry = new Registry;
            $registry->loadArray($data['images']);
            $data['images'] = (string) $registry;
        }

        $contacts = isset($data['contacts']) ? $data['contacts'] : array();

        $res = parent::save($data);

        $id = ($data["id"] == 0) ? $this->getState('organization.id') : $data["id"];

        $res *= $this->saveContacts($id, $contacts);

        return $res;
    }

    /**
     * @param $pks
     * @return mixed
     */
    public function delete(&$pks) {

        $directories = [];

        foreach ($pks as $i => $pk) {

            $item            = $this->getItem($pk);
            $images          = json_decode($item->images);
            $directories[$i] = pathinfo($images['logo'])['dirname'];
        }

        $ret = parent::delete($pks);

        // Si la suppression est OK, on supprime les fichiers aussi.
        if ($ret) {

            jimport('joomla.filesystem.folder');
            jimport('joomla.filesystem.file');

            foreach ($pks as $i => $pk) {

                // On supprime tous les fichiers commençant par l'id.
                $files = JFolder::files(JPATH_ROOT . "/" . $directories[$i], '^' . $pk . '_.', false, true);

                if (!empty($files)) {
                    JFile::delete($files);
                }
            }
        }

        return $ret;
    }

    /**
     * Génère un nom de fichier aléatoire.
     *
     * @param   integer $length La taille du nom de fichier.
     *
     * @return  string  Nom de fichier aléatoire.
     */
    protected function genRandomFilename($length = 8) {
        $salt     = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $base     = strlen($salt);
        $makepass = '';
        $random   = JCrypt::genRandomBytes($length + 1);
        $shift    = ord($random[0]);
        for ($i = 1; $i <= $length; ++$i) {
            $makepass .= $salt[($shift + ord($random[$i])) % $base];
            $shift += ord($random[$i]);
        }
        return $makepass;
    }

    /**
     * Method to save the contacts of a organization.
     * The contact will also be the manager of the organization.
     *
     * @param int   $id
     * @param array $contacts
     *
     * @return bool
     */
    public function saveContacts($id = null, $contacts = array()) {

        $id = (empty($id)) ? $this->getState('organization.id') : $id;

        if($id == 0) {
            JFactory::getApplication()->enqueueMessage(JText::_('COM_ETDORGANIZATIONS_ORGANIZATION_SAVE_WARNING_NO_ORGANIZATION_ID'), 'warning');
            return false;
        }

        // S'il y a des nouveaux contacts.
        if (!empty($contacts)) {

            $query = $this->_db->getQuery(true);

            // On supprime les contacts existants actuellement pour l'entreprise.
            $query->delete($this->_db->quoteName('#__etdorganizations_organization_contacts'))
                  ->where("organization_id = " . (int)$id);

            $this->_db->setQuery($query);

            $this->_db->execute();

            foreach ($contacts as $contact) {

                $query = $this->_db->getQuery(true);
                $query->insert($this->_db->quoteName('#__etdorganizations_organization_contacts'))
                      ->columns(array(
                          'organization_id',
                          'contact_id'
                      ))
                      ->values((int) $id . "," . (int) $contact);

                $this->_db->setQuery($query);

                $this->_db->execute();

            }
        }

        return true;
    }

    /**
     * Method to get the contacts of a organization.
     *
     * @param int   $id     The organization id.
     * @param array $fields Fields to retrieve in the database.
     * @return mixed
     */
    public function getContacts($id = null, $fields = array("a.id", "a.name", "a.image")) {

        $id = (empty($id)) ? $this->getState('organization.id') : $id;

        $query = $this->_db->getQuery(true);

        $query->select(implode(", ", $fields))
            ->from($this->_db->quoteName("#__contact_details") . " AS a")
            ->leftJoin($this->_db->quoteName("#__etdorganizations_organization_contacts") . " AS b ON a.id = b.contact_id")
            ->where("b.organization_id = " . (int) $id);

        $this->_db->setQuery($query);

        return $this->_db->loadObjectList();
    }

    /**
     * Method to change the title & alias.
     *
     * @param   int      $catid        The category id (useless here but still present to be compatible with the parent class).
     * @param   string   $alias        The alias.
     * @param   string   $title        The title.
     *
     * @return	array  Contains the modified title and alias.
     *
     * @since	12.2
     */
    protected function generateNewTitle($catid = 0, $alias, $title) {

        // Alter the title & alias
        $table = $this->getTable();

        while($table->load(array('alias' => $alias))) {

            $title = JString::increment($title);
            $alias = JString::increment($alias, 'dash');
        }

        return array($title, $alias);
    }

    /**
     * Récupère les informations d'une catégorie en fonction de son id.
     *
     * @param null $catid
     * @return bool
     */
    public function getCategory($catid = null) {

        $catid = (empty($catid)) ? $this->getItem()->catid : (int) $catid;

        if ($catid > 0) {
            $query = $this->_db->getQuery(true);

            $query->select('*')
                ->from($this->_db->quoteName('#__categories'))
                ->where('id = ' . (int) $catid);

            $this->_db->setQuery($query)
                ->execute();

            return $this->_db->loadObject();
        }

        return false;
    }
}
