<?php

namespace Stanleysie\HkSms;

class OtpApi
{
    /**
     * Database connection instance
     *
     * @var SmsVerify
     */
    private $smsVerify;

    /**
     * SMS client instance for sending messages
     *
     * @var SmsClient
     */
    private $smsClient;

    /**
     * Constructor to initialize dependencies.
     *
     * @param SmsVerify $smsVerify Instance of SmsVerify for verification operations.
     * @param SmsClient $smsClient Instance of SmsClient for SMS operations.
     */
    public function __construct(SmsVerify $smsVerify, SmsClient $smsClient)
    {
        $this->smsVerify = $smsVerify;
        $this->smsClient = $smsClient;
    }

    /**
     * Executes the SMS OTP process by generating a verification code, sending it via SMS,
     * and storing it in the database.
     *
     * @param string $countryCode  Country code for the recipient's mobile number.
     * @param string $mobile       Mobile number of the recipient.
     * @param int    $merchantId   ID of the merchant requesting OTP.
     * @param int    $regionId     Region ID to associate with the SMS send record.
     * @param int    $smsType      Type of SMS, default is 0.
     * @param string|null $content Custom content for the SMS message.
     * @param int    $expiryMinutes The number of minutes after which the code expires. Default is 10.
     * @return array               Array containing the send result and verification data.
     * @throws \Exception          If there is an error during the process.
     */
    public function executeSms(string $countryCode, string $mobile, int $merchantId, int $regionId, int $smsType = 0, ?string $content = null, int $expiryMinutes = 10): array
    {
        // Step 1: Generate a 7-digit verification code
        $verificationCode = $this->smsVerify->generateVerificationCode();

        // Step 2: Send the SMS content
        $smsContent = $content ?? "Your verification code is: " . $verificationCode;

        // Step 3: Insert a new record in sms_send
        $status = 0; // 0 for unsent, 1 for sent
        $smsSendId = $this->smsVerify->insertSmsSend($merchantId, $regionId, $mobile, $smsContent, $status, $smsType);

        // Step 4: Insert the verification record in sms_verify with the custom expiry time
        $verifyStatus = 0; // 0 indicates unverified
        $insertId = $this->smsVerify->insert($smsSendId, $verificationCode, $verifyStatus, $expiryMinutes);

        return [
            'sms_send_id' => $smsSendId,
            'verification_code' => $verificationCode,
            'status' => 'pending',
            'expiry' => $expiryMinutes,
        ];
    }
}
