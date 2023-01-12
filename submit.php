<?php
if(!defined('IN_PLUGIN'))exit();

require_once(PAY_ROOT."inc/yuansfer.config.php");
require_once(PAY_ROOT."inc/yuansfer_submit.class.php");
if ($order['typename'] == 'alipay'){
    $paytype = 'alipay';
}elseif ($order['typename'] == 'wxpay') {
    $paytype = 'wechatpay';
}elseif ($order['typename'] == 'bank') {
    $paytype = 'unionpay';
}elseif ($order['typename'] = 'qrcode')
{
    
}

$agent = check_wap();
// check if wap
    function check_wap(){
         $regex_match="/(nokia|iphone|android|motorola|^mot\-|softbank|foma|docomo|kddi|up\.browser|up\.link|";

         $regex_match.="htc|dopod|blazer|netfront|helio|hosin|huawei|novarra|CoolPad|webos|techfaith|palmsource|";

         $regex_match.="blackberry|alcatel|amoi|ktouch|nexian|samsung|^sam\-|s[cg]h|^lge|ericsson|philips|sagem|wellcom|bunjalloo|maui|";

         $regex_match.="symbian|smartphone|midp|wap|phone|windows ce|iemobile|^spice|^bird|^zte\-|longcos|pantech|gionee|^sie\-|portalmmm|";

         $regex_match.="jig\s browser|hiptop|^ucweb|^benq|haier|^lct|opera\s*mobi|opera\*mini|320x320|240x320|176x220";

         $regex_match.=")/i";

         return isset($_SERVER['HTTP_X_WAP_PROFILE']) or isset($_SERVER['HTTP_PROFILE']) or preg_match($regex_match, strtolower($_SERVER['HTTP_USER_AGENT'])); //如果UA中存在上面的关键词则返回真。
    }
if($agent){
    $client = 'WAP';
}
else{
    $client = 'YIP';
}
$parameter = array(
"merchantNo" => $alipay_config['merchantNo'],
"storeNo" => $alipay_config['storeNo'],
"vendor" => $paytype,
'currency' => 'CNY',
'settleCurrency' => 'USD',
"ipnUrl"	=> 'https://api.star-horizon.net/gateway/8994b91d-a7b82852f826/yuansfer/notify/'.TRADE_NO.'/',
"callbackUrl"	=> 'https://api.star-horizon.net/gateway/8994b91d-a7b82852f826/yuansfer/return/'.TRADE_NO.'/',
'terminal' => $client,
'reference' => $trade_no,
'description' => $trade_no,
'note' => $trade_no,
"amount"	=> (float)$order['realmoney']
);
//建立请求
$alipaySubmit = new AlipaySubmit($alipay_config);
$html_text = $alipaySubmit->get_cashierUrl($parameter);
$base64_url = base64_encode($html_text);
header('Location: https://api.star-horizon.net/api/cashier/yuansfer.php?cashierUrl='.base64_encode($html_text));