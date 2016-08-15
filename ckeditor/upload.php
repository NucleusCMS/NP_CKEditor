<?php
/*
 * Nucleus: PHP/MySQL Weblog CMS (http://nucleuscms.org/)
 * Copyright (C) 2002-2013 The Nucleus Group
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 * (see nucleus/documentation/index.html#license for more info)
 *
 * File upload for CKEditor plugin
 */

$CONF = array();

// include all classes and config data
$DIR_LIBS = '';
require_once('../../../config.php');
//include($DIR_LIBS . 'MEDIA.php');	// media classes
include_libs('MEDIA.php',false,false);

// user needs to be logged in to use this
if (!$member->isLoggedIn()) {
	upload_doError(_LOGIN_PLEASE);
	exit;
}

// check if member is on at least one teamlist
$query = 'SELECT count(*) as result FROM ' . sql_table('team'). ' WHERE tmember=' . $member->getID();
$teams = intval(quickQuery($query));
if ($teams == 0 && !$member->isAdmin())
	upload_doError(_ERROR_DISALLOWEDUPLOAD);

// get parameters
$responseType = requestVar('responseType');
$funcNum = requestVar('CKEditorFuncNum');
$CKEditor = requestVar('CKEditor');
$langCode = requestVar('langCode');


if (!$member->isAdmin() and $CONF['AllowUpload'] != true) {
	upload_doError(_ERROR_DISALLOWED);
} else {
	media_upload();
}

/**
  * accepts a file for upload
  */
function media_upload() {
	global $DIR_MEDIA, $member, $CONF, $funcNum, $responseType;
		
	$uploadInfo = postFileInfo('upload');
	
	$filename = $uploadInfo['name'];
	$filetype = $uploadInfo['type'];
	$filesize = $uploadInfo['size'];
	$filetempname = $uploadInfo['tmp_name'];
	$fileerror = intval($uploadInfo['error']);
	
	// clean filename of characters that may cause trouble in a filename using cleanFileName() function from globalfunctions.php
	$filename = cleanFileName($filename);
	if ($filename === false) 
		upload_doError(_ERROR_BADFILETYPE . $filename);
	
	switch ($fileerror)
	{
		case 0: // = UPLOAD_ERR_OK
			break;
		case 1: // = UPLOAD_ERR_INI_SIZE
		case 2:	// = UPLOAD_ERR_FORM_SIZE
			upload_doError(_ERROR_FILE_TOO_BIG);
		case 3: // = UPLOAD_ERR_PARTIAL
		case 4: // = UPLOAD_ERR_NO_FILE
		case 6: // = UPLOAD_ERR_NO_TMP_DIR
		case 7: // = UPLOAD_ERR_CANT_WRITE
		default:
			// include error code for debugging
			// (see http://www.php.net/manual/en/features.file-upload.errors.php)
			upload_doError(_ERROR_BADREQUEST . ' (' . $fileerror . ')');
	}
	
	if ($filesize > $CONF['MaxUploadSize'])
		upload_doError(_ERROR_FILE_TOO_BIG);
	
	// check file type against allowed types
	$ok = 0;
	$allowedtypes = explode (',', $CONF['AllowedTypes']);
	foreach ( $allowedtypes as $type )
	{
		if (preg_match("#\." .$type. "$#i",$filename)) $ok = 1;
	}
	if (!$ok) upload_doError(_ERROR_BADFILETYPE . $filename);
	
	if (!is_uploaded_file($filetempname))
		upload_doError(_ERROR_BADREQUEST);
	
	// prefix filename with current date (YYYYMMDD-HHMMSS-)
	// this to avoid nameclashes
	if ($CONF['MediaPrefix'])
		$filename = strftime("%Y%m%d-%H%M%S-", time()) . $filename;

	// currently selected collection
	$collection = requestVar('collection');
	if (!$collection || !@is_dir($DIR_MEDIA . $collection))
		$collection = $member->getID();

	// avoid directory travarsal and accessing invalid directory
	if (!MEDIA::isValidCollection($collection)) media_doError(_ERROR_DISALLOWED);

	$res = MEDIA::addMediaObject($collection, $filetempname, $filename);

	if ($res != '')
		upload_doError($res);

	$url = $CONF['MediaURL'] . $collection . '/' . $filename;
	if ($responseType != 'json') {
		echo "<script type='text/javascript'>window.parent.CKEDITOR.tools.callFunction(" . $funcNum . ", '" . $url . "', '');</script>";
	} else {
		$arr = array(
			'uploaded' => 1,
			'fileName' => $filename,
			'url' => $url
		);
		header( "Content-Type: application/json; charset=utf-8" );
		echo json_encode($arr);
	}
}

function upload_doError($msg) {
	global $funcNum, $responseType;
	if ($responseType != 'json') {
		echo "<script type='text/javascript'>window.parent.CKEDITOR.tools.callFunction(" . $funcNum . ", '', '" . $msg . "');</script>";
	} else {
		$arr = array(
			'uploaded' => 0,
			'error' => array(
				'message' => $msg
			)
		);
			
		header( "Content-Type: application/json; charset=utf-8" );
		echo json_encode($arr);
	}
	exit;
}


?>
