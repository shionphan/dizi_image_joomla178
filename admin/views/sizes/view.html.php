<?php
/**
 * @module		com_di
 * @script		di.php
 * @author-name Tomas Kartasovas
 * @copyright	Copyright (C) 2013 dizi.lt
 */

// No direct access to this file
defined( '_JEXEC' ) or die();
 
// import Joomla view library
jimport( 'joomla.application.component.view' );
 
/**
 * Di View
 */
class DiViewSizes extends JViewLegacy
{
	/**
	 * Images view display method
	 * @return void
	 */
	function display( $tpl = null ) 
	{
		// Get data from the model
		$items = $this->get( 'Items' );
		$pagination = $this->get( 'Pagination' );
 
		// Check for errors.
		if( count( $errors = $this->get( 'Errors' ) ) ) 
		{
			JError::raiseError( 500, implode( '<br />', $errors ) );
			return false;
		}
		
		// Assign data to the view
		$this->items = $items;
		$this->pagination = $pagination;
 
		// Set the toolbar
		$this->addToolBar();
 
		// Display the template
		parent::display( $tpl );
 
		// Set the document
		$this->setDocument();
	}
	
	/**
	 * Setting the toolbar
	 */
	protected function addToolBar() 
	{
		$canDo = DiHelper::getActions();
		JToolBarHelper::title( JText::_( 'COM_DI_MANAGER_IMAGES_SIZES' ), 'di' );
		
		if( $canDo->get( 'core.create' ) ) 
		{
			JToolBarHelper::addNew( 'size.add', 'JTOOLBAR_NEW' );
		}
		
		if( $canDo->get( 'core.edit' ) ) 
		{
			JToolBarHelper::editList( 'size.edit', 'JTOOLBAR_EDIT' );
		}
		
		if( $canDo->get( 'core.delete' ) ) 
		{
			JToolbarHelper::deleteList( 'DI_ARE_YOU_SURE', 'sizes.delete', 'JTOOLBAR_DELETE');
		}
		
		if( $canDo->get( 'core.admin' ) ) 
		{
			JToolBarHelper::divider();
			JToolBarHelper::preferences( 'com_di' );
		}
	}
	/**
	 * Method to set up the document properties
	 *
	 * @return void
	 */
	protected function setDocument() 
	{
		$document = JFactory::getDocument();
		$document->setTitle( JText::_( 'COM_DI_ADMINISTRATION' ) );
	}
}