<?php
/* *
 * 类名：EpaySubmit
 * 功能：彩虹易支付接口请求提交类
 * 详细：构造易支付接口表单HTML文本，获取远程HTTP数据
 */

require_once(PAY_ROOT."inc/yuansfer_core.function.php");
require_once(PAY_ROOT."inc/yuansfer_md5.function.php");

class AlipaySubmit {

	var $alipay_config;

	function __construct($alipay_config){
		$this->alipay_config = $alipay_config;
		$this->alipay_gateway_new = $this->alipay_config['apiurl'].'submit.php';
		$this->alipay_qrcode = $this->alipay_config['apiurl'].'qrcode.php?';
	}
    function AlipaySubmit($alipay_config) {
    	$this->__construct($alipay_config);
    }
	
	/**
	 * 生成签名结果
	 * @param $para_sort 已排序要签名的数组
	 * return 签名结果字符串
	 */
	function buildRequestMysign($para_sort) {
		//把数组所有元素，按照“参数=参数值”的模式用“&”字符拼接成字符串
		$prestr = createLinkstring($para_sort);
		
		$mysign = md5($prestr . md5($this->alipay_config['key']));

		return $mysign;
	}

	/**
     * 生成要请求给支付宝的参数数组
     * @param $para_temp 请求前的参数数组
     * @return 要请求的参数数组
     */
	function buildRequestPara($para_temp) {
		//除去待签名参数数组中的空值和签名参数
		$para_filter = paraFilter($para_temp);

		//对待签名参数数组排序
		$para_sort = argSort($para_filter);

		//生成签名结果
		$mysign = $this->buildRequestMysign($para_sort);
		
		//签名结果与签名方式加入请求提交参数组中
		$para_sort['verifySign'] = $mysign;
		
		return $para_sort;
	}

	/**
     * 生成要请求给支付宝的参数数组
     * @param $para_temp 请求前的参数数组
     * @return 要请求的参数数组字符串
     */
	function buildRequestParaToString($para_temp) {
		//待请求参数数组
		$para = $this->buildRequestPara($para_temp);
		
		//把参数组中所有元素，按照“参数=参数值”的模式用“&”字符拼接成字符串，并对字符串做urlencode编码
		$request_data = createLinkstringUrlencode($para);
		
		return $request_data;
	}
	
    /**
     * 建立请求，以表单HTML形式构造（默认）
     * @param $para_temp 请求参数数组
     * @param $method 提交方式。两个值可选：post、get
     * @param $button_name 确认按钮显示文字
     * @return 提交表单HTML文本
     */
	function get_cashierUrl($params, $method='POST', $button_name='正在跳转') {
		//待请求参数数组
		$url = 'https://mapi.yuansfer.com/online/v3/secure-pay';
        ksort($params, SORT_STRING);
        $str = '';
        foreach ($params as $k => $v) {
            $str .= $k . '=' . $v . '&';
        }
        $params['verifySign'] = md5($str . md5($this->alipay_config['key']));
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
        $cashier_url = $final_result['cashierUrl'];
        return $cashier_url;
	}
    /**
     * 建立请求，以表单HTML形式构造（默认）
     * @param $para_temp 请求参数数组
     * @param $method 提交方式。两个值可选：post、get
     * @param $button_name 确认按钮显示文字
     * @return 提交表单HTML文本
     */
	function get_qrcodeUrl($params, $method='POST', $button_name='正在跳转') {
		//待请求参数数组
		$url = 'https://mapi.yuansfer.com/app-instore/v3/create-trans-qrcode';
        ksort($params, SORT_STRING);
        $str = '';
        foreach ($params as $k => $v) {
            $str .= $k . '=' . $v . '&';
        }
        $params['verifySign'] = md5($str . md5($this->alipay_config['key']));
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
        $qrcode_url = $final_result['deepLink'];
        return $qrcode_url;
	}

	/**
     * 建立请求，以URL形式构造
     * @param $para_temp 请求参数数组
     * @return 提交的URL链接
     */
	function buildRequestUrl($para_temp) {		
		//待请求参数数组字符串
		$request_data = $this->buildRequestPara($para_temp);

		//远程获取数据
		$url = $this->alipay_qrcode.http_build_query($request_data);

		return $url;
	}
}
?>