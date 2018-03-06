<?php
/**
 * @component	com_di
 * @script		edit.php
 * @author-name Tomas Kartasovas
 * @copyright	Copyright (C) 2013 dizi.lt
 */

// No direct access to this file
defined( '_JEXEC' ) or die( 'Restricted access' );

// Include the component HTML helpers.
JHtml::addIncludePath( JPATH_COMPONENT . '/helpers/html' );

// Create shortcut to parameters.
$params = $this->state->get( 'params' );
$params = $params->toArray();

$app 	= JFactory::getApplication();
$input = $app->input;

// Load the tooltip behavior.
JHtml::_( 'behavior.tooltip' );
JHtml::_( 'behavior.formvalidation' );
JHtml::_( 'behavior.keepalive' );
JHtml::_( 'formbehavior.chosen', 'select' );

?>

<script type="text/javascript">
	Joomla.submitbutton = function( task ){
		if( task == 'size.cancel' || document.formvalidator.isValid( document.id( 'item-form' ) ) ){
			Joomla.submitform( task, document.getElementById( 'item-form' ) );
		}
		else{
			alert( '<?php echo $this->escape( JText::_( 'JGLOBAL_VALIDATION_FORM_FAILED' ) );?>' );
		}
	}
</script>

<form action="<?php echo JRoute::_( 'index.php?option=com_di&layout=edit&id='.(int) $this->item->id ); ?>" method="post" name="adminForm" id="item-form" class="form-validate">
	<div class="row-fluid">
		<div class="span10 form-horizontal">
			<div class="tab-content">
				
				<div class="tab-pane active" id="general">
					<div class="row-fluid">
						<div class="span6">
							
							<?php if( isset( $this->item->indent ) ) : ?>
								<?php
									$indent = strtoupper( $this->item->indent );
									
									if( !in_array( $indent, array( 'THUMB', 'REGULAR', 'ZOOMED' ) ) )
									{
										$indent = 'OTHER';
									}
								?>
								<p><?php echo JText::_( 'COM_DI_SIZE_DESCRIPTION_' . $indent . '_TITLE' ); ?></p>
							<?php endif; ?>
							
							<div class="control-group">
								<div class="control-label">
									<?php echo $this->form->getLabel( 'id' ); ?>
								</div>
								<div class="controls">
									<?php echo $this->form->getInput( 'id' ); ?>
								</div>
							</div>
							
							<div class="control-group">
								<div class="control-label">
									<?php echo $this->form->getLabel( 'template_id' ); ?>
								</div>
								<div class="controls">
									<?php echo $this->form->getInput( 'template_id' ); ?>
								</div>
							</div>
							
							<div class="control-group">
								<div class="control-label">
									<?php echo $this->form->getLabel( 'indent' ); ?>
								</div>
								<div class="controls">
									<?php echo $this->form->getInput( 'indent' ); ?>
								</div>
							</div>
							
							<div class="control-group">
								<div class="control-label">
									<?php echo $this->form->getLabel( 'height' ); ?>
								</div>
								<div class="controls">
									<?php echo $this->form->getInput( 'height' ); ?>
								</div>
							</div>
							
							<div class="control-group">
								<div class="control-label">
									<?php echo $this->form->getLabel( 'width' ); ?>
								</div>
								<div class="controls">
									<?php echo $this->form->getInput( 'width' ); ?>
								</div>
							</div>
							
							<div class="control-group">
								<div class="control-label">
									<?php echo $this->form->getLabel( 'crop' ); ?>
								</div>
								<div class="controls">
									<?php echo $this->form->getInput( 'crop' ); ?>
								</div>
							</div>
							
							<p><?php echo JText::_( 'COM_DI_SIZE_RECOMMENDATIONS_TITLE' ); ?></p>
						</div>
					</div>
				</div>
			</div>
			
			<input type="hidden" name="task" value="" />
			<input type="hidden" name="return" value="<?php echo $input->getCmd( 'return' );?>" />
			
			<?php echo JHtml::_( 'form.token' ); ?>
		</div>
	</div>
</form>
