<?php
/**
 * @module		com_di
 * @script		di.php
 * @author-name Tomas Kartasovas
 * @copyright	Copyright (C) 2013 dizi.lt
 */

// No direct access to this file
defined( '_JEXEC' ) or die();
	
?>

<!--  add scritps and stylesheets  -->
<!--  jQuery ui  -->
<link rel="stylesheet" href="<?php echo JUri::root(); ?>media/com_di/js/jquery-ui-1.9.2.custom/css/smoothness/jquery-ui-1.9.2.custom.min.css" type="text/css" />
<script src="<?php echo JUri::root(); ?>media/com_di/js/jquery-ui-1.9.2.custom/js/jquery-ui-1.9.2.custom.min.js" type="text/javascript"></script>

<!--  fancybox  -->
<link rel="stylesheet" href="<?php echo JUri::root(); ?>media/com_di/js/jquery.fancybox-1.3.4/fancybox/jquery.fancybox-1.3.4.css" type="text/css" />
<script src="<?php echo JUri::root(); ?>media/com_di/js/jquery.fancybox-1.3.4/fancybox/jquery.fancybox-1.3.4.pack.js" type="text/javascript"></script>

<!--  images  -->
<style>
	#fancybox-wrap
	{
		position: fixed !important;
		top: 2% !important;
	}
</style>

<?php
	$url_parts = parse_url( $_SERVER[ 'REQUEST_URI' ] );
	parse_str( $url_parts[ 'query' ], $query_parts );
	
	unset( $query_parts[ 'n' ] );
	
	$url_parts[ 'query' ] = http_build_query( $query_parts );
	
	$url = $url_parts[ 'path' ] . '?' . $url_parts[ 'query' ];
?>

<script>
	var di = {};
	
	di.object_id = "<?php echo $id; ?>";
	di.size = '<?php echo $di_size; ?>';
	di.use_featured = '<?php echo $di_use_featured; ?>';
	di.upload_text = '<?php echo JText::_( 'DI_IMAGES_SELECT_FILES_TITLE' ); ?>';
	di.form_data = {
		'session_name': '<?php echo $session->getName(); ?>',
		'session_id' : '<?php echo $session->getId(); ?>'
	};
	di.url = {
		uri: "<?php echo $url; ?>",
		hash: "<?php echo str_replace( ' ', '_', strtolower( $module->title ) ); ?>",
		root: "<?php echo JUri::root(); ?>",
		ajax: "<?php echo JUri::base(); ?>",
		media: "<?php echo $media_url; ?>"
	};
	di.messages = {
		confirm: "<?php echo JText::_( 'DI_IMAGES_MESSAGE_CONFIRM' ); ?>"
	};
	di.labels = {
		select_all: '<?php echo JText::_( 'DI_IMAGES_MANAGE_SELECT_ALL' ); ?>',
		deselect_all: '<?php echo JText::_( 'DI_IMAGES_MANAGE_DESELECT_ALL' ); ?>',
		feature: 'DI_IMAGES_FEATURE_TITLE',
		unfeature: 'DI_IMAGES_UNFEATURE_TITLE',
		
		drag_title: '<?php echo JText::_( 'DI_IMAGES_DRAG_TITLE' ); ?>',
		feature_title: '<?php echo JText::_( 'DI_IMAGES_FEATURE_TITLE' ); ?>',
		edit_title: '<?php echo JText::_( 'DI_IMAGES_EDIT_TITLE' ); ?>',
		remove_title: '<?php echo JText::_( 'DI_IMAGES_REMOVE_TITLE' ); ?>'
	};
</script>
<link rel="stylesheet" href="<?php echo JUri::root() . 'media/com_di/css/images.css'; ?>" type="text/css" />
<script src="<?php echo JUri::root() . 'media/com_di/js/images.js'; ?>" type="text/javascript"></script>

<!--  FileAPI  -->
<style>
	.js-fileapi-wrapper
	{
		background-color: #ddd;
		margin: 0 0 20px;
		padding: 30px 20px;
		position: relative;
	}
		.b-dropzone
		{
			background-color: #fff;
			bottom: 0;
			display: block;
			position: absolute;
			left: 0;
			right: 0;
			text-align: center;
			top: 0;
			outline: 1px solid #ddd;
			z-index: 1;
		}
		.b-dropzone.over
		{
			background-color: #ddd;
		}
			.b-dropzone__txt
			{
				margin: -9px 0 0 -44px;
				position: absolute;
				left: 50%;
				top: 50%;
			}
		#choose
		{
			position: relative;
			z-index: 2;
		}
		.js-fileapi-wrapper .progress
		{
			background-color: #f5f5f5;
			display: block;
			margin: 0;
			position: absolute;
			height: 100%;
			left: 0;
			top: 0;
			width: 100%;
			z-index: 4;
			border-radius: 0;
			box-shadow: inset 0 1px 2px rgba(0,0,0,.1);
		}
			.js-fileapi-wrapper .progress-bar {
				float: left;
				width: 0;
				height: 100%;
				font-size: 12px;
				line-height: 20px;
				color: #fff;
				text-align: center;
				background-color: #337ab7;
				-webkit-box-shadow: inset 0 -1px 0 rgba(0,0,0,.15);
				box-shadow: inset 0 -1px 0 rgba(0,0,0,.15);
				-webkit-transition: width .6s ease;
				-o-transition: width .6s ease;
				transition: width .6s ease;
			}
</style>

<script>
	window.FileAPI = {
		debug: false,
		cors: false,
		media: false,
		staticPath: '<?php echo JUri::root(); ?>media/com_di/js/FileAPI/dist/'
	};
</script>
<script src="<?php echo JUri::root(); ?>media/com_di/js/FileAPI/dist/FileAPI.min.js" type="text/javascript"></script>

<!--  upload button  -->
<fieldset class="adminform">
	<legend><?php echo JText::_( 'DI_IMAGES_UPLOAD_TITLE' ); ?></legend>
	<div class="clearfix">
		<div class="js-fileapi-wrapper upload-btn">
			<div id="drop-zone" class="b-dropzone">
				<div class="b-dropzone__txt"><?php echo JText::_( 'DI_IMAGES_DROP_FILES_HERE' ); ?></div>
			</div>
			
            <input id="choose" name="files" type="file" multiple />
			
			<div class="progress" id="progress" style="display: none;">
				<div class="progress-bar" style="width: 0%;"></div>
			</div>
        </div>
	</div>
</fieldset>
<!--  /upload button  -->

<!--  images list  -->
<fieldset class="adminform">
	<div id="di">
		<legend><?php echo JText::_( 'DI_IMAGES_MANAGE_IMAGES' ); ?></legend>
		
		<div class="di-toolbar <?php echo $list ? 'visible' : ''; ?> clearfix">
			<a class="di-toggle-select btn">
				<label><input type="checkbox" name="" value="" /><span><?php echo JText::_( 'DI_IMAGES_MANAGE_SELECT_ALL' ); ?></span></label>
			</a>
			<a class="delete-selected btn btn-danger"><?php echo JText::_( 'DI_IMAGES_MANAGE_DELETE_SELECTED' ); ?></a>
			
			<a class="resize-images btn btn-info"><?php echo JText::_( 'DI_IMAGES_MANAGE_RESIZE_IMAGES' ); ?></a>
		</div>
		
		<ul id="diImageList" class="list clearfix">
			<?php if( $list ) : ?>
				<?php foreach( $list as $image ) : ?>
					<li id="nli_<?php echo $image->object_image_id; ?>"
						class="item <?php echo (int) $image->featured == 1 ? 'featured' : ''; ?>"
						data-object-image-id="<?php echo $image->object_image_id; ?>"
						data-object-id="<?php echo $image->object_id; ?>"
						data-state="<?php echo $image->state; ?>"
						data-title="<?php echo $image->title; ?>"
						data-description="<?php echo htmlspecialchars( $image->description ); ?>"
						data-featured="<?php echo $image->featured; ?>"
						data-filename="<?php echo htmlspecialchars( $image->filename ); ?>"
						data-link="<?php echo $image->link; ?>"
						data-link-target="<?php echo $image->link_target; ?>"
					>
						<input type="hidden" name="jform[object_image_id][]" value="<?php echo $image->object_image_id; ?>" />
						<table class="preview-container">
							<tr>
								<td>
									<img class="preview" src="<?php echo $media_url . '/' . $image->object_id . '_' . $image->object_image_id . '_' . $di_size . '_' . $image->filename; ?>" alt="<?php echo $image->title; ?>" />
								</td>
							</tr>
						</table>
						<div class="tool drag-selector" title="<?php echo JText::_( 'DI_IMAGES_DRAG_TITLE' ); ?>"><span class="dicon-move"></span></div>
						<div class="tool toggle-featured"  title="<?php echo JText::_( 'DI_IMAGES_FEATURE_TITLE' ); ?>"><span class="dicon-<?php echo (int) $image->featured == 1 ? 'star' : 'star-empty'; ?>"></span></div>
						<div class="tool check"><input type="checkbox" name="di[check][]" value="<?php echo $image->object_image_id; ?>" /></div>
						<a class="tool edit" title="<?php echo JText::_( 'DI_IMAGES_EDIT_TITLE' ); ?>" href="#"><span class="dicon-edit"></span></a>
						<a class="tool remove" title="<?php echo JText::_( 'DI_IMAGES_REMOVE_TITLE' ); ?>" href="#"><span class="dicon-remove"></span></a>
					</li>
				<?php endforeach; ?>
			<?php endif; ?>
		</ul>
	</div>
</fieldset>

<div class="hidden">
	<div class="di-resize-status">
		<div class="big-box">
			<div class="ac">
				<div class="close btn"><?php echo JText::_( 'DI_IMAGES_EDIT_CLOSE_TITLE' ); ?></div>
			</div>
			
			<div class="status-box">
				<div class="progress progress-striped active">
					<div class="bar" style="width: 0%;"></div>
				</div>
			</div>
		</div>
	</div>
	
	<div class="di-edit-box">
		<div class="big-box">
			<div class="ac">
				<div class="title"></div>
				
				<div class="update btn btn-success"><?php echo JText::_( 'DI_IMAGES_EDIT_UPDATE_TITLE' ); ?></div>
				<div class="close btn"><?php echo JText::_( 'DI_IMAGES_EDIT_CLOSE_TITLE' ); ?></div>
			</div>

			<div class="co">
				<table>
					<tbody>
						<tr>
							<td class="first">
								<div class="row clearfix">
									<label><?php echo JText::_( 'DI_IMAGES_EDIT_TITLE_TITLE' ); ?></label>
									<input class="span4" type="text" name="title" value="" />
								</div>
								<div class="row clearfix">
									<label><?php echo JText::_( 'DI_IMAGES_EDIT_DESCRIPTION_TITLE' ); ?></label>
									<textarea class="span4" name="description"></textarea>
								</div>
								<div class="row clearfix">
									<label><?php echo JText::_( 'DI_IMAGES_EDIT_LINK_TITLE' ); ?></label>
									<input class="span4" type="text" name="link" value="" />
								</div>
								<div class="row clearfix">
									<label><?php echo JText::_( 'DI_IMAGES_EDIT_LINK_TARGET_TITLE' ); ?></label>
									<input class="span4" type="text" name="link_target" value="_blank" />
								</div>
								<div class="row for-radio clearfix">
									<label><?php echo JText::_( 'DI_IMAGES_EDIT_FEATURED_TITLE' ); ?></label>
									<span><?php echo JText::_( 'JYES' ); ?></span><input type="radio" name="featured" value="1" />
									<span><?php echo JText::_( 'JNO' ); ?></span><input type="radio" name="featured" value="0" />
								</div>
								<div class="row for-radio clearfix">
									<label><?php echo JText::_( 'DI_IMAGES_EDIT_PUBLISHED_TITLE' ); ?></label>
									<span><?php echo JText::_( 'JYES' ); ?></span><input type="radio" name="state" value="1" />
									<span><?php echo JText::_( 'JNO' ); ?></span><input type="radio" name="state" value="0" />
								</div>
								
								<input type="hidden" name="object_image_id" value="" />
							</td>
							<td class="second">
								<div class="row preview">
									<label><?php echo JText::_( 'DI_IMAGES_EDIT_IMAGE_TITLE' ); ?></label>
									<img src="" alt="" />
								</div>
							</td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>
<!--  /images list  -->