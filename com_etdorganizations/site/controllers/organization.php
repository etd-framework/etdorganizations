<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_etdorganizations
 *
 * @version     1.2.1
 * @copyright	Copyright (C) 2017 - 2018 ETD Solutions. All rights reserved.
 * @license		GNU General Public License v3
 * @author		ETD Solutions http://www.etd-solutions.com
 */

// Protect from unauthorized access
defined('_JEXEC') or die('Restricted access to ETD Organizations');

class EtdOrganizationsControllerOrganization extends JControllerForm {

    protected $default_view = 'organization';

    public function display($cachable = false, $urlparams = array()) {

        // On détecte si c'est un alias ou une clé primaire.
        $input = JFactory::getApplication('site')->input;
        $id    = $input->get('id', 0, 'uint');
        $model = $this->getModel();

        // C'est une clé primaire.
        if (is_numeric($id)) {

            $input->set('id', (int) $id);

        } elseif (is_string($id)) { // C'est un alias.

            // On tente de déterminer la clé primaire grâce à l'alias.
            $alias = (string)$id;

            // On récupère le table.
            $table = $model->getTable();

            // On tente de charger la ligne.
            if (!$table->load(array('alias' => $alias))) {
                JFactory::getApplication()->redirect(JRoute::_(JUri::base()), JText::_('CTRL_' . strtoupper($this->getName()) . '_DO_NOT_EXISTS'));

                return false;
            }

            // Update the primary key in the input.
            $input->set('id', $table->get('id'));

        } else {

            JFactory::getApplication()->redirect(JRoute::_(''), JText::_('CTRL_' . strtoupper($this->getName()) . '_DO_NOT_EXISTS'));

            return false;
        }


        if ($model) {
            $model->hit($input->get('id'));
        }

        return parent::display($cachable, $urlparams);
    }
}