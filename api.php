<?php
if(!defined('IN_PLUGIN'))exit();

require_once(PAY_ROOT."inc/yuansfer.config.php");
require_once(PAY_ROOT."inc/yuansfer_submit.class.php");
if ($order['typename'] == 'alipay'){
    $paytype = 'alipay';
}elseif ($order['typename'] == 'wxpay'){
    $paytype = 'wechatpay';
}elseif ($order['typename'] == 'bank') {
    $paytype = 'unionpay';
}elseif ($order['typename'] = 'qrcode'){
}
//生成链接
$parameter = array(
"merchantNo" => $alipay_config['merchantNo'],
"storeNo" => $alipay_config['storeNo'],
"vendor" => $paytype,
'currency' => 'CNY',
'settleCurrency' => 'USD',
"ipnUrl"	=> 'https://api.star-horizon.net/gateway/8994b91d-a7b82852f826/yuansfer/notify/'.TRADE_NO.'/',
"callbackUrl"	=> 'https://api.star-horizon.net/gateway/8994b91d-a7b82852f826/yuansfer/return/'.TRADE_NO.'/',
'terminal' => DEVICE,
'reference' => $trade_no,
'description' => $trade_no,
'note' => $trade_no,
"amount"	=> (float)$order['realmoney']
);
//建立请求
$yuansferSubmit = new AlipaySubmit($alipay_config);
$origin_url = $yuansferSubmit->get_cashierUrl($parameter);
$Pay_url = 'https://api.star-horizon.net/api/cashier/yuansfer.php?cashierUrl='.base64_encode($origin_url);

/*//生成二维码链接（只能以USD货币收）
$Qr_parameter = array(
"merchantNo" => $alipay_config['merchantNo'],
"storeNo" => $alipay_config['storeNo'],
"vendor" => $paytype,
'currency' => 'USD',
'settleCurrency' => 'USD',
"ipnUrl"	=> 'https://api.star-horizon.net/gateway/8994b91d-a7b82852f826/yuansfer/notify/'.TRADE_NO.'/',
"timeout"	=> '120',
'needQrcode' => 'false',
'reference' => $trade_no,
"amount"	=> (float)$order['realmoney']
);
//建立WAP请求
$qrcodeSubmit = new AlipaySubmit($alipay_config);
$Qrcode_url = $qrcodeSubmit->get_qrcodeUrl($Qr_parameter);*/

//排列起来输出json
$result =  array("payUrl" => $Pay_url);
echo json_encode($result);