<?php
/**
 * Auth generator class
 *
 * @author      Stanley Sie <swookon@gmail.com>
 * @access      public
 * @version     Release: 1.0
 */

namespace Stanleysie\HkSms;

class AuthGenerator
{
    /**
     * Generate a random authentication key.
     *
     * @param int $length Length of the key
     * @return string The generated authentication key
     */
    public function generateKey(int $length = 16): string
    {
        return bin2hex(random_bytes($length));
    }

    /**
     * Generate a random authentication token.
     *
     * @param int $length Length of the token
     * @return string The generated authentication token
     */
    public function generateToken(int $length = 32): string
    {
        return bin2hex(random_bytes($length));
    }
}
