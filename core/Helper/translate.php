<?php /** @noinspection PhpNoReturnAttributeCanBeAddedInspection */

use Localization\Localization;

/**
 * @return string Current locale.
 */
function locale(): string {
    return Localization::getCurrentLocale();
}

function translate($messageKey, ?array $parameters = null, ?string $locale = null): string {
    return Localization::translate($messageKey, $parameters, $locale);
}

function trans($messageKey, ?array $parameters = null, ?string $locale = null): string {
    return Localization::translate($messageKey, $parameters, $locale);
}

function t($messageKey, ?array $parameters = null, ?string $locale = null): string {
    return Localization::translate($messageKey, $parameters, $locale);
}

function __($messageKey, ?array $parameters = null, ?string $locale = null): string {
    return Localization::translate($messageKey, $parameters, $locale);
}
