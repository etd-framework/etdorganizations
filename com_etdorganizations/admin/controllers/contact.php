<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_etdorganizations
 *
 * @version     1.0.1
 * @copyright	Copyright (C) 2017 - 2018 ETD Solutions. All rights reserved.
 * @license		GNU General Public License v3
 * @author		ETD Solutions http://www.etd-solutions.com
 */

// Protect from unauthorized access
defined('_JEXEC') or die('Restricted access to ETD Organizations');

class EtdOrganizationsControllerContact extends JControllerForm {

    public function postSaveHook(JModelLegacy $model, $validData = array()) {

        $app = JFactory::getApplication();
        $app->setUserState('hide_modal', true);

        $isNew = $model->getState('contact.new') ? '1' : '0';
        $id    = $model->getState('contact.id');

        // Redirect back to the edit screen.
        $this->setRedirect(JRoute::_('index.php?option=com_etdorganizations&view=contact&layout=edit&tmpl=component&isNew=' . $isNew .'&id=' . $id, false));
    }

    /**
     * Supprime un élément en AJAX.
     *
     * @return stdClass
     */
    public function ajaxDelete() {

        // On initialise les variables
        $app    = JFactory::getApplication();
        $input  = $app->input;
        $result = new \stdClass();

        // Bad request par défaut.
        $result->status = 400;
        $result->error  = true;

        // On récupère les données.
        $id = $input->get('id', 0, 'uint');

        // On contrôle que les données sont correctes.
        if (empty($id)) {
            return $result;
        }

        // On met à jour l'état de présence.
        $model = $this->getModel();

        // On contrôle les droits de suppression.
        if (!$model->canDelete()) {
            $result->message = JText::_('COM_ETDORGANIZATIONS_UNAUTHORIZED_ACTION');
            $result->status  = 403;

            return $result;
        }

        $id = array($id);

        if (!$model->delete($id)) {
            $result->status = 500;
            $error          = $model->getError();
            if ($error instanceof \Exception) {
                $error = $error->getMessage();
            }
            $result->message = $error;

            return $result;
        }

        // Si on est ici c'est OK.
        $result->status = 200;
        $result->error  = false;

        return $result;

    }

}