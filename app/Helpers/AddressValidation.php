<?php

namespace App\Helpers;

class AddressValidation
{
    /**
     * Address validation constants
     */
    public const MAX_LENGTH = 500;
    public const PATTERN = '/^[a-zA-Z0-9\s,.\-#]+$/';
    public const ALLOWED_SYMBOLS = ',.-#';
    
    /**
     * Address validation rules for Laravel validation
     */
    public const VALIDATION_RULES = [
        'required',
        'string',
        'max:' . self::MAX_LENGTH,
        'regex:' . self::PATTERN
    ];
    
    /**
     * Address validation messages
     */
    public const VALIDATION_MESSAGES = [
        'max' => 'Address must not exceed ' . self::MAX_LENGTH . ' characters.',
        'regex' => 'Address can only contain letters, numbers, spaces, and common symbols (' . self::ALLOWED_SYMBOLS . ').',
    ];
    
    /**
     * Sanitize address input by removing dangerous characters
     *
     * @param string $address
     * @return string
     */
    public static function sanitize(string $address): string
    {
        return preg_replace('/[<>=\'"]/', '', $address);
    }
    
    /**
     * Validate address format
     *
     * @param string $address
     * @return bool
     */
    public static function isValid(string $address): bool
    {
        if (strlen($address) > self::MAX_LENGTH) {
            return false;
        }
        
        return (bool) preg_match(self::PATTERN, $address);
    }
    
    /**
     * Get validation rules array for Laravel validation
     *
     * @return array
     */
    public static function getValidationRules(): array
    {
        return self::VALIDATION_RULES;
    }
    
    /**
     * Get validation messages array for Laravel validation
     *
     * @return array
     */
    public static function getValidationMessages(): array
    {
        return self::VALIDATION_MESSAGES;
    }
}

