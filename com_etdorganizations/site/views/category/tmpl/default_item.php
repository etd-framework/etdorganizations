<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_etdorganizations
 *
 * @version     1.1.1
 * @copyright	Copyright (C) 2017 - 2018 ETD Solutions. All rights reserved.
 * @license		GNU General Public License v3
 * @author		ETD Solutions http://www.etd-solutions.com
 **/

// no direct access
defined('_JEXEC') or die('Restricted access to ETD Organizations');
?>
<div class="col-sm-6 col-lg-3 col-md-4">
    <img class="img-responsive" src="<?php echo $this->_item->logo->{$this->params->get('thumb_size', 'thumb')}; ?>" alt="<?php echo htmlspecialchars($this->_item->title); ?>">
    <?php echo $this->_item->title; ?>
</div>