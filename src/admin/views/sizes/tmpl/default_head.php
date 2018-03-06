<?php
/**
 * @module		com_di
 * @script		di.php
 * @author-name Tomas Kartasovas
 * @copyright	Copyright (C) 2013 dizi.lt
 */

// No direct access to this file
defined( '_JEXEC' ) or die();

?>
<thead>
	<tr>
		<th width="1%" class="hidden-phone">
			<input type="checkbox" name="checkall-toggle" value="" title="Check All" onclick="Joomla.checkAll(this)">
		</th>
		<th>
			<?php echo JText::_( 'JGLOBAL_TITLE' ); ?>
		</th>
	</tr>
</thead>