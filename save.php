<?php
/**
Pre-Load all necessary library & name space
**/

namespace App\Http\Controllers;

//load required library by use
//load session & other useful library
use Auth;
use Illuminate\Routing\Controller as BaseController;
//define model
use OSS\Core\OssException;
use OSS\Core\OssUtil;
use OSS\OssClient;
use Mail;

require_once($_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php');

$accessKeyId 	 = "yourAccessKeyId"; // Access Key ID from OSS
$accessKeySecret = "yourAccessKeySecret"; // Access key Secret from OSS
$endpoint 		 = "http://oss-cn-hangzhou.aliyuncs.com"; // Domain you select for accessing OSS data center
$bucket          = "yourBucketName"; // Public Bucket Name
    // Checking submitted file to process
    if (isset($_FILES["blob"])) {    
        $fileName = $_POST["filename"];
        $uploadDirectory = 'recordings/'.$fileName; 
        // Moving file to particular folder to use by OSS bucket
        if (!move_uploaded_file($_FILES["blob"]["tmp_name"], $uploadDirectory)) {
            echo(" problem moving uploaded file");
        }
        $folder_name = "Dynamic Post ID"; // Pass dynamic folder name to add particular file in that folder on OSS
		$object = $folder_name.'/'.$fileName;
		$filePath = __DIR__ .'/recordings/'.$fileName; // Local file path
		try {
            $ossClient = new OssClient($accessKeyId, $accessKeySecret, $endpoint);
            $ossClient->uploadFile($bucket, $object, $filePath);
        } catch (OssException $e) {
            printf(__FUNCTION__ . ": FAILED\n");
            printf($e->getMessage() . "\n");
            return;
        }
        print(__FUNCTION__ . ": OK" . "\n");
    }
}