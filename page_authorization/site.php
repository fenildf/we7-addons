<?php
/**
 * 网页授权模块微站定义
 *
 * @author laite
 * @url http://bbs.we7.cc/
 */
defined('IN_IA') or exit('Access Denied');

class Page_AuthorizationModuleSite extends WeModuleSite {

	public function doMobileIndex() {
		header("Content-type:text/html;charset=utf-8");
		global $_W,$_GPC;
		$appid = '';  // 1.appid
		$appsecret = '';  // 2.appsecret
		$code = '';
		$code = $_GPC['code'];
		if(empty($code)){
	    	$redirect_uri = urlencode("http://www.xxxx.com/app/index.php?i=2&c=entry&do=index&m=page_authorization"); //3.换成你想要跳转到的完整域名路径.这里可以在浏览器中查看 
	    	$url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=".$appid."&redirect_uri=".$redirect_uri."&response_type=code&scope=snsapi_userinfo&state=123#wechat_redirect";
	    	header('location:'.$url);
		}else{
			//echo $code."<br/>";
			//获取access_token和openid
			$url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=".$appid."&secret=".$appsecret."&code=".$code."&grant_type=authorization_code";
			$code = '';
			$res  = $this->http_curl($url,'get');
			//var_dump($res);
			$openid = $res['openid'];
			$access_token = $res['access_token'];
			//检验access_token是否有效
			$check_url = "https://api.weixin.qq.com/sns/auth?access_token=".$access_token."&openid=".$openid;
			$check_res = $this->http_curl($check_url,'get');
			//print_r($check_res);
			//获取用户信息
			if ($check_res['errcode']=='0'&&$check_res['errmsg']=='ok') {
				$url = "https://api.weixin.qq.com/sns/userinfo?access_token=".$access_token."&openid=".$openid."&lang=zh_CN";
				$res = $this->http_curl($url,'get');
				//print_r($res);
				/*echo "昵称:".$res['nickname']."<br/>";
				echo "性别:".$res['sex']."<br/>";
				echo "国家:".$res['country']."<br/>";
				echo "头像:".$res['headimgurl']."<br/>";*/
			}else{
				echo "access_token或openid无效！！！";
			}
			
		}



		include $this->template('index');

	}
	public function doWebRule1() {
		//这个操作被定义用来呈现 规则列表
	}
	public function doWebNav1() {
		//这个操作被定义用来呈现 管理中心导航菜单
	}
	public function doMobileWebsite1() {
		//这个操作被定义用来呈现 微站首页导航图标
	}

    public  function http_curl($url,$type='get',$res='json',$arr=''){
		//1.初始化curl
		$ch = curl_init();
		// $url = 'http://www.baidu.com';
		//2.设置curl的参数
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		if($type == 'post'){
			curl_setopt($ch,CURLOPT_POST,1);
			curl_setopt($ch,CURLOPT_POSTFIELDS,$arr);
		}
		//3.采集
		$output = curl_exec($ch);
		// curl_close($ch);
		if($res == 'json'){
	       if( curl_errno($ch) ){
	       	// 请求失败 返回错误信息
	         return curl_error($ch);
	       }
	       else{
	         return json_decode($output,true);
	       }	
		}
		
	}

}