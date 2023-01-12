<?php 

if(!defined('IN_PLUGIN'))exit();

require_once(PAY_ROOT."inc/yuansfer.config.php");
$url = 'https://mapi.yuansfer.com/app-data-search/v3/tran-query';
        $params = array(
            'merchantNo' => $alipay_config['merchantNo'],
            'storeNo' => $alipay_config['storeNo'],
            'reference' => TRADE_NO
        );
        ksort($params, SORT_STRING);
        $str = '';
        foreach ($params as $k => $v) {
            $str .= $k . '=' . $v . '&';
        }
        $params['verifySign'] = md5($str . md5($alipay_config['key']));
        $ch = curl_init($url);
        curl_setopt_array($ch, array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER => false,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => http_build_query($params),
        ));
        $result = curl_exec($ch);
        curl_exec($ch);
        $result_json = json_decode($result, true);
        $final_result = $result_json['result'];
        if($result_json['ret_code'] == "000100")
        {
            if($final_result['status'] == "success")
            {
                $trade_no = $final_result['transactionNo'];
                $url=creat_callback($order);
		if($order['status']==0){
			if($DB->exec("update `pre_order` set `status` ='1' where `trade_no`='".TRADE_NO."'")){
					$DB->exec("update `pre_order` set `api_trade_no` ='$trade_no',`endtime` ='$date',`buyer` ='$buyer_id',`date` =NOW() where `trade_no`='".TRADE_NO."'");
					processOrder($order,false);
				}
		}
		returnTemplate($url['return']);
            }
            else
            {
                echo '订单信息校验失败';
            }
        }
        else {
    //验证失败
	echo('验证失败！');
}

?>