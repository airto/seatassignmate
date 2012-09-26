<?php
/*
*   uAPI sample communication in php language
*   php is typeless so you have to modify the wsdl definitions 
*   for example :
*   ...
*   Air2.wsdl:        <operation name="service2"-->
*   ...
*   AirAbstract2.wsdl:        <operation name="service2">
*   ...
*   (C) 2010 Travelport, Inc.
*   This code is for illustration purposes only.
*/
class My_SoapClient extends SoapClient {
    function __doRequest($request, $location, $action, $version) {
        if ($k=preg_match_all("/(xmlns:[a-zA-Z\-_A-Z0-9\"=\/\:\.]*)/i",$request,$regs) )
        {
            foreach ($regs[0] as $vali)
            {
                if (strpos($vali,"SOAP-ENV")==false)
                {
                    $csere.=" ".$vali;
                    $request=str_replace($vali." ","",$request);
                    $request=str_replace($vali,"",$request);
                }
            }
        }
    
        $request=str_replace("<ns2:LowFareSearchReq","<ns2:LowFareSearchReq".$csere,$request);
        
        $request=str_replace('<?xml version="1.0" encoding="UTF-8"?-->','', $request);
        
        $request=str_replace(' >','>', $request);
        $request=str_replace('common_v6_0','common_v5_0', $request);
        $request=str_replace('air_v9_0','air_v8_0', $request);
        $request=substr($request,1);
        return parent::__doRequest($request, $location, $action, $version);
    }
    
    function __getLastRequestHeaders()
    {
        return parent::__getLastRequestHeaders();
    }
    
    function __getLastRequest()
    {
        return parent::__getLastRequest();
    }
}    