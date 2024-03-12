<?php
/**
 * Sms API class
 *
 * @author      Stanley Sie <swookon@gmail.com>
 * @access      public
 * @version     Release: 1.0
 */

namespace Stanleysie\HkSms;

class SmsApi
{
    // 編碼格式。發送編碼格式統一用UTF-8
    const ENCODING = "UTF-8";
    // 簡訊發送url
    const URI_GET_SEND_SMS = "https://api.paasoo.com.tw//json";
    // 發送記錄查詢url
    const URI_GET_SEND_RECORD = "https://api.paasoo.com.tw//dlr";
    // 餘額查詢url
    const URI_GET_USER_BALANCE = "https://api.paasoo.com.tw//balance";
    // 號碼狀態查詢url
    const URI_GET_VALID_NUMBER = "https://api.paasoo.com.tw//lookup";

    /**
     * 簡訊發送
     *
     * @param key       API帳號
     * @param secret    API密碼
     * @param from      SenderID
     * @param to        發送目標號碼
     * @param text      發送內容
     * @return json     格式字串
     */
    public static function getSendSms($key, $secret, $from, $to, $text)
    {
        $text = urlencode($text);
        $url = self::URI_GET_SEND_SMS . "?key=" . $key . "&secret=" . $secret . "&from=" . $from . "&to=" . $to . "&text=" . $text;
        return self::geturl($url);
    }

    /**
     * 發送記錄查詢
     *
     * @param key        API帳號
     * @param secret     API密碼
     * @param messageid  發送記錄訊息ID
     * @return json      格式字串
     */
    public static function getSendRecord($key, $secret, $messageid)
    {
        $url = self::URI_GET_SEND_RECORD . "?key=" . $key . "&secret=" . $secret . "&messageid=" . $messageid;
        return self::geturl($url);
    }

    /**
     * 餘額查詢
     *
     * @param key        API帳號
     * @param secret     API密碼
     * @return json      格式字串
     */
    public static function getUserBalance($key, $secret)
    {
        $url = self::URI_GET_USER_BALANCE . "?key=" . $key . "&secret=" . $secret;
        return self::geturl($url);
    }

    /**
     * 號碼狀態查詢
     *
     * @param key             API帳號
     * @param secret          API密碼
     * @param countryCode     待驗證手機號碼所在國家區號
     * @param nationalNumber  待驗證的手機號碼，不含國家區號
     * @return json           格式字串
     */
    public static function getValidNumber($key, $secret, $countryCode, $nationalNumber)
    {
        $number = $countryCode . $nationalNumber;
        $url = self::URI_GET_VALID_NUMBER . "?key=" . $key . "&secret=" . $secret . "&number=" . $number;
        return self::geturl($url);
    }

    /**
     * Get url use curl.
     *
     * @param string $url     目標網址
     * @return json           格式字串
     */
    public static function geturl($url)
    {
        //$headerArray =array("Content-type:application/x-www-form-urlencoded;","");
        $ch = curl_init();
        //echo $url;
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        //curl_setopt($ch,CURLOPT_HTTPHEADER,$headerArray);
        $output = curl_exec($ch);
        curl_close($ch);
        //print_r($output);
        $output = json_decode($output,true);
        return $output;
    }
}
