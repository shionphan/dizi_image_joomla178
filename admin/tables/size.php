<?php
/**
 * @component	com_di
 * @script		size.php
 * @author-name Tomas Kartasovas
 * @copyright	Copyright (C) 2013 dizi.lt
 */

// No direct access to this file
defined( '_JEXEC' ) or die();

/**
 * Size table
 */
class DiTableSize extends JTable
{
	public function __construct( &$db )
	{
		parent::__construct( '#__di_images_sizes', 'id', $db );
	}
	
	public function delete( $pk = null )
	{
		return parent::delete( $pk );
	}
}
