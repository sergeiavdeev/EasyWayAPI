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
        return $this->getRequest($url);
        
    }
    
        
    public function getPickupPoints(){
        
        $url = $this->url."getPickupPointsV2";
        return $this->getRequest($url);
    }
    
    public function createOrder($order){
        
        $params = array(
            "params" => array(
                "name" => "Order",
                "value" => $order
            )            
        );
                        
        $xml = new \SimpleXMLElement('<request/>');
        $this->to_xml($xml, $params);
        
        $url = $this->url."createOrder";
        
        return $this->postRequest($url, $xml->asXML());
    }
    
    protected function getRequest($url){
                
        curl_setopt($this->curl, CURLOPT_URL, $url);
        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($this->curl, CURLOPT_HEADER, 0);
        curl_setopt($this->curl, CURLOPT_USERPWD, $this->user.":".$this->pass);
        
        $output = curl_exec($this->curl);
        
        return $output;
    }
    
    protected function postRequest($url, $data){
        
        curl_setopt($this->curl, CURLOPT_URL, $url);
        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($this->curl, CURLOPT_HEADER, 0);
        curl_setopt($this->curl, CURLOPT_POST, true);
        curl_setopt($this->curl, CURLOPT_USERPWD, $this->user.":".$this->pass);
        
        curl_setopt($this->curl, CURLOPT_HTTPHEADER,
            array('Content-Type: text/xml; charset=utf-8',
                'Content-Length: '.strlen($data)));
        
        curl_setopt($this->curl, CURLOPT_POSTFIELDS, $data);
        
        $output = curl_exec($this->curl);
        
        return $output;
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
}