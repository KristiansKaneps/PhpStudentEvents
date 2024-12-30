<?php

namespace Localization;

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

    public static function setCurrentLocale(?string $locale): void {
        global $localizedMessages;
        $locale = $locale ?? (isset($_SESSION['locale']) && in_array($_SESSION['locale'], self::$locales, true) ? $_SESSION['locale'] : self::$locales[0]);
        if (self::$currentLocale === null) {
            require_once(ROOT_DIR . 'localization' . DIRECTORY_SEPARATOR . $locale . DIRECTORY_SEPARATOR . 'messages.php');
            self::$currentLocale = $locale;
            $_SESSION['locale'] = $locale;
            return;
        }
        if (self::$currentLocale != $locale) {
            unset($localizedMessages);
            require_once(ROOT_DIR . 'localization' . DIRECTORY_SEPARATOR . $locale . DIRECTORY_SEPARATOR . 'messages.php');
            self::$currentLocale = $locale;
            $_SESSION['locale'] = $locale;
        }
    }

    public static function getCurrentLocale(): string {
        return self::$currentLocale;
    }

    public static function translate(string $messageKey, ?array $parameters = null): string {
        global $localizedMessages;
        $message = $localizedMessages;
        $arrayKey = strtok($messageKey, '.');
        while ($arrayKey !== false) {
            $message = $message[$arrayKey];
            $arrayKey = strtok('.');
        }

        if (!isset($message) || !is_string($message)) return $messageKey;

        if ($parameters !== null) {
            foreach ($parameters as $key => $value) {
                $message = str_replace(':' . $key, $value, $message);
            }
        }

        return $message;
    }
}