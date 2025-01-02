<?php

namespace Types;

enum NotificationType: int {
    case SUCCESS = 0;
    case ERROR = 1;
    case INFO = 2;

    static function toString(mixed $notificationType): string {
        $type = is_object($notificationType) ? $notificationType->value : intval($notificationType);
        return match ($type) {
            self::SUCCESS->value => 'success',
            self::ERROR->value => 'error',
            self::INFO->value => 'info',
            default => ''
        };
    }

    static function getCharIcon(mixed $notificationType): string {
        $type = is_object($notificationType) ? $notificationType->value : intval($notificationType);
        return match ($type) {
            self::SUCCESS->value => htmlspecialchars('✔'),
            self::ERROR->value => htmlspecialchars('⚠'),
            self::INFO->value => htmlspecialchars('ℹ'),
            default => ''
        };
    }
}
