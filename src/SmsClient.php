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
     * @param array  $merchantData  商戶驗證數據
     * @param int    $smsType       短信類型 (默認為 0)
     * @return array                返回結果數組
     */
    public function sendSms(string $content, string $countryCode, string $mobile, array $merchantData, int $smsType = 0): array
    {
        return $this->postRequest('/send', [
            'content' => $content,
            'country_code' => $countryCode,
            'mobile' => $mobile,
            'sms_type' => $smsType,
        ], $merchantData['auth_key'], $merchantData['auth_token']);
    }

    /**
     * 查詢餘額
     *
     * @param array $merchantData 商戶驗證數據
     * @return array              返回結果數組
     */
    public function getBalance(array $merchantData): array
    {
        return $this->postRequest('/balance', [], $merchantData['auth_key'], $merchantData['auth_token']);
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
        ], $merchantData['auth_key'], $merchantData['auth_token']);
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
        ], $merchantData['auth_key'], $merchantData['auth_token']);
    }

    /**
     * 發送 POST 請求到指定的端點，並將提供的資料作為請求內容。
     *
     * @param string $endpoint API 端點，將請求發送到此端點。
     * @param array  $data     請求的資料，將會在請求主體中發送。
     * @param string $auth_key 基本驗證所需的認證金鑰。
     * @param string $auth_token 基本驗證所需的認證令牌。
     *
     * @return array API 回應，將以關聯陣列的形式返回。
     *
     * @throws \Exception 如果發生 cURL 錯誤或 API 請求失敗，將拋出例外。
     */
    protected function postRequest(
        string $endpoint,
        array $data,
        string $auth_key,
        string $auth_token
    ): array {
        // 將 auth_key 和 auth_token 編碼為 Base64 格式
        $credentials = base64_encode($auth_key . ':' . $auth_token);

        $ch = curl_init($this->apiUrl . $endpoint);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/x-www-form-urlencoded',
            'Authorization: Basic ' . $credentials, // 添加 Authorization 標頭
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if ($response === false) {
            throw new \Exception('cURL 錯誤: ' . curl_error($ch));
        }

        curl_close($ch);

        $result = json_decode($response, true);

        if ($httpCode !== 200 || !$result || !isset($result['success'])) {
            throw new \Exception('API 請求失敗: ' . ($result['error_msg'] ?? '未知錯誤'));
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
    protected function getRequest(string $endpoint, array $params = [], string $auth_key, string $auth_token): array
    {
        // 編碼認證資訊
        $credentials = base64_encode($auth_key . ':' . $auth_token);

        // 拼接完整 URL
        $url = $this->apiUrl . $endpoint . '?' . http_build_query($params);

        // 初始化 cURL
        $ch = curl_init($url);

        // 設置 cURL 選項
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/x-www-form-urlencoded',
            'Authorization: Basic ' . $credentials, // 添加 Authorization 標頭
        ]);

        // 執行請求
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if ($response === false) {
            throw new \Exception('cURL error: ' . curl_error($ch));
        }

        curl_close($ch);

        // 解析返回數據
        $result = json_decode($response, true);

        // 驗證回應
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

    /**
     * 創建 SMS 驗證記錄
     *
     * @param array $data 短信驗證數據
     * @param array $merchantData 商戶驗證數據
     * @return array 返回結果數組
     */
    public function createSmsVerify(array $data, array $merchantData): array
    {
        return $this->postRequest('/sms-verify', $data, $merchantData['auth_key'], $merchantData['auth_token']);
    }

    /**
     * 獲取 SMS 驗證記錄
     *
     * @param int $id 短信驗證記錄的 ID
     * @param array $merchantData 商戶驗證數據
     * @return array 返回結果數組
     */
    public function readSmsVerify(int $id, array $merchantData): array
    {
        return $this->getRequest("/sms-verify/{$id}", [], $merchantData['auth_key'], $merchantData['auth_token']);
    }

    /**
     * 更新 SMS 驗證記錄
     *
     * @param int $id 短信驗證記錄的 ID
     * @param array $data 要更新的數據
     * @param array $merchantData 商戶驗證數據
     * @return array 返回結果數組
     */
    public function updateSmsVerify(array $data, array $merchantData): array
    {
        return $this->postRequest("/update-sms-verify", $data, $merchantData['auth_key'], $merchantData['auth_token']);
    }

    /**
     * 刪除 SMS 驗證記錄
     *
     * @param int $id 短信驗證記錄的 ID
     * @param array $merchantData 商戶驗證數據
     * @return array 返回結果數組
     */
    public function deleteSmsVerify(int $id, array $merchantData): array
    {
        return $this->postRequest("/sms-verify/{$id}", ['_method' => 'DELETE'], $merchantData['auth_key'], $merchantData['auth_token']);
    }

    /**
     * 發送短信紀錄
     *
     * @param string $content       短信內容
     * @param string $countryCode   國家代碼
     * @param string $mobile        手機號碼
     * @param array  $merchantData  商戶驗證數據
     * @param int    $smsType       短信類型 (默認為 0)
     * @return array                返回結果數組
     */
    public function createSmsSend(string $content, string $countryCode, string $mobile, array $merchantData, int $smsType = 0, int $merchantId, int $regionId, int $status = 1): array
    {
        // 構建請求資料
        $data = [
            'merchant_id' => $merchantId, // Add merchant ID
            'region_id' => $regionId, // Add region ID
            'mobile' => $mobile, // Mobile number
            'content' => $content, // SMS content
            'status' => $status, // Status (default to 1)
            'sms_type' => $smsType, // SMS type (default to 0)
        ];

        // 調用發送請求
        return $this->postRequest('/sms-send', $data, $merchantData['auth_key'], $merchantData['auth_token']);
    }

    /**
     * Send a PUT request to the specified API endpoint.
     *
     * @param string $endpoint The API endpoint to send the request to.
     * @param array $data The data to be sent in the request body.
     * @param string $auth_key The authentication key for the API.
     * @param string $auth_token The authentication token for the API.
     *
     * @return array The response from the API, decoded from JSON.
     *
     * @throws \Exception If the cURL request fails or the API response indicates an error.
     */
    protected function putRequest(
        string $endpoint,
        array $data,
        string $auth_key,
        string $auth_token
    ): array {
        // Encode auth_key and auth_token to Base64 format
        $credentials = base64_encode($auth_key . ':' . $auth_token);

        // Initialize cURL
        $ch = curl_init($this->apiUrl . $endpoint);

        // Set cURL options
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT'); // Use PUT request method
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data)); // Send the data
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/x-www-form-urlencoded', // Set content type
            'Authorization: Basic ' . $credentials, // Add Authorization header
        ]);

        // Execute cURL request
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE); // Get HTTP status code

        // Error handling
        if ($response === false) {
            throw new \Exception('cURL error: ' . curl_error($ch));
        }

        // Close cURL connection
        curl_close($ch);

        // Decode JSON response
        $result = json_decode($response, true);

        // Check HTTP status code and response result
        if ($httpCode !== 200 || !$result || !isset($result['success'])) {
            throw new \Exception('API request failed: ' . ($result['error_msg'] ?? 'Unknown error'));
        }

        // Return processed result
        return $result;
    }
}
