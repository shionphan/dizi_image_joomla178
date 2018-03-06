<?php
/**
 * @module		com_di
 * @script		di.php
 * @author-name Tomas Kartasovas
 * @copyright	Copyright (C) 2013 dizi.lt
 */

// No direct access to this file
defined( '_JEXEC' ) or die();
 
// Access check.
if( !JFactory::getUser()->authorise( 'core.manage', 'com_di' ) ) 
	return JError::raiseWarning( 404, JText::_( 'JERROR_ALERTNOAUTHOR' ) );

// require helper file
JLoader::register( 'DiHelper', dirname( __FILE__ ) . '/helpers/di.php' );
 
// import joomla controller library
jimport( 'joomla.application.component.controller' );
 
// Get an instance of the controller prefixed by Di
$controller = JControllerLegacy::getInstance( 'Di' );
 
// Perform the Request task
//$controller->execute( JRequest::getCmd( 'task' ) );
$controller->execute( JFactory::getApplication()->input->get( 'task' ) );
 
// Redirect if set by the controller
$controller->redirect();
