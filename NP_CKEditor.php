<?php
class NP_CKEditor extends NucleusPlugin {
	
	var $isActive = false;
	
	function getName()           { return 'CKEditor'; }
	function getAuthor()         { return 'yamamoto, osamuh'; }
	function getURL()            { return 'http://nucleuscms.github.io/NP_CKEditor'; }
	function getVersion()        { return '4.5.6.1'; }
	function supportsFeature($w) { return ($w == 'SqlTablePrefix') ? 1 : 0; }
	function getDescription()    { return 'CKEditor for Nucleus CMS'; }
	function getEventList()      { return array('PreSendContentType', 'AdminPrePageFoot', 'AdminPrePageHead', 'BookmarkletExtraHead'); }

	function install()
	{
		// disable the default javascript edit bar that comes with nucleus
		sql_query(sprintf("UPDATE %s SET value='1' WHERE name='DisableJSTools'", sql_table('config')));
	}

	function unInstall()
	{
		// restore to standard settings
		sql_query(sprintf("UPDATE %s SET value='2' WHERE name='DisableJSTools'", sql_table('config')));
	}

	function event_AdminPrePageHead(&$data)
	{
		$action = $data['action'];
		if ($action != 'createitem' && $action != 'itemedit')
		{
			return;
		}
		$this->_suspendConvertBreaks();
		$ckeditor_path = $this->getAdminURL() . 'ckeditor/ckeditor.js?v=' . $this->getVersion();
		$data['extrahead'].= '<script language="javascript" type="text/javascript" src="' . $ckeditor_path . '"></script>' . "\n";
	}

	function event_BookmarkletExtraHead(&$data)
	{
		$this->_suspendConvertBreaks();
		$ckeditor_path = $this->getAdminURL() . 'ckeditor/ckeditor.js?v=' . $this->getVersion();
		$data['extrahead'].= '<script language="javascript" type="text/javascript" src="' . $ckeditor_path . '"></script>' . "\n";
	}

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
		
		if (!$this->isActive) return;
		
		$str = ob_get_contents();
		ob_end_clean();
		$adminurl = $this->getAdminURL();
		$tpl = file_get_contents($adminurl.'inlinejs.tpl');
		$ph['adminurl'] = $adminurl;
		$ph['lang']     = getLanguageName()==='japanese-utf8' ? 'ja':'en';
		$ph['MediaURL'] = $CONF['MediaURL'];
		$str .= $this->parseText($tpl,$ph);
		echo $str;
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

	function parseText($tpl='string',$ph=array()) {
		foreach($ph as $k=>$v) {
			$k = "<%{$k}%>";
			$tpl = str_replace($k, $v, $tpl);
		}
		return $tpl;
	}
}
