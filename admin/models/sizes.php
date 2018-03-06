<?php
/**
 * @module		com_di
 * @script		sizes.php
 * @author-name Tomas Kartasovas
 * @copyright	Copyright (C) 2013 dizi.lt
 */

// No direct access to this file
defined( '_JEXEC' ) or die();

// import the Joomla modellist library
jimport( 'joomla.application.component.modellist' );
/**
 * DiList Model
 */
class DiModelSizes extends JModelList
{
	/**
	 * Method to build an SQL query to load the list data.
	 *
	 * @return	string	An SQL query
	 */
	protected function getListQuery()
	{
		// Create a new query object.		
		$db = JFactory::getDBO();
		
		$query = $db->getQuery( true );
		// Select some fields
		$query->select( '*' );
		// From the images table
		$query->from( '#__di_images_sizes' );
		
		return $query;
	}
}
