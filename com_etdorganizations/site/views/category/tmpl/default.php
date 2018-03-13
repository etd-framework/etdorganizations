<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_etdorganizations
 *
 * @version     1.0.4
 * @copyright	Copyright (C) 2017 - 2018 ETD Solutions. All rights reserved.
 * @license		GNU General Public License v3
 * @author		ETD Solutions http://www.etd-solutions.com
 **/

// no direct access
defined('_JEXEC') or die('Restricted access to ETD Organizations');

$class = "";
$pageclass_sfx = $this->params->get('pageclass_sfx');

if (!empty($pageclass_sfx)) {
	$class .=  " " . $pageclass_sfx;
}

?>
<div class="organizations<?php echo $class; ?>">
	<?php if ($this->params->get('show_page_heading') != 0 ) : ?>
	<h1>
		<?php echo $this->escape($this->params->get('page_heading')); ?>
	</h1>
	<?php endif; ?>
	<div class="row">
		<?php foreach ($this->items as $item) : ?>
		<?php
			$this->_item = &$item;
			echo $this->loadTemplate('item');
		?>
		<?php endforeach; ?>
	</div>
	<?php if ($this->params->def('show_pagination', 2) == 1  || ($this->params->get('show_pagination') == 2 && $this->pagination->pagesTotal > 1)) : ?>
	<div class="pagination-wrapper">
		<div class="row">
			<div class="col-md-9 col-lg-10">
				<?php echo $this->pagination->getPagesLinks(); ?>
			</div>
			<?php if ($this->params->def('show_pagination_results', 1)) : ?>
				<div class="col-md-3 col-lg-2">
					<span class="counter"><?php echo $this->pagination->getPagesCounter(); ?></span>
				</div>
			<?php  endif; ?>
		</div>
	</div>
	<?php endif; ?>
</div>