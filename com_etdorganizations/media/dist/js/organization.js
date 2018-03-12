jQuery(function($) {
    function editContact(id, nom, photo) {

        // Si le contact existe
        if($('#contact-' + id).length == 0) {

            var chaine = '';

            chaine += '<div class="thumbnail" id="contact-' + id + '">';
            chaine += '<input type="hidden" name="jform[contacts][]" value="' + id + '">';

            if(photo != '') {
                chaine += '<div class="img" style="background-image:url(' + photo + ');"></div>';
            }

            chaine += '<div class="caption"><h3>' + nom + '</h3>';
            chaine += '<p><a href="index.php?option=com_etdorganizations&view=contact&tmpl=component&layout=edit&id=' + id + '" class="modal btn btn-primary" title="Modifier" rel="{handler: \'iframe\', size: {x: 800, y: 500}}"><span class="icon-apply"></span>Modifier</a>';
            chaine += '&nbsp;<a href="#" class="btn btn-danger btn-delete" role="button" data-id="' + id + '"><span class="icon-cancel"></span>Supprimer</a></p>';
            chaine += '</div></div>';

            $('#contact-thumbnails').append(chaine);
            SqueezeBox.assign($('#contact-' + id + ' a.modal').get(), {
                parse: 'rel'
            });
        } else {
            var $contact = $('#contact-' + id);

            $contact.find('.caption h3').text(nom);

            if($contact.find('.img').length) {
                if(photo == '') {
                    $contact.find('.img').remove();
                } else {
                    $contact.find('.img').css('background-image', 'url(' + photo + ')');
                }
            } else {
                $contact.prepend('<div class="img" style="background-image:url(' + photo + ');"></div>');
            }
        }
    }

    $(document).ready(function() {

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

        Joomla.submitbutton = function(task) {
            if (task == 'organization.cancel' || document.formvalidator.isValid(document.getElementById('organization-form'))) {
                Joomla.submitform(task, document.getElementById('organization-form'));
            }
        };
    });
});