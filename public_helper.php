<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if(!function_exists('send_email')){
    function send_email($email){
        $filed = 'method=send_email&';
        foreach ($email as $key => $value) {
            $filed .= $key.'='.$value.'&';
        }
        $filed = rtrim($filed,'&');


        $ch = curl_init();
        curl_setopt($ch,CURLOPT_URL,SERVICE_HOST.':'.SERVICE_PORT);
        curl_setopt($ch,CURLOPT_POST,true);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
        curl_setopt($ch,CURLOPT_POSTFIELDS,$filed);
        $return=curl_exec($ch);
//         var_dump($return);
        curl_close($ch);
    }
}
/**
 *  获取IP
 */
if(!function_exists('get_real_ip')){
	function get_real_ip(){
	     static $realip;
	     if(isset($_SERVER)){
	        if(isset($_SERVER['HTTP_X_FORWARDED_FOR'])){
	            $realip=$_SERVER['HTTP_X_FORWARDED_FOR'];
	        }else if(isset($_SERVER['HTTP_CLIENT_IP'])){
	             $realip=$_SERVER['HTTP_CLIENT_IP'];
	        }else{
	            $realip=$_SERVER['REMOTE_ADDR'];
	        }
	     }else{
	         if(getenv('HTTP_X_FORWARDED_FOR')){
	             $realip=getenv('HTTP_X_FORWARDED_FOR');
	        }else if(getenv('HTTP_CLIENT_IP')){
	             $realip=getenv('HTTP_CLIENT_IP');
	         }else{
	             $realip=getenv('REMOTE_ADDR');
	         }
	    }
	     return $realip;
	 }
}


/**
 *	获取地理位置
 */
if(!function_exists('getIPLoc_QQ')){
	 function getIPLoc_QQ($queryIP){
	    $url = 'http://ip.qq.com/cgi-bin/searchip?searchip1='.$queryIP;
	    $ch = curl_init($url);
	    curl_setopt($ch,CURLOPT_ENCODING ,'gb2312');
	    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true) ; // 获取数据返回
	    $result = curl_exec($ch);
	    $result = mb_convert_encoding($result, "utf-8", "gb2312"); // 编码转换，否则乱码
	    curl_close($ch);
	    preg_match("@<span>(.*)</span></p>@iU",$result,$ipArray);
	    $loc = $ipArray[1];
	    return $loc;
    }
}

if(! function_exists('ajaxReturn')) {
    /**
     * Ajax方式返回数据到客户端
     * @access protected
     * @param mixed $data 要返回的数据
     * @param String $type AJAX返回数据格式
     * @return void
     */
    function ajaxReturn($data=array(),$type='JSON') {
      ob_start();
        ob_end_clean();
        switch (strtoupper($type)){
            case 'JSON' :
                // 返回JSON数据格式到客户端 包含状态信息
                header('Content-Type:application/json; charset=utf-8');
                exit(json_encode($data));
            case 'XML'  :
                // 返回xml格式数据
                header('Content-Type:text/xml; charset=utf-8');
                exit(xml_encode($data));
            case 'JSONP':
                // 返回JSON数据格式到客户端 包含状态信息
                header('Content-Type:application/json; charset=utf-8');
                $handler  =   isset($_GET['callback']) ? $_GET['callback'] : 'callback';
                exit($handler.'('.json_encode($data).');');
            case 'EVAL' :
                // 返回可执行的js脚本
                header('Content-Type:text/html; charset=utf-8');
                exit($data);
            default     :
                // 用于扩展其他返回格式数据
        }
    }
}

if(!function_exists('send_sms')){

    function send_sms($mobile,$message){
        $filed = 'method=send_message&mobile='.$mobile.'&message='.$message;
        $ch = curl_init();
        curl_setopt($ch,CURLOPT_URL,SERVICE_HOST.':'.SERVICE_PORT);
        curl_setopt($ch,CURLOPT_POST,true);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
        curl_setopt($ch,CURLOPT_POSTFIELDS,$filed);
        curl_exec($ch);
        curl_close($ch);
    }
}

if(!function_exists('request_curl')){

    /**
     * 服务器通过post/get请求获得内容
     * @param  [string] $url    [请求url]
     * @param  [string] $method [请求方式]
     * @param  [json] $data   [{"action_name": "QR_LIMIT_STR_SCENE", "action_info": {"scene": {"scene_str": "123"}}}]
     * @return [array]         []
     */
    function request_curl($url,$method,$data="")
    {
        if($method=="get"){
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_URL, $url);
            $response =  curl_exec($ch);
            curl_close($ch);
        }
        else if($method=="post"){
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Content-Length:' . strlen($data)
                ));
            $response = curl_exec($ch);
            curl_close($ch);
        }
        $res = json_decode($response,true);
        return $res;
    }
}

/**
 * 调微信接口获取access_token
 */
if(!function_exists("get_token")){

    function get_token($appid,$appsecret){
        $url_get_token = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$appid."&secret=".$appsecret;
        $res_token = request_curl($url_get_token,"get");
        if(!empty($res_token['access_token'])){
            $token = $res_token['access_token'];
            $redis->setEx('WXTOKEN:'.$appid,7200,$token);
            return $token;
        }
        else {return "fail";}
    }
}
