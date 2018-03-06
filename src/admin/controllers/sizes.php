<?php
/**
 * @module		com_di
 * @script		sizes.php
 * @author-name Tomas Kartasovas
 * @copyright	Copyright (C) 2013 dizi.lt
 */

// No direct access to this file
defined( '_JEXEC' ) or die();

/**
 * Banners list controller class.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_banners
 * @since       1.6
 */
class DiControllerSizes extends JControllerAdmin
{
	/**
	 * @var		string	The prefix to use with controller messages.
	 * @since	1.6
	 */
	protected $text_prefix = 'COM_DI_SIZES';

	/**
	 * Constructor.
	 *
	 * @param	array An optional associative array of configuration settings.
	 * @see		JController
	 * @since	1.6
	 */
	public function __construct( $config = array() )
	{
		parent::__construct( $config );
	}

	/**
	 * Proxy for getModel.
	 * @since	1.6
	 */
	public function getModel( $name = 'Size', $prefix = 'DiModel', $config = array( 'ignore_request' => true ) )
	{
		$model = parent::getModel( $name, $prefix, $config );
		return $model;
	}
	
	/*
	 *  delete
	 */
	public function delete()
	{
		$db 					= JFactory::getDBO();
		$cid 					= JRequest::getVar( 'cid', null );
		$sizes 					= null;
		
		$media_component_params = JComponentHelper::getParams( 'com_media' );
		$di_component_params 	= JComponentHelper::getParams( 'com_di' );
		
		$di_directory 			= 'di';
		$media_path 			= JPATH_ROOT . '/' . $media_component_params->get( 'image_path' ) . '/' . $di_directory;
		
		if( $cid )
		{
			$query = "
				SELECT
					indent
				FROM
					#__di_images_sizes
				WHERE
					id IN ( '" . implode( "', '", $cid ) . "' )
			";
			$db->setQuery( $query );
			$sizes = $db->loadObjectList();
		}
		
		if( $di_component_params->get( 'delete_size_images' ) )
		{
			$files = @scandir( $media_path );
			$files_count = count( $files );
			
			if( $files_count )
			{
				foreach( $files as $file )
				{
					foreach( $sizes as $size )
					{
						if( strpos( $file, '_' . $size->indent . '_'  ) !== FALSE )
						{
							unlink( $media_path . '/' . $file );
						}
					}
				}
			}
		}
		
		parent::delete();
	}
}
