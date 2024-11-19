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
     * @param \PDO $pdo Database connection instance.
     * @param SmsClient $smsClient Instance of SmsClient for SMS operations.
     */
    public function __construct(\PDO $pdo)
    {
        // Initialize SmsVerify with PDO instance
        $this->smsVerify = new SmsVerify($pdo);
        $this->smsClient = new SmsClient();
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
    public function executeSms(
        string $countryCode,
        string $mobile,
        int $merchantId,
        int $regionId,
        array $merchantData,
        int $smsType = 0,
        ?string $content = null,
        int $expiryMinutes = 10
    ): array {
        // Step 1: Generate a 7-digit verification code
        $verificationCode = $this->smsVerify->generateVerificationCode();

        // Step 2: Send the SMS content
        $smsContent = $content ?? "Your verification code is: " . $verificationCode;

        // Step 3: Call smsSend method of SmsClient to send SMS
        $status = 0; // 0 for unsent, 1 for sent
        $smsSendResponse = $this->smsClient->createSmsSend(
            $smsContent,
            $countryCode,
            $mobile,
            $merchantData,
            $smsType,
            $merchantId,
            $regionId,
            $status
        );

        // Step 4: Check if the SMS was successfully sent
        if ($smsSendResponse['success']) {
            $smsSendId = $smsSendResponse['sms_send_id']; // Assuming response contains sms_send_id
        } else {
            throw new \Exception('Failed to send SMS');
        }

        // Step 5: Insert the verification record in sms_verify with the custom expiry time
        $verifyStatus = 0; // 0 indicates unverified
        // Assuming $smsSendResponse contains verification ID or other necessary info, otherwise adjust logic
        $data = [
            'sms_send_id' => $smsSendId,
            'verification_code' => $verificationCode,
            'verify_status' => $verifyStatus,
        ];
        $insertId = $this->smsClient->createSmsVerify($data, $merchantData);

        return [
            'sms_send_id' => $smsSendId,
            'insertId' => $insertId,
            'verification_code' => $verificationCode,
            'status' => 'pending',
            'expiry' => $expiryMinutes,
        ];
    }
}
