<?php
/**
 * SMS Verify class
 *
 * @author      Stanley Sie <swookon@gmail.com>
 * @access      public
 * @version     Release: 1.0
 */

namespace Stanleysie\HkSms;

use \PDO;
use \PDOException;

class SmsVerify
{
    /**
     * Database connection
     *
     * @var PDO
     */
    private $database;

    /**
     * Initialize the SmsVerify class with a PDO instance
     *
     * @param PDO $db PDO instance for database connection
     */
    public function __construct(PDO $db)
    {
        $this->database = $db;
    }

    /**
     * Inserts a new record into the sms_verify table with the provided details.
     *
     * @param int    $smsSendId         The ID associated with the SMS send.
     * @param string $verificationCode   The verification code to store.
     * @param int    $verifyStatus       The verification status (e.g., 0 or 1).
     * @param int    $expiryMinutes      The number of minutes after which the code expires. Default is 10.
     *
     * @return int                       The last inserted ID.
     *
     * @throws PDOException              If a database error occurs during insertion.
     */
    public function insert($smsSendId, $verificationCode, $verifyStatus, $expiryMinutes = 10)
    {
        // 計算到期時間
        $expiredAt = date('Y-m-d H:i:s', strtotime("+$expiryMinutes minutes"));

        $sql = "INSERT INTO sms_verify (sms_send_id, verification_code, verify_status, expired_at)
            VALUES (:sms_send_id, :verification_code, :verify_status, :expired_at)";
        try {
            $stmt = $this->database->prepare($sql);
            $stmt->execute([
                ':sms_send_id' => $smsSendId,
                ':verification_code' => $verificationCode,
                ':verify_status' => $verifyStatus,
                ':expired_at' => $expiredAt,
            ]);
            return $this->database->lastInsertId();
        } catch (PDOException $e) {
            throw new PDOException("Database insert error: " . $e->getMessage());
        }
    }

    /**
     * Get a verification record by ID
     *
     * @param int $id ID of the verification record
     * @return array|null The verification record or null if not found
     * @throws PDOException If there is an error with the database query
     */
    public function getById($id)
    {
        $sql = "SELECT * FROM sms_verify WHERE id = :id";
        try {
            $stmt = $this->database->prepare($sql);
            $stmt->execute([':id' => $id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new PDOException("Database query error: " . $e->getMessage());
        }
    }

    /**
     * Update a verification record
     *
     * @param int $id ID of the verification record
     * @param array $data Associative array of fields to update (e.g., ['verify_status' => 1])
     * @return int Number of affected rows
     * @throws PDOException If there is an error with the database query
     */
    public function update($id, array $data)
    {
        // 強制將 id 轉換為整數
        $id = (int) $id;
        $setClause = [];

        foreach ($data as $key => $value) {
            $setClause[] = "$key = :$key";
            $params[":$key"] = $value;
        }
        $params = [':id' => $id];
        $sql = "UPDATE sms_verify SET " . implode(', ', $setClause) . " WHERE id = :id";

        // 打印出 SQL 語句和參數
        echo $sql . "\n";
        var_dump($params);
        try {
            $stmt = $this->database->prepare($sql);
            $stmt->execute($params);
            return $stmt->rowCount();
        } catch (PDOException $e) {
            throw new PDOException("Database update error: " . $e->getMessage());
        }
    }

    /**
     * Delete a verification record by ID
     *
     * @param int $id ID of the verification record
     * @return int Number of affected rows
     * @throws PDOException If there is an error with the database query
     */
    public function delete($id)
    {
        $sql = "DELETE FROM sms_verify WHERE id = :id";
        try {
            $stmt = $this->database->prepare($sql);
            $stmt->execute([':id' => $id]);
            return $stmt->rowCount();
        } catch (PDOException $e) {
            throw new PDOException("Database delete error: " . $e->getMessage());
        }
    }

    /**
     * Generate a 7-digit random verification code
     *
     * @return string 7-digit verification code
     */
    public function generateVerificationCode()
    {
        // Generate a random number between 1000000 and 9999999 (7 digits)
        return str_pad(random_int(0, 9999999), 7, '0', STR_PAD_LEFT);
    }

    /**
     * Selects the first region associated with a merchant.
     *
     * @param int $merchantId The merchant ID.
     * @return array|null     The first merchant region or null if not found.
     * @throws PDOException   If there is a database error.
     */
    public function selectMerchantRegion(int $merchantId): ?array
    {
        $sql = "SELECT * FROM merchant_region WHERE merchant_id = :merchant_id LIMIT 1";
        try {
            $stmt = $this->database->prepare($sql);
            $stmt->execute([':merchant_id' => $merchantId]);
            return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
        } catch (PDOException $e) {
            throw new PDOException("Database query error: " . $e->getMessage());
        }
    }

    /**
     * Inserts a new record into the sms_send table.
     *
     * @param int    $merchantId   The merchant ID.
     * @param int    $regionId     The region ID.
     * @param string $mobile       The mobile number to send the SMS.
     * @param string $content      The content of the SMS.
     * @param int    $status       The status of the SMS (e.g., 0 for unsent, 1 for sent).
     * @param int    $smsType      The SMS type (default is 0).
     * @return int                 The last inserted ID.
     * @throws PDOException        If a database error occurs during insertion.
     */
    public function insertSmsSend(int $merchantId, int $regionId, string $mobile, string $content, int $status, int $smsType = 0): int
    {
        $sql = "INSERT INTO sms_send (merchant_id, region_id, mobile, content, status, sms_type, sent_time)
                VALUES (:merchant_id, :region_id, :mobile, :content, :status, :sms_type, CURRENT_TIMESTAMP)";

        try {
            $stmt = $this->database->prepare($sql);
            $stmt->execute([
                ':merchant_id' => $merchantId,
                ':region_id' => $regionId,
                ':mobile' => $mobile,
                ':content' => $content,
                ':status' => $status,
                ':sms_type' => $smsType,
            ]);
            return $this->database->lastInsertId();
        } catch (PDOException $e) {
            throw new PDOException("Database insert error: " . $e->getMessage());
        }
    }

    /**
     * Selects a merchant by its name.
     *
     * @param string $merchantName The name of the merchant.
     * @return array|null          The merchant record or null if not found.
     * @throws PDOException        If there is a database error.
     */
    public function selectMerchantByName(string $merchantName): ?array
    {
        $sql = "SELECT * FROM merchant WHERE merchant_name = :merchant_name LIMIT 1";
        try {
            $stmt = $this->database->prepare($sql);
            $stmt->execute([':merchant_name' => $merchantName]);
            return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
        } catch (PDOException $e) {
            throw new PDOException("Database query error: " . $e->getMessage());
        }
    }

    /**
     * Selects the first region by its region code.
     *
     * @param int $regionCode The code of the region.
     * @return array|null     The region record or null if not found.
     * @throws PDOException   If there is a database error.
     */
    public function selectFirstRegionByCode(int $regionCode): ?array
    {
        $sql = "SELECT * FROM regions WHERE region_code = :region_code LIMIT 1";
        try {
            $stmt = $this->database->prepare($sql);
            $stmt->execute([':region_code' => $regionCode]);
            return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
        } catch (PDOException $e) {
            throw new PDOException("Database query error: " . $e->getMessage());
        }
    }

    /**
     * Retrieves all region codes from the regions table.
     *
     * @return array List of region codes.
     * @throws PDOException If there is a database error.
     */
    public function getAllRegionCodes(): array
    {
        $sql = "SELECT region_code FROM regions";
        try {
            $stmt = $this->database->prepare($sql);
            $stmt->execute();
            // Fetch all region codes as a simple array
            return $stmt->fetchAll(PDO::FETCH_COLUMN);
        } catch (PDOException $e) {
            throw new PDOException("Database query error: " . $e->getMessage());
        }
    }
}
