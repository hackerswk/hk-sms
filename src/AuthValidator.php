<?php
/**
 * Auth validator class
 *
 * @author      Stanley Sie <swookon@gmail.com>
 * @access      public
 * @version     Release: 1.0
 */

namespace Stanleysie\HkSms;

class AuthValidator
{
    private $validKeys = [];
    private $validTokens = [];

    /**
     * Set valid authentication keys.
     *
     * @param array $keys List of valid authentication keys
     * @return void
     */
    public function setValidKeys(array $keys): void
    {
        $this->validKeys = $keys;
    }

    /**
     * Set valid authentication tokens.
     *
     * @param array $tokens List of valid authentication tokens
     * @return void
     */
    public function setValidTokens(array $tokens): void
    {
        $this->validTokens = $tokens;
    }

    /**
     * Validate if the authentication key is valid.
     *
     * @param string $key Authentication key
     * @return bool Returns true if the key is valid, otherwise false
     */
    public function validateKey(string $key): bool
    {
        return in_array($key, $this->validKeys);
    }

    /**
     * Validate if the authentication token is valid.
     *
     * @param string $token Authentication token
     * @return bool Returns true if the token is valid, otherwise false
     */
    public function validateToken(string $token): bool
    {
        return in_array($token, $this->validTokens);
    }

    /**
     * Validate if the authentication key and token are valid.
     *
     * @param string $key Authentication key
     * @param string $token Authentication token
     * @return bool Returns true if both key and token are valid, otherwise false
     */
    public function validateAuth(string $key, string $token): bool
    {
        return $this->validateKey($key) && $this->validateToken($token);
    }
}
