# Aliyun-php-demo

# Step 1: 

Install OSS PHP SDK by following given link  https://github.com/aliyun/aliyun-oss-php-sdk#install-oss-php-sdk 
(courtasy by https://github.com/aliyun)

After you have installed OSS PHP SDK we are all good to go.

# Step 2:
In functions.php file define constant for OSS Bucket URL which can be used by our theme.
```
define('OSSBUCKETURL', 'https://#####.oss-cn-hongkong.aliyuncs.com');
```
# Step 3:
Create save.php file. In this file include all necessaory files required to load the dependancy are included. 
Submitted file from form is get here and uploaded to a folder in server and then moved to OSS Bucket. 

# Step 4:
Create fetch.php file. Fetching the bucket for use using below code
```
$listObjectInfo = $ossClient->listObjects($bucket, $options);
````
Fetch Object list from Bucket
```
$objectList = $listObjectInfo->getObjectList(); 
```
Access particular file using $objectInfo
```
foreach ($objectList as $objectInfo) { 
  $oss_video_object = $objectInfo->getKey();
  // Access the object URL using OSSBUCKETURL
  echo '<a href="'.OSSBUCKETURL.'/'.$oss_video_object.'" target="_blank">'. __('Watch Again','theme-text-domain') .'</a>';
}  
```
