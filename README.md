# 拉卡拉收银台SDK

## 文档地址

- <http://open.lakala.com/#/home/document/detail?title=%E5%85%AC%E5%85%B1%E5%8F%82%E6%95%B0&id=282>

## 使用说明

### 收银台订单创建

```php
$lakala = new \Linshaoneng\LakalaSdk\Lakala([
    'appid' => 'OP00000xxx',
    'merchantNo' => 'xxx',
    'mchSerialNo' => 'xxx',
    'merchantPrivateKeyPath' => '/lakala/key/xxx.pem',
    'lklCertificatePath' => '/lakala/key/xxx.cer',
    'notifyUrl' => 'http://127.0.0.1/notify',
    'callbackUrl' => 'http://127.0.0.1/callback',
]);

// 金额 分
$result = $lakala->orderCreate('订单标题', '订单号', '200');
```

### 收银台支付验签

```php
$returnData = [
    "code" => "SUCCESS",
    "message" => "执行成功"
];

$authorization = $_SERVER['HTTP_AUTHORIZATION'];
$response = file_get_contents("php://input");

$lakala = new \Linshaoneng\LakalaSdk\Lakala([
    'appid' => 'OP00000xxx',
    'merchantNo' => 'xxx',
    'mchSerialNo' => 'xxx',
    'merchantPrivateKeyPath' => '/lakala/key/xxx.pem',
    'lklCertificatePath' => '/lakala/key/xxx.cer',
]);

if(!$lakala->signatureVerification($authorization, $response)) {
    //签名不通过
    $returnData = [
        "code" => "ERROR",
        "message" => "签名不通过"
    ];
}

return $returnData;
```

### 主扫交易接口
```php

require __DIR__.'/vendor/autoload.php';


$lakala = new \Linshaoneng\LakalaSdk\Lakala([
    'appid' => 'OP00000003',
    'merchantNo' => '8221210594300JY',
    'mchSerialNo' => '00dfba8194c41b84cf',
    'merchantPrivateKeyPath'   => 'tests/tests/OP00000003_private_key.pem',
    'lklCertificatePath'       => 'tests/tests/OP00000003_cert.cer',
    'notifyUrl' => 'http://127.0.0.1/notify',
    'callbackUrl' => 'http://127.0.0.1/callback',
], 'test');

// 金额 分
$termNo = 'A0073841';
$locationInfo = ['request_ip'=>'123.72.62.189'];
$outOrderNo = time();
$totalAmount = 1;
$subject = '证件照001';
$accBusiFields = ['sub_appid'=>'wx9ef39b708f16694d', 'user_id'=>'oYOA95UOoDM9wXiM3TZ_OMGtQnPA'];
try {
    $result = $lakala->transPreorder($termNo, $locationInfo, $outOrderNo, $totalAmount, $subject, $accBusiFields);
    print_r($result['resp_data']['acc_resp_fields']);
    // echo json_encode($result['resp_data']['acc_resp_fields']);
} catch(\Throwable $e) {
    echo "\r\n";
    print_r($e->getMessage());
}
```