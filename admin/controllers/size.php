<?php
/**
 * @component	com_di
 * @script		size.php
 * @author-name Tomas Kartasovas
 * @copyright	Copyright (C) 2013 dizi.lt
 */

// No direct access to this file
defined( '_JEXEC' ) or die();

class DiControllerSize extends JControllerForm
{
	/**
	 * Class constructor.
	 *
	 * @param   array  $config  A named array of configuration variables.
	 *
	 * @since	1.6
	 */
	public function __construct( $config = array() )
	{
		parent::__construct( $config );
	}

	/**
	 * Method override to check if you can add a new record.
	 *
	 * @param   array  $data  An array of input data.
	 *
	 * @return  boolean
	 *
	 * @since   1.6
	 */
	protected function allowAdd( $data = array() )
	{
		$user 	= JFactory::getUser();
		$allow 	= null;

		// If the category has been passed in the data or URL check it.
		$allow = $user->authorise( 'core.create', 'com_di' );

		if( $allow === null )
		{
			// In the absense of better information, revert to the component permissions.
			return parent::allowAdd();
		}
		else
		{
			return $allow;
		}
	}

	/**
	 * Method override to check if you can edit an existing record.
	 *
	 * @param   array   $data  An array of input data.
	 * @param   string  $key   The name of the key for the primary key.
	 *
	 * @return  boolean
	 *
	 * @since   1.6
	 */
	protected function allowEdit( $data = array() )
	{
		$user = JFactory::getUser();

		// Check general edit permission first.
		if( $user->authorise( 'core.edit', 'com_di' ) )
		{
			return true;
		}

		// Since there is no asset tracking, revert to the component permissions.
		return parent::allowEdit( $data );
	}
}
