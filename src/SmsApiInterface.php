<?php
/**
 * Sms API Interface
 * Designed to allow different departments to use different API keys for SMS API calls
 *
 * @author      Stanley Sie <swookon@gmail.com>
 * @access      public
 * @version     Release: 1.0
 */

namespace Stanleysie\HkSms;

class SmsApiInterface
{
    /**
     * Send SMS using specified API key and other parameters.
     *
     * @param string $key       API account key
     * @param string $secret    API account secret
     * @param string $from      Sender ID
     * @param string $to        Destination number
     * @param string $text      Message content
     * @return array            Response data
     */
    public static function sendSms($key, $secret, $from, $to, $text)
    {
        return SmsApi::getSendSms($key, $secret, $from, $to, $text);
    }

    /**
     * Get send record using specified API key and other parameters.
     *
     * @param string $key          API account key
     * @param string $secret       API account secret
     * @param string $messageid    Message ID
     * @return array               Response data
     */
    public static function getSendRecord($key, $secret, $messageid)
    {
        return SmsApi::getSendRecord($key, $secret, $messageid);
    }

    /**
     * Get user balance using specified API key and other parameters.
     *
     * @param string $key       API account key
     * @param string $secret    API account secret
     * @return array            Response data
     */
    public static function getUserBalance($key, $secret)
    {
        return SmsApi::getUserBalance($key, $secret);
    }

    /**
     * Get valid number using specified API key and other parameters.
     *
     * @param string $key             API account key
     * @param string $secret          API account secret
     * @param string $countryCode     Country code
     * @param string $nationalNumber  National number
     * @return array                  Response data
     */
    public static function getValidNumber($key, $secret, $countryCode, $nationalNumber)
    {
        return SmsApi::getValidNumber($key, $secret, $countryCode, $nationalNumber);
    }

    /**
     * Add invalid phone number to blacklist using parameters.
     *
     * @param string $countryCode     Country code
     * @param string $nationalNumber  National number
     * @return bool                   True if the number was successfully added to the blacklist, otherwise false
     */
    public static function addToBlacklist($countryCode, $nationalNumber)
    {
    }

    /**
     * Check if phone number is in the blacklist using parameters.
     *
     * @param string $countryCode     Country code
     * @param string $nationalNumber  National number
     * @return bool                   True if the number was successfully added to the blacklist, otherwise false
     */
    public static function isInBlacklist($countryCode, $nationalNumber)
    {
    }
}
?>
