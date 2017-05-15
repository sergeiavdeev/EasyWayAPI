<?php

namespace EasyWay\API;

class EWConnector{
    
    private $url;
    private $user;
    private $pass;
    private $curl;
    
    function __construct($url, $user, $pass){
     
        $this->url = $url;
        $this->user = $user;
        $this->pass = $pass;
        $this->curl = curl_init();
    }
    
    function __destruct(){
        
        curl_close($this->curl);
    }
    
    public function getTariff($locationFrom, $locationTo, $weight, $volume){
        
        $url = $this->url."getTariff?locationFrom=".$locationFrom."&locationTo=".$locationTo."&weight=".$weight."&volume=".$volume;
        return json_decode($this->getRequest($url), true);
        
    }
    
        
    public function getPickupPoints(){
        
        $url = $this->url."getPickupPointsV2";
        return json_decode($this->getRequest($url), true);
    }
    
    public function createOrder($order){
        
        $addresFrom = $order["locationFrom"];
        $addresTo = $order["locationTo"];
                
        $order["locationFrom"] = array(
            "addressString" => $addresFrom
        );
        
        $order["locationTo"] = array(
            "addressString" => $addresTo
        );
        
        $params = array(
            "params" => array(
                "name" => "Order",
                "value" => $order
            )            
        );
                        
        $xml = new \SimpleXMLElement('<request/>');
        $this->to_xml($xml, $params);
        
        $url = $this->url."createOrder";
        
        $result =  $this->postRequest($url, $xml->asXML());
        return $result;
    }
    
    
    public function getStatus($orderIds){
        
        $query="";
        
        for ($i = 0; $i < count($orderIds); $i++){
            if($i == count($orderIds) - 1){
                
                $query = $query.$orderIds[$i];    
            } else{
                
                $query = $query.$orderIds[$i].",";    
            }
        }
        
        $url = $this->url."getStatus?json=&number=".$query;
        
        $result = $this->getRequest($url);
        
        //echo $result;
        
        return json_decode($result, true);
    }
    
    protected function getRequest($url){
                
        curl_setopt($this->curl, CURLOPT_URL, $url);
        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($this->curl, CURLOPT_HEADER, 0);
        curl_setopt($this->curl, CURLOPT_HTTPHEADER, array());
        curl_setopt($this->curl, CURLOPT_POST, false);
        curl_setopt($this->curl, CURLOPT_USERPWD, $this->user.":".$this->pass);
        
        $output = curl_exec($this->curl);
        
        return $output;
    }
    
    protected function postRequest($url, $data){
        
        curl_setopt($this->curl, CURLOPT_URL, $url);
        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, 1);        
        curl_setopt($this->curl, CURLOPT_POST, true);
        curl_setopt($this->curl, CURLOPT_USERPWD, $this->user.":".$this->pass);
        
        curl_setopt($this->curl, CURLOPT_HTTPHEADER,
            array('Content-Type: text/xml; charset=utf-8',
                'Content-Length: '.strlen($data)));
        
        curl_setopt($this->curl, CURLOPT_POSTFIELDS, $data);
        
        $output = curl_exec($this->curl);
        
        $arr = $this->to_array($output);
        
        return array(
            "isError" => $arr["fault"]["isError"],
            "errors" => $arr["fault"]["errors"],
            "id" => $arr["value"]
        );
    }
    
    
    private function to_xml(\SimpleXMLElement $object, array $data)
    {
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $new_object = $object->addChild($key);
                $this->to_xml($new_object, $value);
            } else {
                $object->addChild($key, $value);
            }
        }
    }
    
    
    private function to_array ($string)
    {
        $xml   = simplexml_load_string($string, 'SimpleXMLElement', LIBXML_NOCDATA);
        
        $array = json_decode(json_encode($xml, JSON_UNESCAPED_SLASHES), TRUE);
        
        return $array;
    }
}