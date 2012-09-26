<?php
class My_Wsdl {
    protected $targetBranch;
    protected $userName;
    protected $passWord;
    protected $url;
    protected $request;
    protected $response;
    
    public function __construct(){
        /*
        $this->targetBranch = 'P107488';
        $this->userName = 'Universal API/uAPI2203635542-f6f4d5d8';
        $this->passWord = 'kH=4&e2F8K';
        $this->url = 'https://americas.copy-webservices.travelport.com/B2BGateway/connect/uAPI';
        */
        $this->targetBranch = 'P107488';
        $this->userName = 'Universal API/uAPI3328713833-136749ba';
        $this->passWord = 'g2pAqnQrqEYMrwJWgQXtmMKwa';
        $this->url = 'https://emea.copy-webservices.travelport.com/B2BGateway/connect/uAPI/AirService';
        
    }
    
    public function getUsername(){
        return $this->userName;
    }
    public function setUsername($username){
        $this->userName = $username;
    }
    public function getPassword(){
        return $this->passWord;
    }
    public function setPassword($password){
        $this->passWord = $password;
    }
    public function getTargetBranch(){
        return $this->targetBranch;
    }
    public function setTargetBranch($targetBranch){
        $this->targetBranch = $targetBranch;
    }
    public function getUrl(){
        return $this->url;
    }
    public function setUrl($url){
        $this->url = $url;
    }
    public function setRequest($req){
        $this->request = $req;
    }
    public function getRequest(){
        return $this->request;
    }
    public function setResponse($res){
        $this->response = $res;
    }
    public function getResponse(){
        return $this->response;
    }
    public function sendRequest(){
        $TARGETBRANCH = $this->targetBranch;
        $CREDENTIALS = $this->userName.':'.$this->passWord;
        $message = $this->request;
        $auth = base64_encode("$CREDENTIALS");
        $soap_do = curl_init ($this->url);
        $header = array(
            "Content-Type: text/xml;charset=UTF-8",
            //"Accept: gzip,deflate",
            "Cache-Control: no-cache",
            "Pragma: no-cache",
            //"SOAPAction: \"\"",
            "Authorization: Basic $auth",
            "Content-length: ".strlen($message),
            );
        curl_setopt($soap_do, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($soap_do, CURLOPT_TIMEOUT, 30);
        curl_setopt($soap_do, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($soap_do, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($soap_do,CURLOPT_RETURNTRANSFER, true);
        curl_setopt($soap_do, CURLOPT_POST, true );
        curl_setopt($soap_do, CURLOPT_POSTFIELDS, $message);
        curl_setopt($soap_do, CURLOPT_HTTPHEADER, $header);
        $return = curl_exec($soap_do);
        curl_close($soap_do);
        $this->setResponse($return);
        return $return;
    }
    
    public function getAvailabilitySearchService(){
        $res = $this->getResponse();
        preg_match_all("/<air:(.*?)>/i",$res,$p);
        $arr = array();
        $temp =array();
        if(isset($p[1]) && COUNT($p[1]) > 0){
            $a=0;$b=0;$i=0;$j=0;$k=0;
            foreach($p[1] as $key=>$record){
                preg_match('/AvailabilitySearchRsp/i',$record,$Rsp);
                if($Rsp){
                    preg_match('/TransactionId="(.*?)"/',$record,$trxId);
                    preg_match('/ResponseTime="(.*?)"/',$record,$respTm);
                    preg_match('/DistanceUnits="(.*?)"/i',$record,$DistTime);
                    $arr['AvailabilitySearchRsp']['values'] = array('TransactionId'=>$trxId[1],'ResponseTime'=>$respTm[1],'DistanceUnits'=>$DistTime[1]);
                    unset($trxId);
                    unset($respTm);
                    unset($DistTime);
                }
                preg_match('/AirSegmentList/i',$record,$AsgList);
                if($AsgList){
                    $a = $key;
                }
                preg_match('/AirSegment /i',$record,$AirSegment);
                if($AirSegment){
                    $b = $key;
                    $record1 = str_replace('AirSegment ','',$record);
                    $te = explode(' ',trim($record1));
                    unset($record1);
                    foreach($te as $t1){
                        $t2 = explode('=',$t1);
                        if(isset($t2[1]))
                        $arr['AirSegmentList'][$b]['AirSegment']['Value']["{$t2[0]}"] = $t2[1];
                    }
                }
                preg_match('/AirAvailInfo/i',$record,$AirAvailInfo);
                if($AirAvailInfo){
                    $i = $key;
                    $record1 = str_replace('AirAvailInfo ','',$record);
                    $te = explode(' ',trim($record1));
                    unset($record1);
                    foreach($te as $ke=>$t1){
                        $t2 = explode('=',$t1);
                        if(isset($t2[1]))
                        $arr['AirSegmentList'][$b]['AirSegment']['AirAvailInfo'][$i]['Value']["{$t2[0]}"] = $t2[1];
                    }
                }
                preg_match('/BookingCodeInfo/i',$record,$BookingCodeInfo);
                if($BookingCodeInfo){
                    $record1 = str_replace('BookingCodeInfo ','',$record);
                    $te = explode(' ',trim($record1));
                    unset($record1);
                    foreach($te as $ke=>$t1){
                        $t2 = explode('=',$t1);
                        if(isset($t2[1]))
                        $arr['AirSegmentList'][$b]['AirSegment']['AirAvailInfo'][$i]['BookingCodeInfo'][$ke]["{$t2[0]}"] = $t2[1];
                    }
                }
            }
        }
        return $arr;
    }
    public function getSeatMap(){
        $res = $this->getResponse();
        preg_match_all("/<air:(.*?)>/i",$res,$p);
        $arr = array();
        
        if(isset($p[1]) && COUNT($p[1]) > 0){
            $i=0;$j=0;$k=0;
            foreach($p[1] as $key=>$record){
                preg_match('/SeatMapRsp/i',$record,$Rsp);
                if($Rsp){
                    preg_match('/TransactionId="(.*?)"/',$record,$trxId);
                    preg_match('/ResponseTime="(.*?)"/',$record,$respTm);
                    $arr['SeatMapRsp'] = array('TransactionId'=>$trxId[1],'ResponseTime'=>$respTm[1]);
                    unset($trxId);
                    unset($respTm);
                }
                preg_match('/CabinClass/i',$record,$Cls);
                if($Cls){
                    preg_match('/Type="(.*?)"/',$record,$type);
                    $arr['CabinClass'] = $type[1];
                    unset($type);
                }
                preg_match('/Row /i',$record,$Row);
                if($Row){
                    $i = $key;
                    preg_match('/Row Number="(.*?)"/',$record,$number);
                    $arr['SeatRow'][$i]['Row'] = (isset($number[1])?$number[1]:$record);
                    unset($number);
                }
                preg_match('/Facility/i',$record,$fac);
                if($fac){
                    $j = $key;
                    preg_match('/Type="(.*?)"/',$record,$type);
                    preg_match('/SeatCode="(.*?)"/',$record,$seatCode);
                    preg_match('/Availability="(.*?)"/',$record,$avail);
                    $arr['SeatRow'][$i]['Seats'][$j]['Facility'] = array('Type'=>$type[1],'SeatCode'=>(isset($seatCode[1])?$seatCode[1]:'None'),'Availability'=>(isset($avail[1])?$avail[1]:'None'));
                    unset($type);
                    unset($seatCode);
                    unset($avail);
                }
                preg_match('/Characteristic/i',$record,$char);
                if($char){
                    preg_match('/Value="(.*?)"/',$record,$value);
                    $arr['SeatRow'][$i]['Seats'][$j]['Characteristic'] = $value[1];
                }
            }
        } else {
            preg_match_all("/<faultstring>(.*?)<\/faultstring>/i",$res,$arr);
        }
        //echo '<pre>';print_r($arr);die;
        return $arr;
    }
    
    function getFlightDetails(){
        $res = $this->getResponse();
        preg_match_all("/<air:(.*?)>/i",$res,$p);
        $arr = array();
        if(isset($p[1]) && COUNT($p[1]) > 0){
            $i=0;$j=0;$k=0;
            foreach($p[1] as $key=>$record){
                preg_match('/FlightDetailsRsp/i',$record,$Rsp);
                if($Rsp){
                    preg_match('/TransactionId="(.*?)"/',$record,$trxId);
                    preg_match('/ResponseTime="(.*?)"/',$record,$respTm);
                    $arr['FlightDetailsRsp'] = array('TransactionId'=>$trxId[1],'ResponseTime'=>$respTm[1]);
                    unset($trxId);
                    unset($respTm);
                }
                preg_match('/AirSegment /i',$record,$AirSegment);
                if($AirSegment){
                    $record1 = str_replace('AirSegment ','',$record);
                    $te = explode(' ',trim($record1));
                    unset($record1);
                    foreach($te as $ke=>$t1){
                        $t2 = explode('=',$t1);
                        if(isset($t2[1]))
                        $arr['AirSegment']["{$t2[0]}"] = $t2[1];
                    }
                }
                preg_match('/FlightDetails /i',$record,$FlightDetails);
                if($FlightDetails){
                    $record1 = str_replace('FlightDetails ','',$record);
                    $te = explode(' ',trim($record1));
                    unset($record1);
                    foreach($te as $ke=>$t1){
                        $t2 = explode('=',$t1);
                        if(isset($t2[1]))
                        $arr['FlightDetails']["{$t2[0]}"] = $t2[1];
                    }
                }
            }
        
            preg_match('/<air:InFlightServices>(.*?)<\/air:InFlightServices>/i',$res,$FlightService);
            if($FlightService){
                $arr['InFlightServices'] = $FlightService[1];
            }
        }
        return $arr;
    }
    
    function getPNRDetails(){
        $res = $this->getResponse();
        preg_match_all("/<air:(.*?)>/i",$res,$p);
        $arr = array();
        if(isset($p[1]) && COUNT($p[1]) > 0){
            foreach($p[1] as $key=>$record){
            preg_match('/AirSegment /i',$record,$AirSegment);
                if($AirSegment){
                    $b = $key;
                    $record1 = str_replace('AirSegment ','',$record);
                    $te = explode(' ',trim($record1));
                    unset($record1);
                    foreach($te as $t1){
                        $t2 = explode('=',$t1);
                        if(isset($t2[1]))
                        $arr['AirSegment']['Value']["{$t2[0]}"] = str_replace('"','',$t2[1]);
                    }
                }
                preg_match('/FlightDetails /i',$record,$AirAvailInfo);
                if($AirAvailInfo){
                    $record1 = str_replace('AirAvailInfo ','',$record);
                    $te = explode(' ',trim($record1));
                    unset($record1);
                    foreach($te as $ke=>$t1){
                        $t2 = explode('=',$t1);
                        if(isset($t2[1]))
                        $arr['AirSegment']['FlightDetails']["{$t2[0]}"] = str_replace('"','',$t2[1]);
                    }
                }
                preg_match('/BookingInfo /i',$record,$BookingCodeInfo);
                if($BookingCodeInfo){
                    $record1 = str_replace('BookingInfo ','',$record);
                    $te = explode(' ',trim($record1));
                    unset($record1);
                    foreach($te as $ke=>$t1){
                        $t2 = explode('=',$t1);
                        if(isset($t2[1]))
                        $arr['BookingInfo']["{$t2[0]}"] = str_replace('"','',$t2[1]);
                    }
                }
            }
        }
        return $arr;
    }
}