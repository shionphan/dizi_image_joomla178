<?php
/**
 * @component	com_di
 * @script		view.html.php
 * @author-name Tomas Kartasovas
 * @copyright	Copyright (C) 2013 dizi.lt
 */

// No direct access to this file
defined( '_JEXEC' ) or die();

/**
 * View to edit an article.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_content
 * @since       1.6
 */
class DiViewSize extends JViewLegacy
{
	protected $form;

	protected $item;

	protected $state;

	/**
	 * Display the view
	 */
	public function display( $tpl = null )
	{
		$this->form		= $this->get( 'Form' );
		$this->item		= $this->get( 'Item' );
		$this->state	= $this->get( 'State' );

		// Check for errors.
		if( count( $errors = $this->get( 'Errors' ) ) )
		{
			JError::raiseError( 500, implode( "\n", $errors ) );
			return false;
		}
		
		$this->addToolbar();
		parent::display( $tpl );
	}
	
	/**
	 * Add the page title and toolbar.
	 *
	 * @since	1.6
	 */
	protected function addToolbar()
	{
		JFactory::getApplication()->input->set( 'hidemainmenu', true );
		
		$user		= JFactory::getUser();
		$userId		= $user->get( 'id' );
		$isNew		= ( $this->item->id == 0 );
		$canDo		= DiHelper::getActions();
		
		JToolbarHelper::title( JText::_( 'COM_DI_PAGE_'.( $isNew ? 'ADD_SIZE' : 'EDIT_SIZE' ) ), 'size-add.png' );

		// Built the actions for new and existing records.

		// For new records, check the create permission.
		if ( $isNew && ( count( $user->getAuthorisedCategories( 'com_di', 'core.create' ) ) > 0 ) )
		{
			JToolbarHelper::apply( 'size.apply' );
			JToolbarHelper::save( 'size.save' );
			JToolbarHelper::cancel( 'size.cancel' );
		}
		else
		{
			// Since it's an existing record, check the edit permission, or fall back to edit own if the owner.
				if( $canDo->get( 'core.edit' ) || ( $canDo->get( 'core.edit' ) ) )
				{
					JToolbarHelper::apply( 'size.apply' );
					JToolbarHelper::save( 'size.save' );

					// We can save this record, but check the create permission to see if we can return to make a new one.
					if( $canDo->get( 'core.create' ) )
					{
						JToolbarHelper::save2new( 'size.save2new' );
					}
				}

			// If checked out, we can still save
			if( $canDo->get( 'core.create' ) )
			{
				JToolbarHelper::save2copy( 'size.save2copy' );
			}

			JToolbarHelper::cancel( 'size.cancel', 'JTOOLBAR_CLOSE' );
		}

		JToolbarHelper::divider();
	}
}
