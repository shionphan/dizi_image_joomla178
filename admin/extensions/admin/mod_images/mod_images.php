<?php
/**
 * @module		com_di
 * @script		mod_images.php
 * @author-name Tomas Kartasovas
 * @copyright	Copyright (C) 2013 dizi.lt
 */

// No direct access to this file
defined( '_JEXEC' ) or die();

// Include dependancies.
require_once dirname( __FILE__ ) . '/helper.php';

//  variables
$doc 			= JFactory::getDocument();
$session 		= JFactory::getSession();
$id 			= (int) JRequest::getString( 'id', null );

//  media component parameters
$media_component_params = JComponentHelper::getParams( 'com_media' );
$di_component_params 	= JComponentHelper::getParams( 'com_di' );

//  full object image path
$di_directory 		= 'di';
$media_path 		= JPATH_ROOT . '/' . $media_component_params->get( 'image_path' ) . '/' . $di_directory;  //  without trailing slash
$media_url 			= JUri::root() . $media_component_params->get( 'image_path' ) . '/' . $di_directory;  //  full images url without trailing slash
$di_size 			= $di_component_params->get( 'manager_size', 'regular' );
$di_use_featured 	= (int) $di_component_params->get( 'use_featured', 1 );

if( empty( $id ) )
{
	$id = $session->get( 'DI_OBJECT_ID', 0 );
	
	if( empty( $id ) )
	{
		$id = rand( -1000000, -10000000 );
		
		$session->set( 'DI_OBJECT_ID', $id );
	}
}

$list = modImagesHelper::getList( $id );

require JModuleHelper::getLayoutPath( 'mod_images', $params->get( 'layout', 'default' ) );