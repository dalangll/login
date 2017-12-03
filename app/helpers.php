<?php
 if(!function_exists('getIp')){
     function getIp(){

         $mainIp = '';
         if (getenv('HTTP_CLIENT_IP'))
             $mainIp = getenv('HTTP_CLIENT_IP');
         else if(getenv('HTTP_X_FORWARDED_FOR'))
             $mainIp = getenv('HTTP_X_FORWARDED_FOR');
         else if(getenv('HTTP_X_FORWARDED'))
             $mainIp = getenv('HTTP_X_FORWARDED');
         else if(getenv('HTTP_FORWARDED_FOR'))
             $mainIp = getenv('HTTP_FORWARDED_FOR');
         else if(getenv('HTTP_FORWARDED'))
             $mainIp = getenv('HTTP_FORWARDED');
         else if(getenv('REMOTE_ADDR'))
             $mainIp = getenv('REMOTE_ADDR');
         else
             $mainIp = 'UNKNOWN';
         return $mainIp;
     }
 }
 if(!function_exists('getAddress')) {
     function getAddress()
     {
         $ip = getIp();

         $aliaddress = "http://ip.taobao.com/service/getIpInfo.php?ip=$ip";

         $client = new \GuzzleHttp\Client;
         $ipdata = $client->get($aliaddress);

         $result = json_decode($ipdata->getBody()->getContents(), true);

         /*判断结果是否为空*/
         if ($result['code'] == 0) {
             /*返回组装的ip和地址*/
             return $result['data']['country'] . ',' . $result['data']['region'] . ',' . $result['data']['city'] . ',' . $result['data']['ip'];
         }
     }
 }
 /*根据ip得到经纬度*/
if(!function_exists('getRadian')){
    function getRadian($ip){
        $latlng = "http://api.map.baidu.com/location/ip?ip=$ip&ak=您的AK&coor=bd09ll";
        $client = new \GuzzleHttp\Client;
        $ipdata = $client->get($latlng);
        $result = json_decode($ipdata->getBody()->getContents(), true);
        $lng=$result->{'content'}->{'location'}->{'lng'};//提取经度数据

        $lat=$result->{'content'}->{'location'}->{'lat'};//提取纬度数据
       $data=[
           'lng'=>$lng,
           'lat'=>$lat
       ];
       return $data;
    }
}
//根据两点经纬度计算距离
if(!function_exists('distance')){
     function distance($lat1,$lat2,$lng1,$lng2)
    {

        //将角度转为狐度
        $radLat1 = deg2rad($lat1);//deg2rad()函数将角度转换为弧度
        $radLat2 = deg2rad($lat2);
        $radLng1 = deg2rad($lng1);
        $radLng2 = deg2rad($lng2);
        $a = $radLat1 - $radLat2;
        $b = $radLng1 - $radLng2;
        $s = 2*asin(sqrt(pow(sin($a/2),2)+cos($radLat1)*cos($radLat2)*pow(sin($b/2),2)))*6371;
        return round($s,1);
    }
}

