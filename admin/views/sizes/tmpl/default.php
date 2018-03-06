<?php
/**
 * @module		com_di
 * @script		di.php
 * @author-name Tomas Kartasovas
 * @copyright	Copyright (C) 2013 dizi.lt
 */

// No direct access to this file
defined( '_JEXEC' ) or die();

// Include the component HTML helpers.
JHtml::addIncludePath( JPATH_COMPONENT . '/helpers/html' );

// Load the tooltip behavior.
JHtml::_( 'behavior.tooltip' );
JHtml::_( 'behavior.formvalidation' );
JHtml::_( 'behavior.keepalive' );
JHtml::_( 'formbehavior.chosen', 'select' );

?>
<form action="<?php echo JRoute::_( 'index.php?option=com_di' ); ?>"method="post" name="adminForm" id="adminForm">
	<?php if( !empty( $this->sidebar ) ): ?>
		<div id="j-sidebar-container" class="span2">
			<?php echo $this->sidebar; ?>
		</div>
		<div id="j-main-container" class="span10">
	<?php else : ?>
		<div id="j-main-container">
	<?php endif;?>
		
		<table class="table table-striped" id="sizeList">
			<thead><?php echo $this->loadTemplate( 'head' );?></thead>
			<tfoot><?php echo $this->loadTemplate( 'foot' );?></tfoot>
			<tbody><?php echo $this->loadTemplate( 'body' );?></tbody>
		</table>
		
		<p><?php echo JText::_( 'COM_DI_SIZES_RECOMMENDATIONS_TITLE' ); ?></p>
		
		<div>
			<input type="hidden" name="task" value="" />
			<input type="hidden" name="boxchecked" value="0" />
			<?php echo JHtml::_( 'form.token' ); ?>
		</div>
	</div>
</form>