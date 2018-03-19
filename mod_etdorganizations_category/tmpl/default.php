<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_etdorganizations_category
 *
 * @version     1.2.1
 * @copyright	Copyright (C) 2017 - 2018 ETD Solutions. All rights reserved.
 * @license		GNU General Public License v3
 * @author		ETD Solutions http://www.etd-solutions.com
 **/

// no direct access
defined('_JEXEC') or die('Restricted access to ETD Organizations');

$pageclass_sfx = $params->get('pageclass_sfx');
?>
<div class="mod-etdorganizations<?php echo (!empty($pageclass_sfx)) ? $pageclass_sfx : ''; ?>">
    <?php foreach ($list as $item) : ?>
        <?php $images = json_decode($item->images); ?>
        <div>
            <h2><?php echo $item->title; ?></h2>
            <?php echo $item->introtext; ?>


            <?php $info = ($item->address || $item->suburb || $item->state || $item->country || $item->postcode || $item->phone || $item->fax || $item->email_to || $item->website); ?>

            <?php if ($info) : ?>
                <div>
                    <?php if ($item->address && $item->suburb) : ?>
                        <div>
                            <div><span class="fa fa-location-arrow"></span></div>
                            <div>
                                <?php echo $item->address; ?><br/>
                                <?php if ($item->postcode) : ?><?php echo $item->postcode . ' - '; ?><?php endif ; ?><?php echo $item->suburb; ?>
                            </div>
                        </div>
                    <?php endif ; ?>

                    <?php if ($item->state) : ?>
                        <div>
                            <div><span class="fa fa-map-pin"></span></div>
                            <div>
                                <?php echo $item->state; ?>
                            </div>
                        </div>
                    <?php endif ; ?>

                    <?php if ($item->country) : ?>
                        <div>
                            <div><span class="fa fa-map-marker"></span></div>
                            <div>
                                <?php echo $item->country; ?>
                            </div>
                        </div>
                    <?php endif ; ?>

                    <?php if ($item->phone) : ?>
                        <div>
                            <div><span class="fa fa-phone"></span></div>
                            <div>
                                <?php echo $item->phone; ?>
                            </div>
                        </div>
                    <?php endif ; ?>

                    <?php if ($item->fax) : ?>
                        <div>
                            <div><span class="fa fa-fax"></span></div>
                            <div>
                                <?php echo $item->fax; ?>
                            </div>
                        </div>
                    <?php endif ; ?>

                    <?php if ($item->email_to) : ?>
                        <div>
                            <div><span class="fa fa-envelope"></span></div>
                            <div>
                                <a href="mailto:<?php echo $item->email_to; ?>"><?php echo $item->email_to; ?></a>
                            </div>
                        </div>
                    <?php endif ; ?>

                    <?php if ($item->website) : ?>
                        <div>
                            <div><span class="fa fa-globe"></span></div>
                            <div>
                                <?php

                                $website = $item->website;

                                // Check for links
                                if (!preg_match('#http[s]?://\S*#', $item->website)) {
                                    $website = "http://" . $item->website;
                                }

                                ?>
                                <a href="<?php echo $website; ?>"><?php echo $item->website; ?></a>
                            </div>
                        </div>
                    <?php endif ; ?>
                </div>
            <?php endif ; ?>
        </div>
    <?php endforeach; ?>
</div>