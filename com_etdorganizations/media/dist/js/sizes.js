/*
 * serializeForm
 * https://github.com/danheberden/serializeForm
 *
 * Copyright (c) 2012 Dan Heberden
 * Licensed under the MIT, GPL licenses.
 */
(function( $ ){
    $.fn.serializeForm = function() {

        // don't do anything if we didn't get any elements
        if ( this.length < 1) {
            return false;
        }

        var data = {};
        var lookup = data; //current reference of data
        var selector = ':input[type!="checkbox"][type!="radio"], input:checked';
        var parse = function() {

            // Ignore disabled elements
            if (this.disabled) {
                return;
            }

            // data[a][b] becomes [ data, a, b ]
            var named = this.name.replace(/\[([^\]]+)?\]/g, ',$1').split(',');
            var cap = named.length - 1;
            var $el = $( this );

            // Ensure that only elements with valid `name` properties will be serialized
            if ( named[ 0 ] ) {
                for ( var i = 0; i < cap; i++ ) {
                    // move down the tree - create objects or array if necessary
                    lookup = lookup[ named[i] ] = lookup[ named[i] ] ||
                        ( (named[ i + 1 ] === "" || named[ i + 1 ] === '0') ? [] : {} );
                }

                // at the end, push or assign the value
                if ( lookup.length !==  undefined ) {
                    lookup.push( $el.val() );
                }else {
                    lookup[ named[ cap ] ]  = $el.val();
                }

                // assign the reference back to root
                lookup = data;
            }
        };

        // first, check for elements passed into this function
        this.filter( selector ).each( parse );

        // then parse possible child elements
        this.find( selector ).each( parse );

        // return data
        return data;
    };
}( jQuery ));
/*!
 * @package     Joomla.Administrator
 * @subpackage  com_etdorganizations
 *
 * @version     1.2.1
 * @copyright	Copyright (C) 2017 - 2018 ETD Solutions. All rights reserved.
 * @license		GNU General Public License v3
 * @author		ETD Solutions http://www.etd-solutions.com
 */

var index;

function initSizes(id, currentIndex) {

    var $accordion = jQuery('#' + id);
    index = currentIndex;

    jQuery('#' + id +'_add').on('click', function(e) {
        index++;
        var html = '<div class="size" id="' + id +'_' + index + '">';
        html += '<div class="inner">';
        html += '<div class="row-fluid">';
        html += '<input type="text" name="tmp_sizes[' + index + '][name]" class="span3" value="" placeholder="Nom"> ';
        html += '<input type="text" name="tmp_sizes[' + index + '][height]" class="span3" value="" placeholder="Hauteur max"> ';
        html += '<input type="text" name="tmp_sizes[' + index + '][width]" class="span3" value="" placeholder="Largeur max"> ';
        html += '<label class="checkbox span2"><input type="checkbox" value="1" name="tmp_sizes[' + index + '][crop]"> Rogner</label>';
        html += '<label class="span1"><span class="delete"><span class="icon-trash"></span></span></label>';
        html += '</div>';
        html += '</div>';

        $accordion.append(html);

    });

    jQuery(document).on('change', '.sizes input', function() {
        updateValue(id);
    });

    jQuery(document).on('click', '.sizes .delete', function() {
        if (confirm('Êtes-vous sûr ?')) {
            jQuery(this).parents('.size').remove();
            updateValue(id);
        }
    });
}

function updateValue(id) {
    var data = {},
        inputs = jQuery('.sizes > div').serializeForm();
    if (inputs) {
        jQuery.each(inputs.tmp_sizes, function() {
            if (!this.crop) {
                this.crop = '0';
            }
            data[this.name] = this;
        });
    }
    console.log(JSON.stringify(data));
    jQuery('#' + id +'_hidden').val(JSON.stringify(data));
}