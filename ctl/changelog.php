<?php
class changelog extends Controller
{
    public $breadcrumbs = [
        ['href'=>'changelog','title'=>'Changelog','icon'=>'icon-history'],
    ];
    protected static function getVersions()
    {
        $versions = db()->query(
            'SELECT DISTINCT(version)
             FROM change_log 
             ORDER BY time DESC'
        )->fetchAll(PDO::FETCH_COLUMN);

        return $versions;
    }

    protected static function getMessages( $version = null )
    {
        $where = '';
        if(is_array($version))
            $where = ' IN ('.implode(',', array_filter($version,function($v){
                return(preg_match('/^[a-z0-9\.\-]+$/i',$v));
            })) . ')';
        elseif(preg_match('/^[a-z0-9\.\-]+$/i',$version))
            $where = ' = '.db()->quote($version);
            
        $messages = db()->query(
            'SELECT *
             FROM change_log 
             '.($where ? "WHERE version $where" : '')
              .' ORDER BY time DESC'
        );

        if($messages->rowCount() > 1)
            return $messages->fetchAll(PDO::FETCH_ASSOC);
        return $messages->fetch(PDO::FETCH_ASSOC);
    }

    public function index()
    {
        $this->tpl .= '/index';
        $this->data['versions'] = self::getVersions();
        $this->data['messages'] = self::getMessages();
    }

    public function plaintext( $version = null )
    {
        $this->tpl .= '/plaintext';
        $this->data['versions'] = self::getVersions();
        if(in_array($version,$this->data['versions'],true))
        {
            $this->data['versions'] = [$version];
            $this->data['messages'] = self::getMessages($version);
        } else
            $this->data['messages'] = self::getMessages();
        
    }

    public function plainhtml( $version = null )
    {
        $this->plaintext($version);
        unset($this->tpl);
        require_once BUNZ_TPL_BASE_DIR . 'nofrills/changelog/index.inc.php';
        exit;
    }
}
