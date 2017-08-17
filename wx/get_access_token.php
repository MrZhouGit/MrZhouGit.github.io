<?php 
/*access_token 生存周期是7200秒内(2小时)
*
*   故逻辑应为: 任何数据要求access_token的逻辑都应该与去判断$_session中生存周期的access_token是否存在
*   不存在则从新调取
*/


class center_wx{
	private $grant_type = 'client_credential';
	private $appID = 'wx5be444bc85cffb7b';
	private $appsecret = 'ce3914d012d125601ed996acbf0ec8c3';
	private $url = "https://api.weixin.qq.com/cgi-bin/token?";
	
	public function get_token(){
		session_start();
		//判断生效日期以及是否有access
		if($_SESSION['WeChat']['access_token'] && $_SESSION['WeChat']['time'] >= time()){
			return $_SESSION['WeChat']['access_token'];
		}else{
			return $this->create_token();
		}
	}
	
	private function create_token(){
		$mergo_str = "grant_type={$this->grant_type}&appid={$this->appID}&secret={$this->appsecret}";
		$https_url = $this->url.$mergo_str;
		$access_token = $this->https_curl($https_url);
		$wechat_array = json_decode($access_token, true);
		if($wechat_array['access_token'] && $wechat_array['expires_in']){
			$_SESSION['WeChat']['access_token'] = $wechat_array['access_token'];
			$_SESSION['WeChat']['time'] = time()+7210;
			$_SESSION['WeChat']['errcode'] = $wechat_array['errcode'];
			$_SESSION['WeChat']['errmsg'] = $wechat_array['errmsg'];
		}
		return $wechat_array['access_token'];
	}
	
	private function https_curl($url){
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); 
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE); 
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$output = curl_exec($ch);
		curl_close($ch);
		
		return $output;
	}
}
$access =  new center_wx();
print_r($access->get_token());


?>