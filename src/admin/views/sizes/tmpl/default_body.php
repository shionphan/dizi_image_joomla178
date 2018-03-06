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
<?php if( $this->items ) : ?>
	<tbody>
		<?php foreach( $this->items as $key => $item ) : ?>
			<tr class="row<?php echo $key % 2; ?>">
				<td width="1%" class="center">
					<input type="checkbox" id="cb<?php echo ( $key + 1 ); ?>" name="cid[]" value="<?php echo $item->id; ?>" onclick="Joomla.isChecked(this.checked)" title="Checkbox for row <?php echo ( $key + 1 ); ?>">
				</td>
				<td>
					<a title="<?php echo JText::_( 'JACTION_EDIT' ); ?>" href="<?php echo JRoute::_( 'index.php?option=com_di&task=size.edit&id=' . $item->id ); ?>"><?php echo $item->indent; ?></a>
				</td>
			</tr>
		<?php endforeach; ?>
	</tbody>
<?php endif; ?>