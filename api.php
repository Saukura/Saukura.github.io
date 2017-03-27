<?php
/**
  * wechat php test
  */

//define your token
define("TOKEN", "weixin");
$wechatObj = new wechatCallbackapiTest();
$wechatObj->valid();

//开启自动回复
$wecharObj->responseMsg();


class wechatCallbackapiTest
{
	//实现valid方法:实现对接微信公众平台
	public function valid()
    {
		//接受随机字符串
        $echoStr = $_GET["echostr"];

        //valid signature , option
		//进行用户数字签名验证
        if($this->checkSignature()){
			//如果成功,则返回接受到的随机字符串
        	echo $echoStr;
        	exit;
        }
    }

    public function responseMsg()
    {
		//get post data, May be due to the different environments
		//接受用户发送过来的XML数据
		$postStr = $GLOBALS["HTTP_RAW_POST_DATA"];

      	//extract post data
		//判断XML数据是否为空
		if (!empty($postStr)){
                /* libxml_disable_entity_loader is to prevent XML eXternal Entity Injection,
                   the best way is to check the validity of xml by yourself */
                libxml_disable_entity_loader(true);
			//通过simplexml进行XML解析
              	$postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
			//访问者的名字
                $fromUsername = $postObj->FromUserName;
			//微信公众平台
                $toUsername = $postObj->ToUserName;
			//接受用户发送的关键词
                $keyword = trim($postObj->Content);
			//时间戳
                $time = time();
			//接受用户消息类型
			$msgType=$postObj->MsgType;
			//文本发送模板
                $textTpl = "<xml>
							<ToUserName><![CDATA[%s]]></ToUserName>
							<FromUserName><![CDATA[%s]]></FromUserName>
							<CreateTime>%s</CreateTime>
							<MsgType><![CDATA[%s]]></MsgType>
							<Content><![CDATA[%s]]></Content>
							<FuncFlag>0</FuncFlag>
							</xml>";
			//音乐发送模板
				$musicTpl="<xml>
							<ToUserName><![CDATA[%s]]></ToUserName>
							<FromUserName><![CDATA[%s]]></FromUserName>
							<CreateTime>%s</CreateTime>
							<MsgType><![CDATA[%s]]></MsgType>
							<Music>
							<Title><![CDATA[%s]]></Title>
							<Description><![CDATA[%s]]></Description>
							<MusicUrl><![CDATA[%s]]></MusicUrl>
							<HQMusicUrl><![CDATA[%s]]></HQMusicUrl>
							<ThumbMediaId><![CDATA[%s]]></ThumbMediaId>
							</Music>
							</xml>";
			//图文发送模板
				$newsTpl="<xml>
						<ToUserName><![CDATA[%s]]></ToUserName>
						<FromUserName><![CDATA[%s]]></FromUserName>
						<CreateTime>%s</CreateTime>
						<MsgType><![CDATA[%s]]></MsgType>
						<ArticleCount>%s</ArticleCount>
						%s
						</xml>";
			//判断用户发送关键词是否为空
			if($msgType=='text'){
				if(!empty( $keyword ))
                {
					if($keyword=='文本'){
						//回复类型,如果为"text"，代表文本类型
						$msgType = "text";
						//回复内容
						$contentStr = "你发送的是文本内容";
						//格式化字符串
						$resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
						//把XML数据返回给手机端
						echo $resultStr;
					}else if($keyword=='?' || $keyword=='？')
					{
						$msgType='text';
						$contentStr="【1】特种服务号码\n【2】通讯服务号码\n【3】银行服务号码\n您可以通过输入【】方括号的数字获取内容";
						$resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
						echo $resultStr;
					}else if($keyword=='1')
					{
						$msgType='text';
						$contentStr="常用服务号码:\n匪警:110\n火警:119";
						$resultStr=sprintf($textTpl,$fromUsername,$toUsername,$time,$msgType,$contentStr);
						echo $resultStr;
					}else if($keyword=='2'){
						$msgType='text';
						$contentStr="常用通讯服务号码:\n中国移动:10086\n中国电信:10000";
						$resultStr=sprintf($textTpl,$fromUsername,$toUsername,$time,$msgType,$contentStr);
						echo $resultStr;
					}
					else if(keyword=='3')
					{
						$msgType='text';
						$contentStr="常用银行号码:\n工商银行:95588\n中国银行:95533";
						$resultStr=sprintf($textTpl,$fromUsername,$toUsername,$time,$msgType,$contentStr);
						echo $resultStr;
					}else if(keyword=='音乐'){
						$msgType='music';
						$title='冰雪奇缘';
						$desc='《冰雪奇缘》原声大碟......';
						$url='http://czbk888.duapp.com/music.mp3';
						//定义高清音乐链接
						$hqurl="http://czbk888.duapp.com/music.mp3";
						$resultStr=sprintf($musicTpl,$fromUsername,$toUsername,$time,$msgType,$title,$desc,$url,$hqurl);
						echo $resultStr;
					}else if(keyword=='图文'){
						$msgType='news';
						$count=4;

						$str='<Articles>';
						for ($i=1;$i<=$count;++$i)
						{
							$str.='<item>
								<Title><![CDATA[微信开发{$i}]]></Title> 
								<Description><![CDATA[传智播客微信开发]]></Description>
								<PicUrl><![CDATA[http://czbk888.duapp.com/images/{$i}.jpg]]></PicUrl>
								<Url><![CDATA[http://www.itcast.cn]]></Url>
								</item>';
						}
						$str.='</Articles>';
						$resultStr=sprintf($newsTpl,$fromUsername,$toUsername,$time,$msgType,$count,$str);
					}
                }else{
                	echo "Input something...";
                }
			}else if($msgType=='image')
			{

				//回复类型,如果为"text"，代表文本类型
				$msgType = "text";
				//回复内容
				$contentStr = "你发送的图片消息";
				//格式化字符串
				$resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
				//把XML数据返回给手机端
				echo $resultStr;
			}
        }else {
        	echo "";
        	exit;
        }
    }
		
	private function checkSignature()
	{
        // you must define TOKEN by yourself
		//判断TONKEN秘钥是否定义
        if (!defined("TOKEN")) {
			//没有定义抛出异常
            throw new Exception('TOKEN is not defined!');
        }
        //接受微信加密签名
        $signature = $_GET["signature"];
		//接受时间戳
        $timestamp = $_GET["timestamp"];
		//接受随机数
        $nonce = $_GET["nonce"];
        //把TOKEN常量赋值给token变量
		$token = TOKEN;
		//把相关参数组装为数组(密钥,时间戳,随机数)
		$tmpArr = array($token, $timestamp, $nonce);
        // use SORT_STRING rule
		//通过字典法进行排序
		sort($tmpArr, SORT_STRING);
		//把排序后的数组转化为字符串
		$tmpStr = implode( $tmpArr );
		//通过哈希算法对字符串进行加密操作
		$tmpStr = sha1( $tmpStr );

		//与加密签名进行对比
		if( $tmpStr == $signature ){
			return true;
		}else{
			return false;
		}
	}
}

?>
