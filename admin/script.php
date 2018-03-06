<?php
/**
 * @module		com_di
 * @script		di.php
 * @author-name Tomas Kartasovas
 * @copyright	Copyright (C) 2013 dizi.lt
 */

// No direct access to this file
defined( '_JEXEC' ) or die();

jimport( 'joomla.installer.helper' );
jimport( 'joomla.filesystem.folder' );

/**
 * Script file of di component
 */
class com_diInstallerScript
{
	/**
	 * method to install the component
	 *
	 * @return void
	 */
	function install( $parent ) 
	{
		$db 				= JFactory::getDBO();
		$extensions_path 	= JPATH_ADMINISTRATOR . '/components/com_di/extensions';
		
		//  admin extensions
		$extensions_path_admin = $extensions_path . '/admin';
		$extensions_admin = array(
			array(
				'title' => 'Module mod_images',
				'filename' => 'mod_images',
				'element' => 'mod_images',
				'type' => 'module',
				'position' => 'images',
				'params' => ''
			),
			array(
				'title' => 'Plugin content images',
				'filename' => 'plg_content_images',
				'element' => 'images',
				'type' => 'plugin',
				'position' => '',
				'params' => ''
			),
			array(
				'title' => 'Plugin system images',
				'filename' => 'plg_system_images',
				'element' => 'images',
				'type' => 'plugin',
				'position' => '',
				'params' => ''
			)
		);
		
		//  site extensions
		$extensions_path_site = $extensions_path . '/site';
		$extensions_site = array(
			array(
				'title' => 'Module mod_images',
				'filename' => 'mod_images',
				'element' => 'mod_images',
				'type' => 'module',
				'position' => 'images',
				'params' => '{"fluid":"1","load_fancybox":"1","layout":"_:default","moduleclass_sfx":"","cache":"1","cache_time":"900","module_tag":"div","bootstrap_size":"0","header_tag":"h3","header_class":"","style":"0"}'
			)
		);
		
		$installer = new JInstaller();
		
		//  install admin extensions
		foreach( $extensions_admin as $key => $item )
		{
			$filename 	= $extensions_path_admin . '/' . $item[ 'filename' ] . '.zip';
			$package 	= JInstallerHelper::unpack( $filename );
			
			if( $installer->install( $package[ 'dir' ] ) )
			{
				$color = "#4892AB";
				$message  = "Extension: " . $item[ 'filename' ] . " successfully installed.";
				
				$set_published = '';
				if( $item[ 'type' ] == 'module' )
				{
					//  update module
					$query = "
						UPDATE
							#__modules
						SET
							`published` = '1',
							`position` = '" . $item[ 'position' ] . "',
							`access` = '3',
							`params` = '" . $item[ 'params' ] . "'
						WHERE
							module = '" . $item[ 'element' ] . "'
							AND client_id = '1'
					";
					$db->setQuery( $query );
					$db->query();
					
					//  update module menu
					$query = "
						SELECT
							id
						FROM
							#__modules
						WHERE
							module = '" . $item[ 'element' ] . "'
							AND client_id = '1'
					";
					$db->setQuery( $query );
					$modules = $db->loadObjectList();
					
					if( $modules )
					{
						foreach( $modules as $module )
						{
							$query = "
								INSERT INTO
									#__modules_menu
								(
									`moduleid`,
									`menuid`
								)
								VALUES
								(
									'" . $module->id . "',
									'0'
								)
							";
							$db->setQuery( $query );
							$db->query();
						}
					}
					
					$set_published = ", `state` = '1'";
				}
				
				//  active it
				$query = "
					UPDATE
						#__extensions
					SET
						`enabled` = '1'
						$set_published
					WHERE
						type = '" . $item[ 'type' ] . "'
						AND element = '" . $item[ 'element' ] . "'
				";
				$db->setQuery( $query );
				$db->query();
			}
			else
			{
				$color = "#880000";
				$message  = "ERROR: Could not install the " . $item[ 'element' ] . ". Please install manually.";
			}
			
			echo '<div style="color: ' . $color . ';">' . $message . '</div>';
			
			JInstallerHelper::cleanupInstall( $filename, $package[ 'dir' ] );
		}
		
		//  install site extensions
		foreach( $extensions_site as $key => $item )
		{
			$filename 	= $extensions_path_site . '/' . $item[ 'filename' ] . '.zip';
			$package 	= JInstallerHelper::unpack( $filename );
			
			if( $installer->install( $package[ 'dir' ] ) )
			{
				$color = "#4892AB";
				$message  = "Extension: " . $item[ 'filename' ] . " successfully installed.";
				
				$set_published = '';
				if( $item[ 'type' ] == 'module' )
				{
					//  update module
					$query = "
						UPDATE
							#__modules
						SET
							`published` = '1',
							`position` = '" . $item[ 'position' ] . "',
							`params` = '" . $item[ 'params' ] . "'
						WHERE
							module = '" . $item[ 'element' ] . "'
							AND client_id = '0'
					";
					$db->setQuery( $query );
					$db->query();
					
					//  update module menu
					$query = "
						SELECT
							id
						FROM
							#__modules
						WHERE
							module = '" . $item[ 'element' ] . "'
							AND client_id = '0'
					";
					$db->setQuery( $query );
					$modules = $db->loadObjectList();
					
					if( $modules )
					{
						foreach( $modules as $module )
						{
							$query = "
								INSERT INTO
									#__modules_menu
								(
									`moduleid`,
									`menuid`
								)
								VALUES
								(
									'" . $module->id . "',
									'0'
								)
							";
							$db->setQuery( $query );
							$db->query();
						}
					}
					
					$set_published = ", `state` = '1'";
				}
				
				//  active it
				$query = "
					UPDATE
						#__extensions
					SET
						`enabled` = '1'
						$set_published
					WHERE
						type = '" . $item[ 'type' ] . "'
						AND element = '" . $item[ 'element' ] . "'
				";
				$db->setQuery( $query );
				$db->query();
			}
			else
			{
				$color = "#FF0000";
				$message  = "ERROR: Could not install the " . $item[ 'element' ] . ". Please install manually.";
			}
			
			echo '<div style="color: ' . $color . ';">' . $message . '</div>';
			
			JInstallerHelper::cleanupInstall( $filename, $package[ 'dir' ] );
		}
	}
 
	/**
	 * method to uninstall the component
	 *
	 * @return void
	 */
	function uninstall( $parent ) 
	{
		// $parent is the class calling this method
		//echo '<p>' . JText::_( 'COM_DI_UNINSTALL_TEXT' ) . '</p>';
		
		$db = JFactory::getDBO();
		$installer = new JInstaller();
		
		//  component params
		$component_params 	= JComponentHelper::getParams( 'com_media' );
		$di_directory 		= 'di';
		$media_path 		= JPATH_ROOT . '/' . $component_params->get( 'image_path' ) . '/' . $di_directory;
		
		//  delete images and folder
		if( is_dir( $media_path ) )
		{
			JFolder::delete( $media_path );
		}
		
		//  uninstall extensions
		$installed_extensions = array(
			array(
				'title' => 'Module mod_images',
				'filename' => 'mod_images',
				'element' => 'mod_images',
				'type' => 'module'
			),
			array(
				'title' => 'Plugin content images',
				'filename' => 'plg_content_images',
				'element' => 'images',
				'type' => 'plugin',
				'position' => '',
				'params' => ''
			),
			array(
				'title' => 'Plugin system images',
				'filename' => 'plg_system_images',
				'element' => 'images',
				'type' => 'plugin',
				'position' => '',
				'params' => ''
			)
		);
		
		foreach( $installed_extensions as $item )
		{
			$query = "
				SELECT
					extension_id,
					name,
					type
				FROM
					#__extensions
				WHERE
					type = '" . $item[ 'type' ] . "'
					AND element = '" . $item[ 'element' ] . "'
			";
			$db->setQuery( $query );
			$extensions = $db->loadObjectList();
			
			foreach( $extensions as $extension )
			{
				if( $installer->uninstall( $extension->type, $extension->extension_id ) )
				{
					$color = "#4892AB";
					$message  = "Extension: " . $extension->name . " uninstall successful.";
				}
				else
				{
					$color = "#FF0000";
					$message  = "ERROR: Could not install the " . $extension->name . ". Please uninstall manually.";
				}
				
				echo '<div style="color: ' . $color . ';">' . $message . '</div>';
			}
		}
	}
 
	/**
	 * method to update the component
	 *
	 * @return void
	 */
	function update( $parent ) 
	{
		// $parent is the class calling this method
		//echo '<p>' . JText::_( 'COM_DI_UPDATE_TEXT' ) . '</p>';
	}
 
	/**
	 * method to run before an install/update/uninstall method
	 *
	 * @return void
	 */
	function preflight( $type, $parent ) 
	{
		// $parent is the class calling this method
		// $type is the type of change (install, update or discover_install)
		//echo '<p>' . JText::_( 'COM_DI_PREFLIGHT_' . $type . '_TEXT' ) . '</p>';
	}
 
	/**
	 * method to run after an install/update/uninstall method
	 *
	 * @return void
	 */
	function postflight( $type, $parent ) 
	{
		// $parent is the class calling this method
		// $type is the type of change (install, update or discover_install)
		//echo '<p>' . JText::_( 'COM_DI_POSTFLIGHT_' . $type . '_TEXT' ) . '</p>';
	}
}
