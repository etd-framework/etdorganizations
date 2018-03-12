<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_etdorganizations
 *
 * @version     1.0.1
 * @copyright	Copyright (C) 2017 - 2018 ETD Solutions. All rights reserved.
 * @license		GNU General Public License v3
 * @author		ETD Solutions http://www.etd-solutions.com
 **/

// Protect from unauthorized access
defined('_JEXEC') or die('Restricted access to ETD Organizations');

$images = json_decode($this->item->images);
?>
<div class="organization">
    <header class="header">
        <div class="bg-img" style="<?php if (!empty($images->image_fulltext)) : ?>background-image:url('<?php echo $images->image_fulltext; ?>');<?php endif ; ?>background-size:<?php echo $images->float_fulltext; ?>"></div>
        <div class="container">
            <div class="organization-info">
                <h1><?php echo $this->escape($this->item->title); ?></h1>
                <?php if(!empty($images->logo)) : ?>
                    <img class="img-responsive" src="<?php echo $images->logo; ?>" alt="Logo <?php echo $this->item->title; ?>"/>
                <?php endif ; ?>
                <div class="description">
                    <?php echo $this->item->fulltext; ?>
                </div>
            </div>
        </div>
    </header>
    <div id="details">
        <div class="container">
            <div class="organization-details">
                <?php if(!empty($this->item->address)) : ?>
                    <div class="address">
                        <div><span class="fa fa-home"></span></div>
                        <div><span class="text">&nbsp;<?php echo $this->item->address; ?></span></div>

                        <?php if(!empty($this->item->suburb)) : ?>
                            <div><span class="text">&nbsp;<?php echo (!empty($this->item->postcode)) ? $this->item->postcode . ' ' : '' ; ?><?php echo $this->item->suburb; ?></span></div>
                        <?php endif ; ?>
                    </div>
                <?php endif ; ?>

                <?php if(!empty($this->item->phone)) : ?>
                    <div class="phone">
                        <div><span class="fa fa-phone"></span></div>
                        <div><span class="text">&nbsp;<?php echo $this->item->phone; ?></span></div>
                    </div>
                <?php endif ; ?>

                <?php if(!empty($this->item->fax)) : ?>
                    <div class="fax">
                        <div><span class="fa fa-fax"></span></div>
                        <div><span class="text">&nbsp;<?php echo $this->item->fax; ?></span></div>
                    </div>
                <?php endif ; ?>

                <?php if(!empty($this->item->email_to)) : ?>
                    <div class="email_to">
                        <div><span class="fa fa-at"></span></div>
                        <div><span class="text">&nbsp;<a href="mailto:<?php echo $this->item->email_to; ?>"><?php echo $this->item->email_to; ?></a></span></div>
                    </div>
                <?php endif ; ?>
            </div>

            <?php if(!empty($this->item->website)) : ?>
                <a id="website-link" class="btn btn-primary" href="<?php echo $this->item->website; ?>">Visiter le site web</a>
            <?php endif ; ?>

            <div class="contacts-info">
                <?php foreach($this->item->contacts as $key => $contact) : ?>
                    <div class="contact">
                        <?php if(!empty($contact->image)) : ?>
                            <div class="picture">
                                <img class="img-responsive img-circle" src="<?php echo $contact->image; ?>" alt="<?php echo $contact->name; ?>"/>
                            </div>
                        <?php endif ; ?>

                        <div class="details">
                            <h2><?php echo $contact->name; ?></h2>

                            <?php if(!empty($contact->telephone)) : ?>
                                <p><?php echo $contact->telephone; ?></p>
                            <?php endif ; ?>

                            <?php if(!empty($contact->mobile)) : ?>
                                <p><?php echo $contact->mobile; ?></p>
                            <?php endif ; ?>

                            <?php if(!empty($contact->email_to)) : ?>
                                <p><a href="mailto:<?php echo $contact->email_to; ?>"><?php echo $contact->email_to; ?></a></p>
                            <?php endif ; ?>
                        </div>
                    </div>
                <?php endforeach ; ?>
            </div>
        </div>
    </div>
</div>