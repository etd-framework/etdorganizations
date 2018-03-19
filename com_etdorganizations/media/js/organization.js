/*!
 * @package     Joomla.Administrator
 * @subpackage  com_etdorganizations
 *
 * @version     1.2.0
 * @copyright	Copyright (C) 2017 - 2018 ETD Solutions. All rights reserved.
 * @license		GNU General Public License v3
 * @author		ETD Solutions http://www.etd-solutions.com
 */

jQuery(function($) {
    window.editContact = function(id, name, picture) {

        var contact = document.getElementById('contact-' + id);

        // The contact does not exist, we need to create it.
        if (contact === null) {

            var html = '';

            html += '<div class="thumbnail" id="contact-' + id + '">';
            html += '<input type="hidden" name="jform[contacts][]" value="' + id + '">';

            if (picture !== undefined) {
                if (picture.length) {
                    html += '<div class="img" style="background-image:url(' + picture + ');"></div>';
                }
            }

            html += '<div class="caption"><h3>' + name + '</h3>';
            html += '<p><a href="index.php?option=com_etdorganizations&view=contact&tmpl=component&layout=edit&id=' + id + '" class="modal btn" title="Modifier" rel="{handler: \'iframe\', size: {x: 800, y: 500}}"><span class="icon-apply"></span>Modifier</a>';
            html += '&nbsp;<a href="#" class="btn btn-remove" role="button" data-id="' + id + '"><span class="icon-cancel"></span>Effacer</a>';
            html += '&nbsp;<a href="#" class="btn btn-delete" role="button" data-id="' + id + '"><span class="icon-trash"></span>Supprimer</a></p>';
            html += '</div></div>';

            $('#contact-thumbnails').append(html);
            SqueezeBox.assign($('#contact-' + id + ' a.modal').get(), {
                parse: 'rel'
            });
        }
        // The contact does already exit, this is just an update.
        else {

            contact.find('.caption h3').text(name);

            // If there is a picture for the contact.
            if (picture !== undefined) {
                if (picture.length) {

                    // If the contact had a picture before.
                    if (contact.find('.img').length > 0) {
                        contact.find('.img').css('background-image', 'url(' + picture + ')');
                    } else {
                        contact.prepend('<div class="img" style="background-image:url(' + picture + ');"></div>');
                    }
                }
            } else {
                contact.find('.img').remove();
            }
        }
    };

    /**
     * Process new/edit modal fields in child.
     *
     * @param   object  element       The modal footer button element.
     * @param   string  fieldPrefix   The fields to be updated prefix.
     * @param   string  action        Modal action (add, edit).
     * @param   string  itemType      The item type (Article, Contact, etc).
     * @param   string  task          Task to be done (apply, save, cancel).
     * @param   string  formId        Id of the form field (defaults to itemtype-form).
     * @param   string  idFieldId     Id of the id field (defaults to jform_id).
     * @param   string  titleFieldId  Id of the title field (defaults to jform_title).
     *
     * @return  boolean
     *
     * @since   3.7.0
     */
    window.processModalEdit = function (element, fieldPrefix, action, itemType, task, formId, idFieldId, titleFieldId)
    {
        formId       = formId || itemType.toLowerCase() + '-form';
        idFieldId    = idFieldId || 'jform_id';
        titleFieldId = titleFieldId || 'jform_title';

        var modalId = element.parentNode.parentNode.id, submittedTask = task;

        // Set frame id.
        jQuery('#' + modalId + ' iframe').get(0).id = 'Frame_' + modalId;

        var iframeDocument = jQuery('#Frame_' + modalId).contents().get(0);

        // If Close (cancel task), close the modal.
        if (task === 'cancel')
        {
            // Submit button on child iframe so we can check out.
            document.getElementById('Frame_' + modalId).contentWindow.Joomla.submitbutton(itemType.toLowerCase() + '.' + task);

            jQuery('#' + modalId).modal('hide');
        }
        // For Save (apply task) and Save & Close (save task).
        else
        {
            // Attach onload event to the iframe.
            jQuery('#Frame_' + modalId).on('load', function()
            {
                // Reload iframe document var value.
                iframeDocument = jQuery(this).contents().get(0);

                // Validate the child form and update parent form.
                if (iframeDocument.getElementById(idFieldId) && iframeDocument.getElementById(idFieldId).value != '0')
                {
                    //window.processModalParent(fieldPrefix, iframeDocument.getElementById(idFieldId).value, iframeDocument.getElementById(titleFieldId).value);
                    window.editContact(iframeDocument.getElementById(idFieldId).value, iframeDocument.getElementById(titleFieldId).value);

                    // If Save & Close (save task), submit the edit close action (so we don't have checked out items).
                    if (task === 'save')
                    {
                        window.processModalEdit(element, fieldPrefix, 'edit', itemType, 'cancel', formId, idFieldId, titleFieldId);
                    }
                }

                // Show the iframe again for future modals or in case of error.
                jQuery('#' + modalId + ' iframe').removeClass('hidden');
            });

            // Submit button on child iframe.
            if (iframeDocument.formvalidator.isValid(iframeDocument.getElementById(formId)))
            {
                // For Save & Close (save task) when creating we need to replace the task as apply because of redirects after submit and hide the iframe.
                if (task === 'save')
                {
                    submittedTask = 'apply';
                }

                document.getElementById('Frame_' + modalId).contentWindow.Joomla.submitbutton(itemType.toLowerCase() + '.' + submittedTask);
            }
        }

        return false;
    };

    /**
     * Process select modal fields in child.
     *
     * @param   string  itemType     The item type (Article, Contact, etc).
     * @param   string  fieldPrefix  The fields to be updated prefix.
     * @param   string  id           The new id for the item.
     * @param   string  title        The new title for the item.
     * @param   string  catid        Future usage.
     * @param   object  object       Future usage.
     * @param   string  url          Future usage.
     * @param   string  language     Future usage.
     *
     * @return  boolean
     *
     * @since   3.7.0
     */
    window.processModalSelect = function(itemType, fieldPrefix, id, title, catid, object, url, language) {

        window.editContact(id, title);

        jQuery('#ModalSelect' + itemType + '_' + fieldPrefix).modal('hide').modal('clear');

        return false;
    };

    Joomla.submitbutton = function(task) {
        if (task === 'organization.cancel' || document.formvalidator.isValid(document.getElementById('organization-form'))) {
            Joomla.submitform(task, document.getElementById('organization-form'));
        }
    };

    $(document).ready(function() {

        $(document).on('click', '.btn-remove', function() {
            var id = $(this).data('id');
            $('#contact-' + id).remove();
        });

        $(document).on('click', '.btn-delete', function() {
            var id = $(this).data('id');

            $.ajax({
                url: 'index.php?option=com_etdorganizations&task=contact.ajaxDelete&id=' + id,
                type: 'POST'
            }).done(function (data) {
                $('#contact-' + id).remove();
            }).fail(function (data) {
                alert("La fiche contact n'a pas pu être supprimée.");
            });
        });
    });
});