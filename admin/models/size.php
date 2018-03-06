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
 * Item Model for an Size.
 */
class DiModelSize extends JModelAdmin
{
	/**
	 * @var		string	The prefix to use with controller messages.
	 * @since	1.6
	 */
	protected $text_prefix = 'COM_DI';
	
	/**
	 * Method to test whether a record can be deleted.
	 *
	 * @return	boolean	True if allowed to delete the record. Defaults to the permission set in the component.
	 * @since	1.6
	 */
	protected function canDelete( $record )
	{
		$user = JFactory::getUser();
			
		return $user->authorise( 'core.delete', 'com_di' );
	}
	
	/**
	 * Returns a Table object, always creating it.
	 *
	 * @param	type	The table type to instantiate
	 * @param	string	A prefix for the table class name. Optional.
	 * @param	array	Configuration array for model. Optional.
	 *
	 * @return	JTable	A database object
	*/
	public function getTable( $type = 'Size', $prefix = 'DiTable', $config = array() )
	{
		return JTable::getInstance( $type, $prefix, $config );
	}
	
	/**
	 * Auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	protected function populateState()
	{
		$app = JFactory::getApplication( 'administrator' );
		
		// Load the User state.
		$pk = $app->input->getInt( 'id' );
		$this->setState( $this->getName() . '.id', $pk );
		
		// Load the parameters.
		$params = JComponentHelper::getParams( 'com_di' );
		$this->setState( 'params', $params );
	}
	
	/**
	 * Method to get a single record.
	 *
	 * @param	integer	The id of the primary key.
	 *
	 * @return	mixed	Object on success, false on failure.
	 */
	public function getItem( $pk = null )
	{
		if( $result = parent::getItem( $pk ) )
		{
			return $result;
		}
		
		return null;
	}
	
	/**
	 * Method to get the record form.
	 *
	 * @param	array	$data		Data for the form.
	 * @param	boolean	$loadData	True if the form is to load its own data (default case), false if not.
	 *
	 * @return	mixed	A JForm object on success, false on failure
	 * @since	1.6
	 */
	public function getForm( $data = array(), $loadData = true )
	{
		// Get the form.
		$form = $this->loadForm( 'com_di.size', 'size', array( 'control' => 'jform', 'load_data' => $loadData ) );
		
		if( empty( $form ) )
		{
			return false;
		}
		
		return $form;
	}
	
	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return	mixed	The data for the form.
	 * @since	1.6
	 */
	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$app  = JFactory::getApplication();
		$data = $app->getUserState( 'com_di.edit.' . $this->getName() . '.data', array() );
		
		if( empty( $data) )
		{
			$data = $this->getItem();
		}
		
		return $data;
	}
	
	/**
	 * Method to save the form data.
	 *
	 * @param	array	The form data.
	 *
	 * @return	boolean	True on success.
	 * @since	1.6
	 */
	public function save( $data )
	{
		$app 	= JFactory::getApplication();
		$db 	= JFactory::getDBO();
		
		if( empty( $data[ 'indent' ] ) )
		{
			$data[ 'indent' ] = 'new_size';
		}
		
		// Alter the title for save as copy
		if( $app->input->get( 'task' ) == 'save2copy' )
		{
			$query = "SELECT count(id) FROM #__di_images_sizes WHERE indent LIKE '%" . $data[ 'indent' ] . "%'";
			$db->setQuery( $query );
			$count = $db->loadResult();
			
			$data[ 'indent' ] = $data[ 'indent' ] . '_' . ( $count );
		}

		if( parent::save( $data ) )
		{
			return true;
		}
		
		return false;
	}
}
