<?php

class SystemHandler extends OPHandler{
    // TRUNCATED FOR CLARITY

    public function syncNtpTime($params){
        $ntphost = $params['ntphost'];
        $this->paramsCheck($ntphost);

        $cmd = "systemctl stop ntpd";
        $opName = 'PT_SYSTEM_BACKGROUND_OP_DO_COMMAND';
        $msg = array('command'=>$cmd);
        $mbResult = $this->mbPFMsg($opName, json_encode($msg), true);
        if(!$mbResult['result']){
            return $this->muOpResult($mbResult['result'], Xphp::$_lang['WEB_SYSTEM_SETTING_TIME_NTP_STOP']);
        }

        $cmd = "ntpdate " . $ntphost;
        $opName = 'PT_SYSTEM_BACKGROUND_OP_DO_COMMAND';
        $msg = array('command'=>$cmd);
        $mbResult = $this->mbPFMsg($opName, json_encode($msg), true);
        if($mbResult['result']){
            $cmd = "hwclock -w";
            $opName = 'PT_SYSTEM_BACKGROUND_OP_DO_COMMAND';
            $msg = array('command'=>$cmd);
            $this->mbPFMsg($opName, json_encode($msg), true);
            $ext = array(
                "newTime" => date("Y-m-d H:i:s", $this->getSystemTime())
            );
        }
        return $this->muOpResult($mbResult['result'], Xphp::$_lang['WEB_SYSTEM_SETTING_TIME_NTP_SYNC'], null, null, 0, $ext);
    }

    public function deleteUpdateAPK($params){
        $md5 = $params['md5'];
        $file_name = $params['file_name'];
        if(empty($file_name)){
            return $this->muOpResult(false, Xphp::$_lang['WEB_SETTINGS_UPDATE_DOWNLOAD_FILE_CANCEL'], Xphp::$_lang['WEB_SETTINGS_UPDATE_DOWNLOAD_FILE_CANCEL_NAME_NULL'], "warning");
        }
        $path_to = Xphp::$_config['UPDATE_DOWNLOAD_FILE_PATH'];
        $path =$path_to.$file_name;
        $path_tmp = $path_to.$file_name.".tmp";
        $cmd = "rm -rf ".$path;
        $cmd_tmp = "rm -rf ".$path_tmp;
        exec($cmd);
        exec($cmd_tmp);
        
        $sql = "delete from bd_update_file where level1_md5 = ?";
        $result = $this->dbExec($sql,array($md5));
        
        return $this->muOpResult(true, Xphp::$_lang['WEB_SETTINGS_UPDATE_DOWNLOAD_FILE_CANCEL'], "", "success");
    }
   
    public function setNetworkCardInfo($params){
        $name = $params['NAME'];
        $ipaddr = $params['IPADDR'];
        $netmask = $params['NETMASK'];
        $gateway = $params['GATEWAY'];
        $dns = $params['DNS'];
        $this->paramsCheck($name);
        
        $params['TYPE'] = "Ethernet";           
        $params['BOOTPROTO'] = "static";
        $params['ONBOOT'] = "yes";      
		$params['DEVICE'] = $name;        
        $operate = Xphp::$_lang['WEB_SYSTEM_SETTING_NETWORK'] . "[" . $name . "]" . Xphp::$_lang['WEB_SYSTEM_SETTING_IP'];
        $this->checkTaskStatus($operate);
        
        $configFileContent = '';
        $networkCardPath = Xphp::$_config['NETWORKCARD']['path'] . Xphp::$_config['NETWORKCARD']['prefix'] . $name;
        if(file_exists($networkCardPath)){
            $cmd = "cat " . $networkCardPath;
            exec($cmd, $info);
            $keyArr = array("NAME", "DEVICE", "IPADDR", "NETMASK", "GATEWAY", "TYPE", "BOOTPROTO", "ONBOOT");
            foreach ($keyArr as $each){
                $flag = true;
                foreach ($info as $key){
                    if(stristr($key, $each)){
                        $configFileContent .= $each . "=" . $params[$each] . PHP_EOL;
                        $flag = false;
                        break;
                    }
                }
                if ($flag){
                    if(!empty($each) && "DNS" != substr($each, 0, 3)){
                        $configFileContent .=  $each . "=" . $params[$each] . PHP_EOL;
                    }
                }
            }
        }else{
            $keyArr = array("NAME", "DEVICE", "IPADDR", "NETMASK", "GATEWAY", "TYPE", "BOOTPROTO", "ONBOOT");
            foreach ($keyArr as $key){
                $configFileContent .= $key . "=" . $params[$key] . PHP_EOL;
            }
        }
        
        if(!empty($dns)){
            $comma = ",";
            if(stristr($dns, "，")){
                $comma = "，";
            }
            $dnsArr = explode($comma, $dns);
            $i = 1;
            foreach ($dnsArr as $each){
                $configFileContent .= "DNS" . $i++ . "=" . $each . PHP_EOL;
            }
        }
        $cmd = "echo '" . $configFileContent . "'>" . $networkCardPath;
        $opName = 'PT_SYSTEM_BACKGROUND_OP_DO_COMMAND';
        $msg = array('command'=>$cmd);
        $mbResult = $this->mbPFMsg($opName, json_encode($msg), true);
        
        $this->unifyWriteSystemLog($mbResult['result'], 'SYSTEM_SETTING_IP');
        
        if($mbResult['result']){
            $cmd = "/usr/sbin/service network restart;";
            $opName = 'PT_SYSTEM_BACKGROUND_OP_DO_COMMAND';
            $msg = array('command'=>$cmd);
            $mbResult = $this->mbPFMsg($opName, json_encode($msg), true);
            if($mbResult['result']){
                $this->restartService();
            }
        }
        
        return $this->muOpResult($mbResult['result'], $operate);
    }
}

?>