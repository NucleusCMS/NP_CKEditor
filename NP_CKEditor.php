<?php
define('BR', PHP_EOL);
class NP_CKEditor extends NucleusPlugin {
	function getName()           { return 'CKEditor'; }
	function getAuthor()         { return 'yamamoto, osamuh'; }
	function getURL()            { return 'http://kyms.ne.jp/'; }
	function getVersion()        { return '4.0'; }
	function supportsFeature($w) { return ($w == 'SqlTablePrefix') ? 1 : 0; }
	function getDescription()    { return 'Enable CKEditor to edit blog items'; }
	function getEventList()      { return array('PreSendContentType', 'AdminPrePageFoot', 'AdminPrePageHead', 'BookmarkletExtraHead'); }

	function install()
	{
		// disable the default javascript edit bar that comes with nucleus
		sql_query
		(
			"UPDATE "
			. sql_table('config')
			. " SET   value = '1'"
			. " WHERE name  = 'DisableJSTools'"
		);
	}

	function unInstall()
	{
		// restore to standard settings
		sql_query
		(
			"UPDATE "
			. sql_table('config')
			. " SET   value = '2'"
			. " WHERE name  = 'DisableJSTools'"
		);
	}

	function event_AdminPrePageHead(&$data)
	{
		global $action;
		$adminurl = $this->getAdminURL();
		$action = $data['action'];
		if (($action != 'createitem') && ($action != 'itemedit'))
		{
			return;
		}
		$CKEditor_version = $this->getOption('CKEditor_version');
		$this->_suspendConvertBreaks();
		$data['extrahead'].= '<script language="javascript" type="text/javascript" src="' . $adminurl . 'ckeditor/ckeditor.js"></script>' . BR;

}

	function event_BookmarkletExtraHead($data)
	{
		$adminurl = $this->getAdminURL();
		$CKEditor_version = $this->getOption('CKEditor_version');
		$this->_suspendConvertBreaks();
		$data['extrahead'].= '<script language="javascript" type="text/javascript" src="' . $adminurl . 'ckeditor/ckeditor.js"></script>' . BR;
	}

	var $isActive = false;
	function event_PreSendContentType($data)
	{
		if (substr($data['pageType'], 0, 6) == 'admin-')
		{
			ob_implicit_flush(false);
			$this->isActive = ob_start();
		}
	}

	function event_AdminPrePageFoot($data)
	{
		global $DIR_MEDIA, $CONF;
		$adminurl = $this->getAdminURL();
		$pluginDirectory = $this->getDirectory();
		$lang = (getLanguageName()=='japanese-utf8') ? 'ja':'en';
		if ($this->isActive)
		{
			$action = $data['action'];
			$str = ob_get_contents();
			ob_end_clean();
			$str .= '<script type="text/javascript">' . BR;
			$str .= '//<![CDATA[' . BR;
			$str .= "CKEDITOR.config.customConfig ='{$adminurl}ckeditor/config.js';" . BR;
			$str .= "CKEDITOR.config.language = '$lang';" . BR;
			$str .= "CKEDITOR.config.filebrowserBrowseUrl ='{$adminurl}media.php';" . BR;
			$str .= "CKEDITOR.config.filebrowserUploadUrl ='{$adminurl}upload.php';" . BR;
			$str .= "CKEDITOR.config.uploadUrl ='{$adminurl}upload.php?responseType=json';" . BR;
			$str .= "CKEDITOR.replace('body', {skin: 'flat,{$adminurl}ckeditor/skins/flat/'});" . BR;
			$str .= "CKEDITOR.replace('more', {skin: 'flat,{$adminurl}ckeditor/skins/flat/'});" . BR;
			$str .= BR;
			$str .= 'function getQuery()' . BR;
			$str .= '{' . BR;
			$str .= '	if(location.search.length > 1)' . BR;
			$str .= '	{' . BR;
			$str .= '		var get = new Object();' . BR;
			$str .= '		var ret = location.search.substr(1).split("&");' . BR;
			$str .= '		for(var i = 0; i < ret.length; i++)' . BR;
			$str .= '		{' . BR;
			$str .= '			var r = ret[i].split("=");' . BR;
			$str .= '			get[r[0]] = r[1];' . BR;
			$str .= '		}' . BR;
			$str .= '		return get;' . BR;
			$str .= '	}' . BR;
			$str .= '	else' . BR;
			$str .= '	{' . BR;
			$str .= '		return false;' . BR;
			$str .= '	}' . BR;
			$str .= '}' . BR;
			$str .= BR;
			$str .= 'var queryParam = getQuery();' . BR;
			$str .= 'var CKEditorFuncNum = queryParam["CKEditorFuncNum"];' . BR;
			$str .= BR;
			$str .= 'function includeImage(collection, filename, type, width, height,CKEditorFuncNum) {' . BR;
			$str .= 'var fullName;' . BR;
			$str .= "	fullName = '" . $CONF['MediaURL'] . "' + collection + '/' + filename;" . BR;
			$str .= 'CKEDITOR.tools.callFunction(CKEditorFuncNum, fullName);'. BR;
			$str .= 'window.close();' . BR;
			$str .= '}' . BR;
			$str .= '//]]>' . BR;
			$str .= '</script>' . BR;
			echo $str;
		}
	}

	function _suspendConvertBreaks()
	{
		global $manager, $blogid;
		$b = & $manager->getBlog($blogid);
		if ($b->getSetting('bconvertbreaks'))
		{
			$b->setConvertBreaks(false);
			$b->writeSettings();
		}
	}
}

?>
