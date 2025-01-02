<?php

namespace Localization;

use Services\Auth;

class Localization {
    private static array $locales = array();
    private static ?string $currentLocale = null;

    private function __construct() { } // Disable instantiation

    public function __clone() { } // Disable cloning

    public function __wakeup() { } // Disable de-serialization

    public static function setLocales(array $locales): void {
        self::$locales = $locales;
    }

    public static function addLocale(string $locale): void {
        if (in_array($locale, self::$locales)) return;
        self::$locales[] = $locale;
    }

    public static function getLocales(): array {
        return self::$locales;
    }

    public static function getDefaultLocale(): string {
        return self::$locales[0];
    }

    public static function localeExists(string $locale): bool {
        return in_array($locale, self::$locales, true);
    }

    /**
     * Determines the appropriate locale automatically from session or user.
     * @param string|null $requiredLocale Locale override (must be `null` for automatic locale detection).
     * @return string Detected locale.
     */
    public static function detectUserLocale(?string $requiredLocale = null): string {
        if (empty($requiredLocale)) {
            $requiredLocale = resolve(Auth::class)->getUserLocale();
            if (!empty($requiredLocale) && self::localeExists($requiredLocale)) {
                return $requiredLocale;
            }
            if (isset($_SESSION['locale']) && self::localeExists($_SESSION['locale'])) {
                return $_SESSION['locale'];
            }
            return self::getDefaultLocale();
        }
        return self::localeExists($requiredLocale) ? $requiredLocale : self::getDefaultLocale();
    }

    /**
     * Sets the current locale for system.
     * @param string|null $locale Locale to set (or `null` to determine locale automatically from session or user).
     * @return void
     */
    public static function setCurrentLocale(?string $locale = null): void {
        global $localizedMessages;
        $locale = self::detectUserLocale($locale);
        if (self::$currentLocale === null) {
            require_once(ROOT_DIR . 'localization' . DIRECTORY_SEPARATOR . $locale . DIRECTORY_SEPARATOR . 'messages.php');
            self::$currentLocale = $locale;
            $_SESSION['locale'] = $locale;
            resolve(Auth::class)->setUserLocale($locale);
            return;
        }
        if (self::$currentLocale != $locale) {
            unset($localizedMessages);
            require_once(ROOT_DIR . 'localization' . DIRECTORY_SEPARATOR . $locale . DIRECTORY_SEPARATOR . 'messages.php');
            self::$currentLocale = $locale;
            $_SESSION['locale'] = $locale;
            resolve(Auth::class)->setUserLocale($locale);
        }
    }

    public static function getCurrentLocale(): string {
        return self::$currentLocale;
    }

    /**
     * Localize a message.
     * @param string $messageKey Message key.
     * @param array|null $parameters Message parameters for interpolation.
     * @param string|null $locale Locale to use for localizing this message (or `null` if using currently set locale).
     * @return string
     */
    public static function translate(string $messageKey, ?array $parameters = null, ?string $locale = null): string {
        global $localizedMessages;

        if ($locale !== null) {
            // Switch locale to the given locale temporarily.
            $prevLocale = self::getCurrentLocale();
            self::setCurrentLocale($locale);
        }

        $message = $localizedMessages;
        $arrayKey = strtok($messageKey, '.');
        while ($arrayKey !== false) {
            $message = $message[$arrayKey];
            $arrayKey = strtok('.');
        }

        if (!isset($message) || !is_string($message)) return $messageKey;

        if ($parameters !== null) {
            foreach ($parameters as $key => $value) {
                $message = str_replace(':' . $key, $value ?? '', $message);
            }
        }

        if (isset($prevLocale)) {
            self::setCurrentLocale($prevLocale);
        }

        return $message;
    }
}