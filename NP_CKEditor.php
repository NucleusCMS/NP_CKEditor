<?php
class NP_CKEditor extends NucleusPlugin {
    
    public $isActive = false;
    public $isEnabled  = true;
    
    function getName()           { return 'CKEditor'; }
    function getAuthor()         { return 'yamamoto, osamuh'; }
    function getURL()            { return 'http://nucleuscms.github.io/NP_CKEditor'; }
    function getVersion()        { return '4.6.2.1'; }
    function supportsFeature($feature) { return in_array($feature , array('SqlTablePrefix', 'SqlApi', 'SqlApi_SQL92')); }
    function getDescription()    { return 'CKEditor for Nucleus CMS'; }
    function getEventList()      {
        $this->createItemOption('cke_item_enable', 'CKEditor有効', 'yesno', 'yes');
        return array('PreSendContentType', 'AdminPrePageFoot', 'AdminPrePageHead', 'BookmarkletExtraHead', 'PrepareItemForEdit');
    }

    function event_AdminPrePageHead(&$data)
    {
        if(!$this->isEditAction($data['action'])) return;
        if(!$this->isEnabled)                     return;
        
        $this->_suspendConvertBreaks();
        $vs = array($this->getAdminURL().'ckeditor', $this->getVersion());
        $data['extrahead'].= vsprintf('<script language="javascript" type="text/javascript" src="%s/ckeditor.js?v=%s"></script>',$vs) . "\n";
        $data['extrahead'].= '<style>.cke_dialog a:link, .cke_dialog a:visited {text-decoration:none;}</style>';
    }

    function event_BookmarkletExtraHead(&$data)
    {
        if(!$this->isEnabled) return;
        
        $this->_suspendConvertBreaks();
        $vs = array($this->getAdminURL().'ckeditor', $this->getVersion());
        $data['extrahead'].= vsprintf('<script language="javascript" type="text/javascript" src="%s/ckeditor.js?v=%s"></script>',$vs) . "\n";
        $data['extrahead'].= '<style>.cke_dialog a:link, .cke_dialog a:visited {text-decoration:none;}</style>';
        $data['extrahead'].= $this->getInlinejs();
    }

    function event_PreSendContentType($data)
    {
        if (substr($data['pageType'], 0, 6) !== 'admin-') return;
        
        ob_implicit_flush(false);
        $this->isActive = ob_start();
    }

    function event_AdminPrePageFoot($data)
    {
        if(!$this->isEditAction($data['action'])) return;
        
        if (!$this->isActive) return;
        
        $str = ob_get_contents();
        ob_end_clean();
        echo $str . $this->getInlinejs();
    }

    function event_PrepareItemForEdit(&$data)
    {
        global $CONF;
        
        $content = $data['item']['body'].' '.$data['item']['more'];
        if(    strpos($content, '<%') !== false
            || strpos($content, '<!%') !== false
            || strpos($content, '<form') !== false
            || strpos($content, '</pre>') !== false
            ) {
            $this->isEnabled = false;
            $CONF['DisableJsTools'] = 0;
        } else {
            $this->isEnabled = true;
        }
    }
    
    private function getInlinejs()
    {
        static $called = FALSE;
        if ($called)
            return; // Use only once
        $called = TRUE;
        global $DIR_MEDIA, $CONF;
        $adminurl = $this->getAdminURL();
        $tpl = file_get_contents($this->getDirectory().'inlinejs.tpl');
        if ($tpl !== FALSE) {
            $ph['adminurl'] = $adminurl;
            $ph['lang']     = getLanguageName()==='japanese-utf8' ? 'ja':'en';
            $ph['MediaURL'] = $CONF['MediaURL'];
            return $this->parseText($tpl,$ph);
        }
        return '';
    }

    function _suspendConvertBreaks()
    {
        global $manager, $blogid;
        
        $b = & $manager->getBlog($blogid);
        if (!$b->getSetting('bconvertbreaks')) return;
        
        $b->setConvertBreaks(false);
        $b->writeSettings();
    }

    function isEditAction($action) {
        global $itemid;
        
        if($this->getItemOption($itemid,'cke_item_enable')==='no') return false;
        
        return ($action==='createitem' || $action==='itemedit');
    }
    
    function parseText($tpl='string',$ph=array()) {
        foreach($ph as $k=>$v) {
            $k = "<%{$k}%>";
            $tpl = str_replace($k, $v, $tpl);
        }
        return $tpl;
    }
    
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
}
