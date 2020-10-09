<?php

namespace YIVDEV\UTILS;

/**
 * Utils class to handle the different useful operations
 */
class Utils
{
    /**
     * slugify function
     *
     * @param string $text
     * @return string
     */
    public static function slugify(string $text): string
    {
        try {
            // lowercase
            $text = strtolower($text);

            // replace non letter or digits by -
            $text = preg_replace('~[^\pL\d]+~u', '-', $text);

            // transliterate
            $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

            // remove unwanted characters
            $text = preg_replace('~[^-\w]+~', '', $text);

            // trim
            $text = trim($text, '-');

            // remove duplicate -
            $text = preg_replace('~-+~', '-', $text);

            if (empty($text)) {
                return 'n-a';
            }

            return $text;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * encode function
     *
     * @param string $content
     * @param string $cipher
     * @param string $key
     * @return string
     */
    public static function encode(string $content, string $cipher, string $key): string
    {
        try {
            $ivlen = openssl_cipher_iv_length($cipher);
            $iv = openssl_random_pseudo_bytes($ivlen);
            $ciphertext_raw = openssl_encrypt($content, $cipher, $key, $options = OPENSSL_RAW_DATA, $iv);
            $hmac = hash_hmac('sha256', $ciphertext_raw, $key, $as_binary = true);
            $result_content = base64_encode($iv . $hmac . $ciphertext_raw);

            return $result_content;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * decode function
     *
     * @param string $encoded_content
     * @param string $cipher
     * @param string $key
     * @return string|\Exception
     */
    public static function decode(string $encoded_content, string $cipher, string $key)
    {
        try {
            $encoded = base64_decode($encoded_content);
            $ivlen = openssl_cipher_iv_length($cipher);
            $iv = substr($encoded, 0, $ivlen);
            $hmac = substr($encoded, $ivlen, $sha2len = 32);
            $ciphertext_raw = substr($encoded, $ivlen + $sha2len);
            $original_plaintext = openssl_decrypt($ciphertext_raw, $cipher, $key, $options = OPENSSL_RAW_DATA, $iv);
            $calcmac = hash_hmac('sha256', $ciphertext_raw, $key, $as_binary = true);
            if (hash_equals($hmac, $calcmac)) {
                $result_config = json_decode($original_plaintext);
                return $result_config;
            } else {
                throw new \Exception('CAN`T BE DECODED');
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
