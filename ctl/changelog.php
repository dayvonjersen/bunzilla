<?php
class changelog extends Controller
{

    protected static function getVersions()
    {
        $versions = db()->query(
            'SELECT DISTINCT(version)
             FROM change_log 
             ORDER BY time ASC'
        )->fetchAll(PDO::FETCH_COLUMN);

        // let's always return an array so we can iterate predictably
        return $versions;
//        return count($versions) > 1 ? $versions : current($versions);        
    }

    protected static function getMessages( $version = null )
    {
        $where = '';
        if(is_array($version))
            $where = ' IN ('.implode(',',array_filter($version,function($v){return(preg_match('/^[a-z0-9\.\-]+$/i',$v));})) . ')';
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

        exit;
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
}
