<?php
/**
 * @module		com_di
 * @script		di.php
 * @author-name Tomas Kartasovas
 * @copyright	Copyright (C) 2013 dizi.lt
 */

// No direct access to this file
defined( '_JEXEC' ) or die();
 
// import Joomla controller library
jimport( 'joomla.application.component.controller' );
 
/**
 * General Controller of Di component
 */
class DiController extends JControllerLegacy
{
	/**
	 * display task
	 *
	 * @return void
	 */
	function display( $cachable = false, $urlparams = false )
	{	// set default view if not set
		JRequest::setVar( 'view', JRequest::getCmd( 'view', 'sizes' ) );
 
		// call parent behavior
		parent::display( $cachable, $urlparams );
		
		return $this;
	}
}
