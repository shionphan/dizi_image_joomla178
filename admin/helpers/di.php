<?php
/**
 * @module		com_di
 * @script		di.php
 * @author-name Tomas Kartasovas
 * @copyright	Copyright (C) 2013 dizi.lt
 */

// No direct access to this file
defined( '_JEXEC' ) or die();
 
/**
 * Di component helper.
 */
abstract class DiHelper
{
	/**
	 * Configure the Linkbar.
	 */
	public static function addSubmenu( $submenu ) 
	{
		JSubMenuHelper::addEntry( JText::_( 'COM_DI_SUBMENU_SIZES' ), 'index.php?option=com_di', $submenu == 'sizes' );
		
		// set some global property
		$document = JFactory::getDocument();
		$document->addStyleDeclaration( '.icon-48-di {background-image: url(../media/com_di/sizes/di-48x48.png);}' );
	}
	
	/**
	 * Get the actions
	 */
	public static function getActions( $messageId = 0 )
	{
		$user	= JFactory::getUser();
		$result	= new JObject;
 
		if( empty( $messageId ) )
		{
			$assetName = 'com_di';
		}
		else
		{
			$assetName = 'com_di.sizes.' . (int) $messageId;
		}
 
		$actions = array(
			'core.admin',
			'core.manage',
			'core.create',
			'core.edit',
			'core.delete'
		);
 
		foreach( $actions as $action )
		{
			$result->set( $action, $user->authorise( $action, $assetName ) );
		}
		
		return $result;
	}
}
