<?php
namespace Linsh\LakalaSdk;

use Linsh\LakalaSdk\Model\BaseRequest;
use Linsh\LakalaSdk\Model\OrderCreateReq;
use Linsh\LakalaSdk\Model\TransPreorderReq;
use Linsh\LakalaSdk\Model\RefundReq;
use Linsh\LakalaSdk\Model\CloseReq;
use Linsh\LakalaSdk\Model\TradequeryReq;
use GuzzleHttp\Client;

/**
 * 仅用于拉卡拉收银台收款服务
 */
class Lakala {
    //private $apiUrl = 'https://s2.lakala.com';
    private $apiUrl = 'https://test.wsmsd.cn/sit';
    private $appid;
    private $merchantNo;
    private $mchSerialNo;
    private $merchantPrivateKeyPath;
    private $lklCertificatePath;
    private $notifyUrl;
    private $callbackUrl;
    private $schema = 'LKLAPI-SHA256withRSA';
    private $version;
    
    //prod正式环境，test测试i环境
    public function __construct($params, $env='prod') {
        $this->appid                    = $params['appid'];
        $this->merchantNo               = $params['merchantNo'];
        $this->mchSerialNo              = $params['mchSerialNo'];
        $this->merchantPrivateKeyPath   = $params['merchantPrivateKeyPath'];
        $this->lklCertificatePath       = $params['lklCertificatePath'];
        $this->notifyUrl                = $params['notifyUrl'];
        $this->callbackUrl              = $params['callbackUrl'];
        if( !isset($params['version'] ))
        {
            $params['version']  = '3.0';
        }
        $this->version                  = $params['version'];
        $this->apiUrl = ($env =='prod')?'https://s2.lakala.com':'https://test.wsmsd.cn/sit'; 
    }

    /**
     * 收银台订单创建
     * @param string $orderInfo 订单标题
     * @param string $outOrderNo 订单号
     * @param string $totalAmount 总金额
     * @return mixed
     * @throws \Exception
     */
	public function orderCreate($orderInfo, $outOrderNo, $totalAmount) {
        $reqData = new OrderCreateReq();
        $reqData->order_info = $orderInfo;
        $reqData->out_order_no = $outOrderNo;
        $reqData->total_amount = $totalAmount;
        $reqData->merchant_no = $this->merchantNo;
        $reqData->order_efficient_time = date('YmdHis', time() + 600);
        $reqData->notify_url = $this->notifyUrl;
        $reqData->callback_url = $this->callbackUrl;

        $baseRequestVO = new BaseRequest();
        $baseRequestVO->req_time = date('YmdHis');
        $baseRequestVO->version = $this->version;
        $baseRequestVO->req_data = $reqData;

        $body = json_encode($baseRequestVO, JSON_UNESCAPED_UNICODE);
        $authorization = $this->getAuthorization($body);
   
        return $this->post($this->apiUrl . '/api/v3/ccss/counter/order/create', $body, $authorization);
    }

    //主扫交易接口
    public function transPreorder($termNo, $locationInfo, $outOrderNo, $totalAmount, $subject, $accBusiFields){
        $reqData = new TransPreorderReq();
        $reqData->merchant_no = $this->merchantNo;
        $reqData->term_no = $termNo;
        $reqData->out_trade_no = $outOrderNo;
        $reqData->account_type = 'WECHAT';
        $reqData->trans_type = '71';
        $reqData->total_amount = $totalAmount;
        $reqData->location_info = $locationInfo;
        $reqData->subject = $subject;
        $reqData->notify_url = $this->notifyUrl;
        $reqData->acc_busi_fields = $accBusiFields;

        $baseRequestVO = new BaseRequest(); 
        $baseRequestVO->req_data = $reqData;
        $baseRequestVO->req_time = date('YmdHis');
        $baseRequestVO->version = $this->version;

        $body = json_encode($baseRequestVO, JSON_UNESCAPED_UNICODE);
        logger()->info( $body );
        $authorization = $this->getAuthorization($body);
        try{
            return $this->post($this->apiUrl . '/api/v3/labs/trans/preorder', $body, $authorization);
        }catch(\Throwable $e) {
            throw new \Exception('请求异常,'.$e->getMessage());
        } 
    }

    //03退款交易 
    public function refund($termNo, $outOrderNo, $originOutTradeNo, $refundAmount, $refundReason='', $locationInfo)
    {
        $reqData = new RefundReq();
        $reqData->merchant_no = $this->merchantNo;
        $reqData->term_no = $termNo;
        $reqData->out_trade_no = $outOrderNo;
        $reqData->origin_out_trade_no = $originOutTradeNo;
        $reqData->refund_amount = $refundAmount;
        $reqData->refund_reason = $refundReason;
        $reqData->location_info = $locationInfo;

        $baseRequestVO = new BaseRequest(); 
        $baseRequestVO->req_data = $reqData;
        $baseRequestVO->req_time = date('YmdHis');
        $baseRequestVO->version = $this->version;

        $body = json_encode($baseRequestVO, JSON_UNESCAPED_UNICODE);
        echo $body;
        $authorization = $this->getAuthorization($body);
        try{
            return $this->post($this->apiUrl . '/api/v3/labs/relation/refund', $body, $authorization);
        }catch(\Throwable $e) {
            throw new \Exception('请求异常,'.$e->getMessage());
        } 
    }

    //05关单交易
    public function close($termNo, $originOutTradeNo, $originTradeNo, $locationInfo)
    {
        $reqData = new CloseReq();
        $reqData->merchant_no = $this->merchantNo;
        $reqData->term_no = $termNo; 
        $reqData->origin_out_trade_no = $originOutTradeNo;
        $reqData->origin_trade_no = $originTradeNo;
        $reqData->location_info = $locationInfo;

        $baseRequestVO = new BaseRequest(); 
        $baseRequestVO->req_data = $reqData;
        $baseRequestVO->req_time = date('YmdHis');
        $baseRequestVO->version = $this->version;

        $body = json_encode($baseRequestVO, JSON_UNESCAPED_UNICODE);
        echo $body;
        $authorization = $this->getAuthorization($body);
        try{
            return $this->post($this->apiUrl . '/api/v3/dcp/trans/close', $body, $authorization);
        }catch(\Throwable $e) {
            throw new \Exception('请求异常,'.$e->getMessage());
        } 
    }

    //06查询交易 
    public function tradequery($termNo, $outTradeNo)
    {
        $reqData = new TradequeryReq();
        $reqData->merchant_no = $this->merchantNo;
        $reqData->term_no = $termNo; 
        $reqData->out_trade_no = $outTradeNo;  
        $baseRequestVO = new BaseRequest(); 
        $baseRequestVO->req_data = $reqData;
        $baseRequestVO->req_time = date('YmdHis');
        $baseRequestVO->version = $this->version;

        $body = json_encode($baseRequestVO, JSON_UNESCAPED_UNICODE);
        echo $body;
        $authorization = $this->getAuthorization($body);
        try{
            return $this->post($this->apiUrl . '/api/v3/labs/query/tradequery', $body, $authorization);
        }catch(\Throwable $e) {
            throw new \Exception('请求异常,'.$e->getMessage());
        } 
    }

    /**
     * 签名
     * http://open.lakala.com/#/home/document/detail?title=%E6%89%AB%E7%A0%81-%E4%B8%BB%E6%89%AB&id=33
     */
	public function getAuthorization($body) {
		$nonceStr = $this->random(12);
     	$timestamp = time();
        //${appid}\n+${serialNo}\n+${timeStamp}\n+${nonceStr}\n+${body}\n
      	$message = $this->appid . "\n" . $this->mchSerialNo . "\n" . $timestamp . "\n" . $nonceStr . "\n" . $body . "\n";
        
		$key = openssl_get_privatekey(file_get_contents($this->merchantPrivateKeyPath));
        
        openssl_sign($message, $signature, $key, OPENSSL_ALGO_SHA256);
        openssl_free_key($key);

        return $this->schema . " appid=\"" . $this->appid . "\"," . "serial_no=\"" . $this->mchSerialNo . "\"," . "timestamp=\"" . $timestamp . "\"," . "nonce_str=\"" . $nonceStr . "\"," . "signature=\"" . base64_encode($signature) . "\"";
	}

    /**
     * 验签
     */
    public function signatureVerification($authorization, $body) {
        $authorization = str_replace($this->schema . " ", "", $authorization);
        $authorization = str_replace(",","&", $authorization);
        $authorization = str_replace("\"","", $authorization);
        $authorization = $this->convertUrlQuery($authorization);

        $authorization['signature'] = base64_decode($authorization['signature']);

        $message = $authorization['timestamp'] . "\n" . $authorization['nonce_str'] . "\n" . $body . "\n";

        $key = openssl_get_publickey(file_get_contents($this->lklCertificatePath));
        $flag = openssl_verify($message, $authorization['signature'], $key, OPENSSL_ALGO_SHA256);
        openssl_free_key($key);
        if($flag) {
            return true;
        }
        return false;
    }

    /**
     * 请求
     * @throws \Exception
     */
    public function post($url, $data, $authorization) {
        $client = new Client();
        $response = $client->post($url, [
            'headers' => [
                "Accept" => 'application/json',
                "Content-Type" => 'application/json',
                "Authorization" => $authorization,
            ],
            'body' => $data,
        ]);

        if (!$response) {
            throw new \Exception('请求异常');
        }
     
        $contents =  $response->getBody()->getContents();
        logger()->info( $contents );
        $result = json_decode($contents, true);
        logger()->info( $result );
        if (!isset($result['code']) || $result['code'] != 'BBS00000') {
            throw new \Exception('请求异常: ' . $result['msg']);
        }
        return $result;
    }

    //请求
    public function post2($url, $data, $authorization) {
            $headers = [
                "Authorization: " . $authorization,
                "Accept: application/json",
                "Content-Type:application/json",
            ];
    
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);//设置HTTP头
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, 1);
            $result = curl_exec($ch);
            curl_close($ch);
            $result = json_decode($result, true);
            if (!isset($result['code']) || $result['code'] != 'BBS00000') {
                throw new \Exception('请求异常: ' . $result['msg']);
            }
            return $result; 
        }

    //签名参数转数组
    private function convertUrlQuery($query) { 
        $queryParts = explode('&', $query); 
         
        $params = array(); 
        foreach ($queryParts as $param) { 
            $item = explode('=', $param); 
            $params[$item[0]] = $item[1]; 
        }
        if($params['signature']) {
            $params['signature'] = substr($query, strrpos($query, 'signature=') + 10);
        }
         
        return $params; 
    }

    /**
     * 生成随机数
     */
    private function random($len = 12) {
        $pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        return substr(str_shuffle(str_repeat($pool, ceil($len / strlen($pool)))), 0, $len);
    }
}







