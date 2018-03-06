<?php
/**
 * @module		com_di
 * @script		mod_images.php
 * @author-name Tomas Kartasovas
 * @copyright	Copyright (C) 2013 dizi.lt
 */

// No direct access to this file
defined( '_JEXEC' ) or die();

//  images plugin
jimport( 'content.images' );

/**
 * Helper for mod_images
 */
abstract class modImagesHelper
{
	public static function getList( $id )
	{
		if( !empty( $id ) )
			return plgContentImages::getImages( $id, true );
		
		return null;
	}
}
