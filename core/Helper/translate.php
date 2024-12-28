<?php /** @noinspection PhpNoReturnAttributeCanBeAddedInspection */

use Localization\Localization;

/**
 * @return string Current locale.
 */
function locale(): string {
    return Localization::getCurrentLocale();
}

function translate($messageKey, ?array $parameters = null): string {
    return Localization::translate($messageKey, $parameters);
}

function trans($messageKey, ?array $parameters = null): string {
    return Localization::translate($messageKey, $parameters);
}

function t($messageKey, ?array $parameters = null): string {
    return Localization::translate($messageKey, $parameters);
}

function __($messageKey, ?array $parameters = null): string {
    return Localization::translate($messageKey, $parameters);
}
