<?php
/**
 * Sms client API class
 *
 * @author      Stanley Sie <swookon@gmail.com>
 * @access      public
 * @version     Release: 1.0
 */

namespace Stanleysie\HkSms;

class SmsClient
{
    protected $apiUrl = 'https://sms.holkee.com';

    /**
     * 發送短信
     *
     * @param string $content       短信內容
     * @param string $countryCode   國家代碼
     * @param string $mobile        手機號碼
     * @param int    $smsType       短信類型 (默認為 0)
     * @return array                返回結果數組
     */
    public function sendSms(string $content, string $countryCode, string $mobile, int $smsType = 0): array
    {
        return $this->postRequest('/send', [
            'content' => $content,
            'country_code' => $countryCode,
            'mobile' => $mobile,
            'sms_type' => $smsType,
        ]);
    }

    /**
     * 查詢餘額
     *
     * @param array $merchantData 商戶驗證數據
     * @return array              返回結果數組
     */
    public function getBalance(array $merchantData): array
    {
        return $this->postRequest('/balance', [
            'merchant_id' => $merchantData['merchant_id'],
            'api_token' => $merchantData['api_token'],
        ]);
    }

    /**
     * 查詢短信狀態
     *
     * @param string $messageId    短信 ID
     * @param array  $merchantData 商戶驗證數據
     * @return array               返回結果數組
     */
    public function getStatus(string $messageId, array $merchantData): array
    {
        return $this->postRequest('/status', [
            'message_id' => $messageId,
            'merchant_id' => $merchantData['merchant_id'],
            'api_token' => $merchantData['api_token'],
        ]);
    }

    /**
     * 檢查手機號碼
     *
     * @param string $mobile       手機號碼
     * @param string $countryCode  國碼
     * @param array  $merchantData 商戶驗證數據
     * @return array               返回結果數組
     */
    public function checkMobile(string $mobile, string $countryCode, array $merchantData): array
    {
        return $this->postRequest('/check_mobile', [
            'mobile' => $mobile,
            'country_code' => $countryCode,
            'merchant_id' => $merchantData['merchant_id'],
            'api_token' => $merchantData['api_token'],
        ]);
    }

    /**
     * 發送 POST 請求至指定 API
     *
     * @param string $endpoint     API 路徑
     * @param array  $data         發送的 POST 數據
     * @return array               API 返回結果
     * @throws \Exception
     */
    protected function postRequest(string $endpoint, array $data): array
    {
        $ch = curl_init($this->apiUrl . $endpoint);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/x-www-form-urlencoded',
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if ($response === false) {
            throw new \Exception('cURL error: ' . curl_error($ch));
        }

        curl_close($ch);

        $result = json_decode($response, true);

        if ($httpCode !== 200 || !$result || !isset($result['success'])) {
            throw new \Exception('API request failed: ' . ($result['message'] ?? 'Unknown error'));
        }

        return $result;
    }

    /**
     * 發送 GET 請求至指定 API
     *
     * @param string $endpoint     API 路徑
     * @param array  $params       發送的 GET 參數
     * @return array               API 返回結果
     * @throws \Exception
     */
    protected function getRequest(string $endpoint, array $params = []): array
    {
        $url = $this->apiUrl . $endpoint . '?' . http_build_query($params);

        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/x-www-form-urlencoded',
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if ($response === false) {
            throw new \Exception('cURL error: ' . curl_error($ch));
        }

        curl_close($ch);

        $result = json_decode($response, true);

        if ($httpCode !== 200 || !$result || !isset($result['success'])) {
            throw new \Exception('API request failed: ' . ($result['message'] ?? 'Unknown error'));
        }

        return $result;
    }

    /**
     * 執行 Cron 任務
     *
     * @param array $params 附加的查詢參數（如果需要）
     * @return array       返回結果數組
     * @throws \Exception
     */
    public function executeCron(array $params = []): array
    {
        return $this->getRequest('/cron', $params);
    }
}
