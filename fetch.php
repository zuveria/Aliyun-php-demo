<?php
// File inside theme folder where you need to display uploaded files to OSS bucket
require_once($_SERVER['DOCUMENT_ROOT'] . '/vendor/aliyuncs/oss-sdk-php/samples/Common.php');
use OSS\OssClient;
use OSS\Core\OssException;

// In functions.php file define constant for OSS Bucket URL
define('OSSBUCKETURL', 'https://#####.oss-cn-hongkong.aliyuncs.com');

    // Fetch all posts created by particular user by using wp_query
    while( $list->have_posts()) ) : $list->the_post();
		//OSS BUCKET FETCH FILE START		
		$prefix = $post_id.'/'; // Folder name where file is saved on OSS bucket it can be post ID
	    $delimiter = '/';
	    $nextMarker = '';
	    $maxkeys = 1000;
	    $options = array(
	        'delimiter' => $delimiter,
	        'prefix' => $prefix,
	        'max-keys' => $maxkeys,
	        'marker' => $nextMarker,
	    );

	    // Fetching the bucket for use
	    try {
	        $listObjectInfo = $ossClient->listObjects($bucket, $options);
	    } catch (OssException $e) {
	        printf(__FUNCTION__ . ": FAILED\n");
	        printf($e->getMessage() . "\n");
	        return;
	    }	    
	    // Check if video data is saved in post meta
	    $session_videos_new = get_post_meta($post_id, '_cf_meta_value_session_videos', true);
	    $original_video_list_size = sizeof($session_videos_new);
	    if(empty($session_videos_new)){
	    	$session_videos_new = array();
	    }
	    // Fetch Object list from Bucket
	    $objectList = $listObjectInfo->getObjectList(); 
	    // Fetching files list from server to compare with OSS bucket files
	    $upload_basedir = WP_CONTENT_DIR . '/video-chat/server/recordings';
		$folder_video_list = glob($upload_basedir."/*");
		$compare_file_name = "filename"; // Put your file name here to compare
		$compare_file_length = strlen($compare_file_name);

		// accessing bucket data
	    if (!empty($objectList)) { 
	        foreach ($objectList as $objectInfo) { 
	        	$oss_video_object = $objectInfo->getKey();
	        	// if new video is found on bucket add in video list to save in post meta
	        	if(!in_array( $oss_video_object, $session_videos_new )){
	        		array_unshift($session_videos_new,$oss_video_object);	
	        	}
	        	$update_video_list_size = sizeof($session_videos_new);
	        	// If video list is updated from original than update the post meta
	        	if( $update_video_list_size >  $original_video_list_size ){
	        		update_post_meta($post_id, '_cf_meta_value_session_videos',$session_videos_new);
	        	}
	        	// Code to display bucket URL for particular post
	        	echo '<div class="class-history">';
					echo '<i class="fa fa-video-camera"></i> <strong>'. __('Video Conference','theme-text-domain') .' : </strong>';
					echo '<a href="'.OSSBUCKETURL.'/'.$oss_video_object.'" target="_blank">'. __('Watch Again','theme-text-domain') .'</a>';
					echo "<br/>";
				echo '</div>';
				// Check if file exists on OSS Bucket then remove it from server.
				foreach( $folder_video_list as $saved_list ){
						$list_array = explode('/',$saved_list);
						$filename = end($list_array);
						$oss_name = explode('/',$oss_video_object);
						$oss_filename = end($oss_name);
						// Comparing file name from server and OSS bucket to delete it.
						if(substr($filename, 0, $compare_file_length) === $compare_file_name && $oss_filename==$filename){
								$path = $upload_basedir.'/'.$filename;
								unlink($path);
						}
				}
	        }
	    }

endwhile; wp_reset_postdata();