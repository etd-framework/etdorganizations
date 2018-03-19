<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_etdorganizations
 *
 * @version     1.2.0
 * @copyright	Copyright (C) 2017 - 2018 ETD Solutions. All rights reserved.
 * @license		GNU General Public License v3
 * @author		ETD Solutions http://www.etd-solutions.com
 */

// Protect from unauthorized access
defined('_JEXEC') or die('Restricted access to ETD Organizations');

class EtdOrganizationsControllerOrganization extends JControllerForm {

    // The prefix to use with controller messages.
    protected $text_prefix = 'COM_ETDORGANIZATIONS_ORGANIZATION';

    protected function allowEdit($data = array(), $key = 'id') {

        $allowed_users = $this->getModel()->getContacts($data[$key], array("a.user_id"));

        return parent::allowEdit($data) || in_array(JFactory::getUser()->id, $allowed_users);
    }

    /**
     * Function that allows child controller access to model data
     * after the data has been saved.
     *
     * @param   \JModelLegacy  $model      The data model object.
     * @param   array          $validData  The validated data.
     *
     * @return  void
     *
     * @since   1.6
     */
    protected function postSaveHook(\JModelLegacy $model, $validData = array()) {

        // Retrieve the organization id.
        $id = $model->getState('organization.id');

        $config    = JComponentHelper::getParams('com_etdorganizations');
        $item      = $model->getItem($id);
        $sizes     = json_decode($config->get('sizes', '[]'));
        $imagesDir = JPATH_ROOT . "/";

        if (isset($item->images['logo']) && $item->images['logo']) {

            $logo = pathinfo($item->images['logo']);

            // Normalize the name of the file.
            $logo_basename = $this->normalizeFileName($logo['basename']);

            // Original path.
            $original_path = JPath::clean($imagesDir . $logo['dirname'] . "/" . $logo_basename);

            // Generate all the image sizes.
            $this->generateImageSizes($original_path, $sizes);
        }

        if (isset($item->images['image_fulltext']) && $item->images['image_fulltext']) {

            $image_fulltext = pathinfo($item->images['image_fulltext']);

            // Normalize the name of the file.
            $image_fulltext_basename = $this->normalizeFileName($image_fulltext['basename']);

            // Original path.
            $original_path = JPath::clean($imagesDir . $image_fulltext['dirname'] . "/" . $image_fulltext_basename);

            // Generate all the image sizes.
            $this->generateImageSizes($original_path, $sizes);
        }
    }

    protected function generateImageSizes($original_path, $sizes, $crop = null) {

        jimport('image.image');

        $config = JComponentHelper::getParams('com_etdorganizations');

        // On instancie le gestionnaire d'image.
        $image = new JImage($original_path);

        // On extrait le nom du fichier sans extension.
        $filename = pathinfo($original_path, PATHINFO_FILENAME);

        // On extrait le dossier.
        $path = pathinfo($original_path, PATHINFO_DIRNAME);

        // On extrait l'extension.
        $ext = strtolower(pathinfo($original_path, PATHINFO_EXTENSION));

        $options = array();

        switch ($ext) {
            case 'gif':
                $type = IMAGETYPE_GIF;
                break;

            case 'png':
                $type = IMAGETYPE_PNG;
                $options['quality'] = $config->get('quality', 80) / 100;
                break;

            case 'jpg':
            case 'jpeg':
            default:
                $type = IMAGETYPE_JPEG;
                $options['quality'] = $config->get('quality', 80);
                break;
        }

        // On change la couleur de fond.
        $image->filter('Backgroundfill', ['color' => '#FFFFFF']);

        // On crée les déclinaisons de taille pour l'image.
        foreach ($sizes as $size_name => $size) {

            // On crée le nouveau nom de fichier.
            $new_name = $filename . "_" . $size_name;

            // On redimensionne l'image si besoin.
            if ($image->getWidth() > $size->width || $image->getHeight() > $size->height) {
                $newImage = $image->resize($size->width, $size->height, true, $size->crop ? JImage::SCALE_OUTSIDE : JImage::SCALE_INSIDE);
            } else {
                $newImage = new JImage($original_path);
            }

            // On rogne l'image.
            if ($size->crop) {

                $left = null;
                $top  = null;

                if (is_object($crop) && property_exists($crop, $size_name)) {
                    $left = $crop->$size_name->x;
                    $top  = $crop->$size_name->y;
                }

                $newImage->crop($size->width, $size->height, $left, $top, false);
            }

            // On sauvegarde l'image.
            if (!$newImage->toFile($path . "/" . $new_name . "." . $ext, $type, $options)) {
                throw new \InvalidArgumentException(JText::_('toFile error'));
            }

            // On libère la mémoire.
            $newImage->destroy();

        }

        // On libère la mémoire.
        $image->destroy();
    }

    protected function normalizeFileName($name) {

        // On extrait le nom du fichier sans extension.
        $filename = strtolower(pathinfo($name, PATHINFO_FILENAME));

        // On effectue une translitération.
        $filename = JApplicationHelper::stringURLSafe($filename);

        // On extrait l'extension.
        $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));

        return $filename . "." . $ext;
    }
}