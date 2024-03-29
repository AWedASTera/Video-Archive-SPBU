<?php
global $global, $config;
if(!isset($global['systemRootPath'])){
    require_once '../videos/configuration.php';
}
require_once $global['systemRootPath'] . 'objects/user.php';

class Plugin extends ObjectYPT {

    protected $id, $status, $object_data, $name, $uuid, $dirName;

    static function getSearchFieldsNames() {
        return array('name');
    }

    static function getTableName() {
        return 'plugins';
    }

    function getId() {
        return $this->id;
    }

    function getStatus() {
        return $this->status;
    }

    function getObject_data() {
        return $this->object_data;
    }
    
    function getPluginVersion() {
        return $this->pluginVersion;
    }
    
    function getName() {
        return $this->name;
    }

    function setId($id) {
        $this->id = $id;
    }

    function setStatus($status) {
        $this->status = $status;
    }

    function setObject_data($object_data) {
        $this->object_data = $object_data;
    }

    function setName($name) {
        $name = preg_replace("/[^A-Za-z0-9 _-]/", '', $name);
        $this->name = $name;
    }

    function getUuid() {
        return $this->uuid;
    }

    function getDirName() {
        return $this->dirName;
    }

    function setUuid($uuid) {
        $this->uuid = $uuid;
    }

    function setDirName($dirName) {
        $dirName = preg_replace("/[^A-Za-z0-9 _-]/", '', $dirName);
        $this->dirName = $dirName;
    }
    
    static function setCurrentVersionByUuid($uuid, $currentVersion){
        error_log("plugin::setCurrentVersionByUuid $uuid, $currentVersion");
        $p=static::getPluginByUUID($uuid);
        if(!$p){
            error_log("plugin::setCurrentVersionByUuid error on get plugin");
            return false;
        }
        //pluginversion isn't an object property so we must explicity update it using this function
        $sql="update ".static::getTableName()." set pluginversion='$currentVersion' where uuid='$uuid'";
        $res=sqlDal::writeSql($sql); 
    }
    
    static function getCurrentVersionByUuid($uuid){
        $p=static::getPluginByUUID($uuid);
        if(!$p)
        return false;
        //pluginversion isn't an object property so we must explicity update it using this function
        $sql="SELECT pluginversion FROM ".static::getTableName()." WHERE uuid=? LIMIT 1 ";
        $res = sqlDAL::readSql($sql, "s", array($uuid));
        $data = sqlDAL::fetchAssoc($res);
        sqlDAL::close($res);
        if (!empty($data)) {
            return $data['pluginversion'];
        } 
        return false;
    }
        

    static function getPluginByName($name) {
        global $global, $getPluginByName;
        if(empty($getPluginByName)){
            $getPluginByName = array();
        }
        if(empty($getPluginByName[$name])){
            $sql = "SELECT * FROM " . static::getTableName() . " WHERE name = ? LIMIT 1";
            $res = sqlDAL::readSql($sql, "s", array($name));
            $data = sqlDAL::fetchAssoc($res);
            sqlDAL::close($res);
            if (!empty($data)) {
                $getPluginByName[$name] = $data;
            } else {
                $getPluginByName[$name] = false;
            }
        }
        return $getPluginByName[$name];
    }

    static function getPluginByUUID($uuid) {
        global $global,$getPluginByUUID;
        if(empty($getPluginByUUID)){
            $getPluginByUUID = array();
        }
        if(empty($getPluginByUUID[$uuid])){
            $sql = "SELECT * FROM " . static::getTableName() . " WHERE uuid = ? LIMIT 1";
            $res = sqlDAL::readSql($sql, "s", array($uuid));
            $data = sqlDAL::fetchAssoc($res);
            sqlDAL::close($res);
            if (!empty($data)) {
                if(empty($data['pluginversion'])){
                    $data['pluginversion'] = "1.0";
                }
                $getPluginByUUID[$uuid] = $data;
            } else {
                $getPluginByUUID[$uuid] = false;
            }
        }
        return $getPluginByUUID[$uuid];
    }

    function loadFromUUID($uuid) {
        $uuid = preg_replace("/[^A-Za-z0-9 _-]/", '', $uuid);
        $this->uuid = $uuid;
        $row = static::getPluginByUUID($uuid);
        if (!empty($row)) {
            $this->load($row['id']);
        }
    }

    static function isEnabledByName($name) {
        $row = static::getPluginByName($name);
        if ($row) {
            return $row['status'] == 'active';
        }
        return false;
    }

    static function isEnabledByUUID($uuid) {
        $row = static::getPluginByUUID($uuid);
        if ($row) {
            return $row['status'] == 'active';
        }
        return false;
    }

    static function getAvailablePlugins() {
        global $global,$getAvailablePlugins;
        if(empty($getAvailablePlugins)){
            $dir = $global['systemRootPath'] . "plugin";
            $getAvailablePlugins = array();
            $cdir = scandir($dir);
            foreach ($cdir as $key => $value) {
                if (!in_array($value, array(".", ".."))) {
                    if (is_dir($dir . DIRECTORY_SEPARATOR . $value)) {
                        $p = YouPHPTubePlugin::loadPlugin($value);
                        if (!is_object($p) || $p->hidePlugin()) {
                            error_log("Plugin Not Found: {$value}");
                            continue;
                        }
                        $obj = new stdClass();
                        $obj->name = $p->getName();
                        $obj->dir = $value;
                        $obj->uuid = $p->getUUID();
                        $obj->description = $p->getDescription();
                        $obj->installedPlugin = static::getPluginByUUID($obj->uuid);
                        $obj->enabled = (!empty($obj->installedPlugin['status']) && $obj->installedPlugin['status'] === "active") ? true : false;
                        $obj->id = (!empty($obj->installedPlugin['id'])) ? $obj->installedPlugin['id'] : 0;
                        $obj->data_object = $p->getDataObject();
                        $obj->databaseScript = !empty(static::getDatabaseFile($value));
                        $obj->pluginMenu = $p->getPluginMenu();
                        $obj->tags = $p->getTags();
                        $obj->pluginversion=$p->getPluginVersion();
                        $getAvailablePlugins[] = $obj;
                    }
                }
            }
        }
        return $getAvailablePlugins;
    }

    static function getDatabaseFile($pluginName) {
        $filename = static::getDatabaseFileName($pluginName);
        if (!$filename) {
            return false;
        }
        return url_get_contents($filename);
    }

    static function getDatabaseFileName($pluginName) {
        global $global;
        $dir = $global['systemRootPath'] . "plugin";
        $filename = $dir . DIRECTORY_SEPARATOR . $pluginName . DIRECTORY_SEPARATOR . "install" . DIRECTORY_SEPARATOR . "install.sql";
        if (!file_exists($filename)) {
            return false;
        }
        return $filename;
    }

    static function getAllEnabled() {
        global $global, $getAllEnabledRows;
        if(empty($getAllEnabledRows)){
            $sql = "SELECT * FROM  " . static::getTableName() . " WHERE status='active' ";
            $res = sqlDAL::readSql($sql);
            $fullData = sqlDAL::fetchAllAssoc($res);
            sqlDAL::close($res);
            $getAllEnabledRows = array();
            foreach ($fullData as $row) {
                $getAllEnabledRows[] = $row;
            }
            uasort($getAllEnabledRows, 'cmpPlugin');
        }
        return $getAllEnabledRows;
    }

    static function getAllDisabled()
    {
        global $global, $getAllDisabledRows;
        if(empty($getAllDisabledRows)){
          $sql = "SELECT * FROM  " . static::getTableName() . " WHERE status='inactive' ";
          $res = sqlDAL::readSql($sql);
          $fullData = sqlDAL::fetchAllAssoc($res);
          sqlDAL::close($res);
          $getAllDisabledRows = array();
          foreach ($fullData as $row) {
            $getAllDisabledRows[] = $row;
          }
          uasort($getAllDisabledRows, 'cmpPlugin');
      }
        return $getAllDisabledRows;
    }

    static function getEnabled($uuid) {
        global $global,$getEnabled;
        if(empty($getEnabled)){
            $getEnabled = array();
        }
        if(empty($getEnabled[$uuid])){
            $getEnabled[$uuid] = array();
            $sql = "SELECT * FROM  " . static::getTableName() . " WHERE status='active' AND uuid = '".$uuid."' ;";
            $res = sqlDAL::readSql($sql);
            $pluginRows = sqlDAL::fetchAllAssoc($res);
            sqlDAL::close($res);
            if($pluginRows!=false){
                foreach($pluginRows as $row){
                    $getEnabled[$uuid][] = $row;
                }
            }
        }
        return $getEnabled[$uuid];
    }

    static function getOrCreatePluginByName($name, $statusIfCreate='inactive'){
        global $global;
        if(self::getPluginByName($name)===false){
            $pluginFile = $global['systemRootPath'] . "plugin/{$name}/{$name}.php";
            if(file_exists($pluginFile)){
                require_once $pluginFile;
                $code = "\$p = new {$name}();";
                eval($code);
                $plugin = new Plugin(0);
                $plugin->setDirName($name);
                $plugin->setName($name);
                $plugin->setObject_data(json_encode($p->getDataObject()));
                $plugin->setStatus($statusIfCreate);
                $plugin->setUuid($p->getUUID());
                $plugin->save();
            }
        }
        return self::getPluginByName($name);
    }
    
    function save() {
        if(empty($this->uuid)){
            return false;
        }
        global $global;
        $this->object_data = $global['mysqli']->real_escape_string($this->object_data);
        if(empty($this->object_data)){
            $this->object_data = 'null';
        }
        return parent::save();
    }

}
