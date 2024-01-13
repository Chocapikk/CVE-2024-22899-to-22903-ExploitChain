<?php

public function getVerifydiyResult($params){
        $type = intval($params['type']);
        $value = $params['value'];
        $this->paramsCheck($type, $value);
        
        $verifyResult = false;
        $verifyType = Xphp::$_config['VERIFY_TYPE'];
        switch ($type){
            case $verifyType['IP']:
                $verifyResult = $this->verifyPing($value);
                break;
            case $verifyType['WEB']:
                $verifyResult = $this->verifyWeb($value);
                break;
        }
        $result = array(
            "result" => $verifyResult
        );
        return json_encode($result);
    }
    
    /**
     * ping验证
     * @param string $ip
     * @return boolean
     */
    private function verifyPing($ip){
        exec("ping -c 1 $ip", $outcome, $status);
        $result = 0 == $status ? true : false;
        return $result;
    }
?>    