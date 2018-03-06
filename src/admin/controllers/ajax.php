<?php
/**
 * @module		com_di
 * @script		ajax.php
 * @author-name Tomas Kartasovas
 * @copyright	Copyright (C) 2013 dizi.lt
 */

// No direct access to this file
defined( '_JEXEC' ) or die();

//  enable error reporting
error_reporting( E_ALL );

//  images plugin
require_once JPATH_ROOT . '/plugins/content/images/images.php';
jimport( 'joomla.filesystem.file' );
jimport( 'joomla.filesystem.folder' );

//  image
require_once JPATH_ROOT . '/libraries/src/Image/Image.php';

/**
 * ajax actions controller class.
 */
class DiControllerAjax extends JControllerAdmin
{
	private $response;
	
	//  construct
	public function __construct()
	{
		$this->response 			= new stdClass();
		$this->response->status 	= -1;
		$this->response->message 	= '';
		$this->response->data 		= '';
		
		//$task = JRequest::getCmd( 'task', null );
		$task = JFactory::getApplication()->input->get( 'task', null );
		if( $task && method_exists( 'DiControllerAjax', $task ) )
		{
			$this->$task();
		}
		
		//  set headers
		$this->setHeaders();
		
		//  output
		$this->output();
	}
	//  /construct
	
	//  upload images
	public function upload()
	{
		$db 		= JFactory::getDBO();
		$object_id 	= JRequest::getInt( 'object_id', 0 );
		
		$user 		= JFactory::getUser();
		$user_id 	= (int) $user->get( 'id' );
		
		if( !$user->authorise( 'core.create', 'com_di' ) )
		{
			return;
		}
		
		if( $user_id )
		{
			//  retrieve image sizes
			$sizes = plgContentImages::getImagesSizes();
			
			//  media component parameters
			$media_component_params = JComponentHelper::getParams( 'com_media' );
			$di_component_params 	= JComponentHelper::getParams( 'com_di' );
			
			$di_directory 		= 'di';
			$media_path 		= JPATH_ROOT . '/' . $media_component_params->get( 'image_path' ) . '/' . $di_directory;
			$media_url 			= JUri::root() . '/' . $media_component_params->get( 'image_path' ) . '/' . $di_directory;
			$quality 			= $di_component_params->get( 'quality', 80 );

			// Create param
			$waterparam			= array();
			$waterparam['watermarkfile'] = $di_component_params->get( 'watermarkfile', '' );
			$waterparam['horiz_position'] = $di_component_params->get( 'horiz_position', 'center' );
			$waterparam['horiz_shift'] = (int) $di_component_params->get( 'horiz_shift', 0 );
			$waterparam['vert_position'] = $di_component_params->get( 'vert_position', 'bottom' );
			$waterparam['vert_shift'] = (int) $di_component_params->get( 'vert_shift', 10 );
			$waterparam['transparency'] = (int) $di_component_params->get( 'transparency', 50 );

			//  get allowed image extensions
			$image_extensions 	= $media_component_params->get( 'image_extensions' );
			if( !empty( $image_extensions ) )
			{
				$image_extensions = explode( ',', $image_extensions );
			}
			
			$source_file = isset( $_FILES[ 'Filedata' ][ 'tmp_name' ] ) ? $_FILES[ 'Filedata' ][ 'tmp_name' ] : null;
			
			$filename 		= $this->slug( htmlspecialchars( $_FILES[ 'Filedata' ][ 'name' ], ENT_COMPAT, 'UTF-8' ) );
			$target_name 	= md5( time() );
			$target_file 	= $media_path . '/' . $target_name;
			$path_info 		= pathinfo( $filename );
			
			if( !empty( $source_file ) && in_array( $path_info[ 'extension' ], $image_extensions ) )
			{
				//  check if directory exists
				if( !file_exists( $media_path ) )
				{
					JFolder::create( $media_path );
				}
				
				//  upload file
				JFile::upload( $source_file, $target_file );
				
				if( JFile::exists( $target_file ) )
				{
					chmod( $target_file, 0755 );
					
					$query = "
						INSERT INTO #__di_images (
							object_id,
							filename
						)
						VALUES (
							'" . $object_id . "',
							'" . $filename . "'
						);
					";
					$db->setQuery( $query );
					$db->query();
					
					$object_image_id = $db->insertid();
					
					if( $object_image_id )
					{
						$new_name = $object_id . '_' . $object_image_id . '_' . $filename;
						
						rename( $target_file, $media_path . '/' . $new_name );
						
						foreach( $sizes as $size ) {
							$this->create_image(
								$media_path . '/' . $new_name,
								$media_path . '/' .  $object_id . '_' . $object_image_id . '_' . $size->indent . '_' . $filename,
								$size->width,
								$size->height,
								$quality,
								null,
								$size->crop,
								$waterparam
							);
						}
						
						$this->setResponse( 'status', 1 );
						$this->setResponse( 'message', 'SUCCESS' );
						$this->setResponse( 'data', $media_url . '/' . $new_name );
					}
				}
			}
		}
	}
	//  /upload images
	
	//  change order
	public function order()
	{
		$nli 		= JRequest::getVar( 'nli', null );
		$object_id 	= JRequest::getInt( 'object_id', null );
		
		$db 		= JFactory::getDbo();
		$user 		= JFactory::getUser();
		$user_id 	= (int) $user->get( 'id' );
		
		if( !$user->authorise( 'core.edit', 'com_di' ) )
		{
			return;
		}
		
		if( isset( $nli ) && is_array( $nli ) && $object_id && $user_id )
		{
			$query = "SELECT object_image_id FROM #__di_images WHERE object_id = '" . $object_id . "'";
			$db->setQuery( $query );
			$images = $db->loadObjectList();
			
			$images_ids = '';
			if( is_array( $images ) )
			{
				foreach( $images as $image )
				{
					if( $images_ids !== '')
					{
						$images_ids .=  ',' . $image->object_image_id;
					}
					else
					{
						$images_ids .=  $image->object_image_id;
					}
				}
			}
			
			$items = '';
			foreach( $nli as $item )
			{
				if( $items !== '' )
				{
					$items .= ',' . (int)$item;
				}
				else
				{
					$items .= (int)$item;
				}
			}
			
			$query = 'UPDATE #__di_images SET ordering = FIND_IN_SET(object_image_id, "' . $items . '") WHERE object_image_id IN (' . $images_ids . ')';
			$db->setQuery( $query );
			$db->query();
			
			$this->setResponse( 'status', $db->getAffectedRows() );
			$this->setResponse( 'message', 'SUCCESS' );
		}
	}
	//  /change order
	
	//  remove image
	public function remove()
	{
		$object_image_id = JRequest::getString( 'object_image_id', '' );
		
		$db 		= JFactory::getDbo();
		$user 		= JFactory::getUser();
		$user_id 	= (int) $user->get( 'id' );
		
		//  media component parameters
		$component_params 	= JComponentHelper::getParams( 'com_media' );
		$di_directory 		= 'di';
		$media_path 		= JPATH_ROOT . '/' . $component_params->get( 'image_path' ) . '/' . $di_directory;
		
		if( !$user->authorise( 'core.delete', 'com_di' ) )
		{
			return;
		}
		
		if( $object_image_id && $user_id )
		{
			$images 	= null;
			$affected 	= 0;
			$data 		= null;
			
			//  retrieve image sizes
			$sizes = plgContentImages::getImagesSizes();
			
			if( strpos( $object_image_id, ',' ) !== FALSE )
			{
				$object_image_id = explode( ',', $object_image_id );
				
				foreach( $object_image_id as $key => $value )
				{
					$object_image_id[ $key ] = (int) $value;
					
					if( empty( $object_image_id[ $key ] ) )
					{
						unset( $object_image_id[ $key ] );
					}
				}
				
				$query = "SELECT object_image_id, object_id, filename FROM #__di_images WHERE object_image_id IN ( '" . implode( "', '", $object_image_id ) . "' )";
				$db->setQuery( $query );
				$images = $db->loadObjectList();
			}
			else
			{
				$query = "SELECT object_image_id, object_id, filename FROM #__di_images WHERE object_image_id = '$object_image_id'";
				$db->setQuery( $query );
				$image = $db->loadObject();
				
				$images[] = $image;
			}
			
			if( $images )
			{
				foreach( $images as $item )
				{
					$target_file = $media_path . '/' . $item->object_id . '_' . $item->object_image_id . '_' . $item->filename;
				
					JFile::delete( $target_file );
					
					if( !JFile::exists( $target_file ) )
					{
						if( is_array( $sizes ) )
						{
							foreach( $sizes as $size )
							{
								JFile::delete( $media_path . '/' .  $item->object_id . '_' . $item->object_image_id . '_' . $size->indent . '_' . $item->filename );
							}
						}
						
						//  delete from database
						$query = "DELETE FROM #__di_images WHERE object_image_id = '" . $item->object_image_id . "'";
						$db->setQuery( $query );
						$db->query();
						
						$affected += $db->getAffectedRows();
						$data[] = $item->object_image_id;
					}
				}
				
				$this->setResponse( 'status', $affected );
				$this->setResponse( 'data', $data );
				$this->setResponse( 'message', 'SUCCESS' );
			}
		}
	}
	//  /remove image
	
	//  resize images
	public function resize()
	{
		$db 		= JFactory::getDBO();
		$object_id 	= JRequest::getInt( 'object_id', 0 );
		
		$user 		= JFactory::getUser();
		$user_id 	= (int) $user->get( 'id' );
		
		if( !$user->authorise( 'core.edit', 'com_di' ) )
		{
			return;
		}
		
		if( $user_id )
		{
			$resized 	= 0;
			$data 		= null;
			$images 	= null;
			
			//  media component parameters
			$media_component_params = JComponentHelper::getParams( 'com_media' );
			$di_component_params 	= JComponentHelper::getParams( 'com_di' );
			
			$di_directory 		= 'di';
			$media_path 		= JPATH_ROOT . '/' . $media_component_params->get( 'image_path' ) . '/' . $di_directory;
			$media_url 			= JUri::root() . '/' . $media_component_params->get( 'image_path' ) . '/' . $di_directory;
			$quality 			= (int) $di_component_params->get( 'quality', 80 );

			// Create param
			$waterparam			= array();
			$waterparam['watermarkfile'] = $di_component_params->get( 'watermarkfile', '' );
			$waterparam['horiz_position'] = $di_component_params->get( 'horiz_position', 'center' );
			$waterparam['horiz_shift'] = (int) $di_component_params->get( 'horiz_shift', 0 );
			$waterparam['vert_position'] = $di_component_params->get( 'vert_position', 'bottom' );
			$waterparam['vert_shift'] = (int) $di_component_params->get( 'vert_shift', 10 );
			$waterparam['transparency'] = (int) $di_component_params->get( 'transparency', 50 );
			
			$object_image_id = JRequest::getVar( 'object_image_id', null );
			
			if( is_array( $object_image_id ) )
			{
				foreach( $object_image_id as $key => $value )
				{
					$object_image_id[ $key ] = (int) $value;
					
					if( empty( $object_image_id[ $key ] ) )
					{
						unset( $object_image_id[ $key ] );
					}
				}
			}
			
			if( is_array( $object_image_id ) )
			{
				$query = "SELECT object_image_id, object_id, filename FROM #__di_images WHERE object_image_id IN ( '" . implode( "', '", $object_image_id ) . "' )";
				$db->setQuery( $query );
				$images = $db->loadObjectList();
			}
			
			if( $images )
			{
				//  retrieve image sizes
				$sizes = plgContentImages::getImagesSizes();
				
				if( isset( $object_id )
					&& is_array( $images )
					&& is_array( $sizes )
				)
				{
					foreach( $images AS $image )
					{
						foreach( $sizes AS $size )
						{
							$filename = $media_path . '/' .  $object_id . '_' . $image->object_image_id . '_' . $size->indent . '_' . $image->filename;
							
							//  delete old
							if( file_exists( $filename ) )
							{
								JFile::delete( $filename );
							}
							
							//  create resized image
							$this->create_image(
								$media_path . '/' . $object_id . '_' . $image->object_image_id . '_' . $image->filename,
								$filename,
								$size->width,
								$size->height,
								$quality,
								null,
								$size->crop,
								$waterparam
							);
							
							if( file_exists( $filename ) )
							{
								
								$resized++;
								$data[] = $media_url . '/' . $object_id . '_' . $image->object_image_id . '_' . $size->indent . '_' . $image->filename;
							}
						}
					}
				}
				
				$this->setResponse( 'status', $resized );
				$this->setResponse( 'data', $data );
				$this->setResponse( 'message', 'SUCCESS' );
			}
		}
	}
	//  /resize images
	
	//  change featured state
	public function featured()
	{
		$db 		= JFactory::getDBO();
		$object_image_id 	= JRequest::getInt( 'object_image_id', 0 );
		$value 		= JRequest::getInt( 'value', 0 );
		
		$user 		= JFactory::getUser();
		$user_id 	= (int) $user->get( 'id' );
		
		if( !$user->authorise( 'core.edit', 'com_di' ) )
		{
			return;
		}
		
		if( $user_id && $object_image_id )
		{
			$query = "
				SELECT
					object_id
				FROM
					#__di_images
				WHERE
					object_image_id = '$object_image_id'
			";
			$db->setQuery( $query );
			$object_id = $db->loadResult();
			
			//  unset featured for same object_id images
			$query = "
				UPDATE
					#__di_images
				SET
					`featured` = '0'
				WHERE
					object_id = '$object_id'
			";
			$db->setQuery( $query );
			$db->query();
			
			//  set featured
			$query = "
				UPDATE
					#__di_images
				SET
					`featured` = '$value'
				WHERE
					object_image_id = '$object_image_id'
			";
			$db->setQuery( $query );
			$db->query();
			
			$this->setResponse( 'status', $db->getAffectedRows() );
			$this->setResponse( 'message', 'SUCCESS' );
		}
	}
	//  /change featured state
	
	//  update image
	public function update()
	{
		$db 				= JFactory::getDBO();
		$object_image_id 	= JRequest::getInt( 'object_image_id', 0 );
		
		$user 		= JFactory::getUser();
		$user_id 	= (int) $user->get( 'id' );
		
		if( !$user->authorise( 'core.edit', 'com_di' ) )
		{
			return;
		}
		
		if( $user_id && $object_image_id )
		{
			$title = JRequest::getString( 'title', '' );
			$description = JRequest::getString( 'description', '' );
			$link = JRequest::getString( 'link', '' );
			$link_target = JRequest::getString( 'link_target', '' );
			
			$title = $db->escape( $title );
			$description = $db->escape( $description );
			$link = $db->escape( $link );
			$link_target = $db->escape( $link_target );
			
			$featured = JRequest::getInt( 'featured', 0 );
			$state = JRequest::getInt( 'state', 0 );
			
			$query = "
				UPDATE
					#__di_images
				SET
					`title` = '$title',
					`description` = '$description',
					`link` = '$link',
					`link_target` = '$link_target',
					`featured` = '$featured',
					`state` = '$state'
				WHERE
					object_image_id = '$object_image_id'
			";
			$db->setQuery( $query );
			$db->query();
			
			$this->setResponse( 'status', $db->getAffectedRows() );
			$this->setResponse( 'message', 'SUCCESS' );
		}
	}
	//  /update image
	
	//  get images
	public function getImages()
	{
		$object_id 	= JRequest::getInt( 'object_id', 0 );
		
		$user 		= JFactory::getUser();
		$user_id 	= (int) $user->get( 'id' );
		
		if( !$user->authorise( 'core.manage', 'com_di' ) )
		{
			return;
		}
		
		if( $user_id && $object_id )
		{
			$list = plgContentImages::getImages( $object_id, true );
			$list_count = count( $list );
			
			if( $list_count )
			{
				$this->setResponse( 'status', $list_count );
				$this->setResponse( 'message', 'SUCCESS' );
				$this->setResponse( 'data', $list );
			}
		}
	}
	//  /get images
	
	//  sets
	//  set http headers
	private function setHeaders()
	{
		header( 'Access-Control-Allow-Origin: *' );
		header( 'Cache-Control: no-cache, must-revalidate' );
		header( 'Content-type: text/html; charset=utf-8' );
		header( 'Content-type: application/json' );
		header( 'Expires: Mon, 26 Jul 1997 05:00:00 GMT' );
	}
	//  /set http headers
	
	//  set status
	private function setResponse( $key, $value )
	{
		$this->response->$key = $value;
	}
	//  /set status
	
	//  gets
	//  print output
	private function output()
	{
		echo json_encode( $this->response );
		exit();
	}
	
	//  make slug
	function slug( $string )
	{
		$string = preg_replace( '/[^\w\.-]/', '-', strtolower( $string ) );
		$string = preg_replace( '/-+/', "-", $string );
		
		return $string;
	}
	//  /make slug
	
	/*
	 *  create resized image
	 *  $source - old image filename
	 *  $target - new image filename
	 *  $width - image width
	 *  $height - image height
	 *  $quality - image quality
	 *  $format - image output format
	 *  $crop - T/F to crop and center image or not
	 */
	function create_image( $source, $target, $width, $height, $quality = '80', $format = null, $crop = 0, $waterparam, $x = null, $y = null )
	{
		$parts = explode( '.', $source );
		$parts_count = count( $parts );
		
		$format = '';
		$extension = !empty( $parts[ $parts_count - 1 ] ) ? strtolower( $parts[ $parts_count - 1 ] ) : '';
		$size = getimagesize( $source );
		
		if( $width > $height )
		{
			if( $height > $size[ 1 ] )
			{
				$height = $size[ 1 ];
			}
		}
		else
		{
			if( $width > $size[ 0 ] )
			{
				$width = $size[ 0 ];
			}
		}
		
		if( !empty( $extension ) )
			switch( $extension )
			{
				case 'gif' :
					$format = IMAGETYPE_GIF;
					break;
				case 'png' :
					$format = IMAGETYPE_PNG;
					break;
				case 'jpeg' :
				case 'jpg' :
				default :
					$format = IMAGETYPE_JPEG;
					break;
			}
		
		/**
		 * Add watermark by @https://github.com/shionphan 2018-03-06
		 * @param watermarkfile; Path to watermark file or URL
		 * @param horiz_position; Watermark horizontal position;
		 * @param horiz_shift; Left position of watermark. Use negative for right position. Leave empty for center.
		 * @param vert_position; Watermark Vertical position;
		 * @param vert_shift; Top position of watermark. Use negative for bottom position. Leave empty for center.
		 * @param transparency; Transparency in percent. 0 = watermark invisible, 100 = not transparent (except for the set transparent color!). Ignored for true alpha blending.
		 */
		//Add Begin	
		$watermarkfile = $waterparam['watermarkfile'];
		$horiz_position = $waterparam['horiz_position'];
		$horiz_shift = $waterparam['horiz_shift'];
		$vert_position = $waterparam['vert_position'];
		$vert_shift = $waterparam['vert_shift'];
		$transparency = $waterparam['transparency'];
		$transparency_type = 'alpha';
		$transcolor = false;

		static $disable_wm_ext_warning, $disable_wm_load_warning, $disable_alpha_warning;

		if($transparency_type == 'alpha') {
			$transcolor = FALSE;
		}

		$sourceType = strtolower(substr($source, strlen($source)-3));
		switch($sourceType) {
			case 'png':
				$source_id = @imagecreatefrompng($source);
				break;
			case 'gif':
				$source_id = @imagecreatefromgif($source);
				break;
			case 'jpg':
				$source_id = @imagecreatefromjpeg($source);
				break;
			default:
				$source = basename($source);
				if(!$disable_wm_ext_warning) $this->multithumb_msg .= "You cannot use a .$fileType file ($watermarkfile) as a watermark<br />\\n";
				$disable_wm_ext_warning = true;
				return false;
		}

		//Get the resource ids of the pictures
		$fileType = strtolower(substr($watermarkfile, strlen($watermarkfile)-3));
		switch($fileType) {
			case 'png':
				$watermarkfile_id = @imagecreatefrompng($watermarkfile);
				break;
			case 'gif':
				$watermarkfile_id = @imagecreatefromgif($watermarkfile);
				break;
			case 'jpg':
				$watermarkfile_id = @imagecreatefromjpeg($watermarkfile);
				break;
			default:
				$watermarkfile = basename($watermarkfile);
				if(!$disable_wm_ext_warning) $this->multithumb_msg .= "You cannot use a .$fileType file ($watermarkfile) as a watermark<br />\\n";
				$disable_wm_ext_warning = true;
				return false;
		}
		if(!$watermarkfile_id) {
			if(!$disable_wm_load_warning) $this->multithumb_msg .= "There was a problem loading the watermark $watermarkfile<br />\\n";
			$disable_wm_load_warning = true;
			return false;
		}

		@imageAlphaBlending($watermarkfile_id, false);
		$result = @imageSaveAlpha($watermarkfile_id, true);
		if(!$result) {
			if(!$disable_alpha_warning) $this->multithumb_msg .= "Watermark problem: your server does not support alpha blending (requires GD 2.0.1+)<br />\\n";
			$disable_alpha_warning = true;
			imagedestroy($watermarkfile_id);
			return false;
		}

		//Get the sizes of both pix
		$sourcefile_width=imagesx($source_id);
		$sourcefile_height=imagesy($source_id);
		$watermarkfile_width=imagesx($watermarkfile_id);
		$watermarkfile_height=imagesy($watermarkfile_id);

		switch ($horiz_position) {
			case 'center':
				$dest_x = ( $sourcefile_width / 2 ) - ( $watermarkfile_width / 2 );
				break;
			case 'left':
				$dest_x = $horiz_shift;
				break;
			case 'right':
				$dest_x = $sourcefile_width - $watermarkfile_width + $horiz_shift;
				break;
		}

		switch ($vert_position) {
			case 'middle':
				$dest_y = ( $sourcefile_height / 2 ) - ( $watermarkfile_height / 2 );
				break;
			case 'top':
				$dest_y = $vert_shift;
				break;
			case 'bottom':
				$dest_y = $sourcefile_height - $watermarkfile_height + $vert_shift;
				break;
		}

		// if a gif, we have to upsample it to a truecolor image
		if($fileType == 'gif') {
			// create an empty truecolor container
			$tempimage = imagecreatetruecolor($sourcefile_width, $sourcefile_height);

			// copy the 8-bit gif into the truecolor image
			imagecopy($tempimage, $source, 0, 0, 0, 0, $sourcefile_width, $sourcefile_height);

			// copy the source_id int
			$source_id = $tempimage;
		}

		if($transcolor!==false) {
			imagecolortransparent($watermarkfile_id, $transcolor); // use transparent color
			imagecopymerge($source_id, $watermarkfile_id, $dest_x, $dest_y, 0, 0, $watermarkfile_width, $watermarkfile_height, $transparency);
		} else {
			imagecopy($source_id, $watermarkfile_id, $dest_x, $dest_y, 0, 0, $watermarkfile_width, $watermarkfile_height); // True alphablend
		}

		imagedestroy($watermarkfile_id);
		header("Content-type: image/jpeg");
		imagejpeg($source_id,$target,100);
		imagedestroy($source_id);
		$image = new JImage($target);
		//Add End;

		//$image = new JImage( $source );
		
		if( !empty( $crop ) )
			//$image->crop( $width, $height, $x, $y, false );
			$image->cropResize( $width, $height, $x, $y, false );
		else
			$image->resize( $width, $height, false, JImage::SCALE_INSIDE );
		
		$image->toFile( $target, $format, array( 'options' => array( 'quality' => $quality ) ) );
		
		// chmod
		chmod( $target, 0644 );
	}
	//  /create resized image
}