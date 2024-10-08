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
     * Insert a new SMS verification record
     *
     * @param int $smsSendId ID of the related SMS send record
     * @param string $verificationCode 7-digit verification code
     * @param int $verifyStatus Verification status (0: not verified, 1: success, 2: failed)
     * @return int Last inserted ID
     * @throws PDOException If there is an error with the database query
     */
    public function insert($smsSendId, $verificationCode, $verifyStatus)
    {
        $sql = "INSERT INTO sms_verify (sms_send_id, verification_code, verify_status)
                VALUES (:sms_send_id, :verification_code, :verify_status)";
        try {
            $stmt = $this->database->prepare($sql);
            $stmt->execute([
                ':sms_send_id' => $smsSendId,
                ':verification_code' => $verificationCode,
                ':verify_status' => $verifyStatus,
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
        $setClause = [];
        $params = [':id' => $id];
        foreach ($data as $key => $value) {
            $setClause[] = "$key = :$key";
            $params[":$key"] = $value;
        }
        $sql = "UPDATE sms_verify SET " . implode(', ', $setClause) . " WHERE id = :id";
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
}
