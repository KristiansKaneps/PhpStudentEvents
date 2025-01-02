<?php

namespace Services;

use Types\NotificationPriority;
use Types\NotificationType;

class NotificationService extends Service {
    public const STATUS_NOT_SENT = 0;
    public const STATUS_SENT = 1;
    public const STATUS_DELETABLE = 255;

    public function listToastNotifications(): array {
        $userId = resolve(Auth::class)->getUserId();
        try {
            $this->db->beginTransaction();
            if (empty($userId)) {
                // Delete previous [deletable] notifications.
                $this->db->execute(<<<SQL
                    DELETE FROM notifications
                    WHERE status = ? AND session_id = ?
                    SQL,
                    [self::STATUS_DELETABLE, session_id()],
                );
                // Get current toast notifications.
                $notifications = $this->db->query(<<<SQL
                SELECT * FROM notifications
                WHERE status = ? AND session_id = ? AND priority = ?
                ORDER BY created_at, type, type_counter
                SQL,
                    [self::STATUS_SENT, session_id(), NotificationPriority::NONE->value],
                );
                // Mark current toast notifications for deletion.
                $this->db->execute(<<<SQL
                UPDATE notifications SET status = ?
                WHERE status = ? AND session_id = ? AND priority = ?
                SQL,
                    [self::STATUS_DELETABLE, self::STATUS_SENT, session_id(), NotificationPriority::NONE->value],
                );
            } else {
                // Delete previous [deletable] notifications.
                $this->db->execute(<<<SQL
                    DELETE FROM notifications
                    WHERE status = ? AND (user_id = ? OR session_id = ?)
                    SQL,
                    [self::STATUS_DELETABLE, $userId, session_id()],
                );
                // Get current toast notifications.
                $notifications = $this->db->query(<<<SQL
                SELECT * FROM notifications
                WHERE status = ? AND (user_id = ? OR session_id = ?) AND priority = ?
                ORDER BY timeout DESC, created_at, type, type_counter
                SQL,
                    [self::STATUS_SENT, $userId, session_id(), NotificationPriority::NONE->value],
                );
                // Mark current toast notifications for deletion.
                $this->db->execute(<<<SQL
                UPDATE notifications SET status = ?
                WHERE status = ? AND (user_id = ? OR session_id = ?) AND priority = ?
                SQL,
                    [self::STATUS_DELETABLE, self::STATUS_SENT, $userId, session_id(), NotificationPriority::NONE->value],
                );
            }
            $this->db->commitTransaction();
            return $notifications;
        } catch (\Exception) { }
        return [];
    }

    private const DEFAULT_TOAST_NOTIFICATION_TIMEOUT = 3500;

    /**
     * Creates a toast notification.
     * @param int|NotificationType $type Notification type.
     * @param string $text Notification text.
     * @param int|null $timeout Notification timeout in web (or `null` if default timeout of 3500ms).
     * @param int|null $eventId Event ID that this notification is related to
     *                          (or `null` if it is a generic notification).
     * @param int|null $userId User ID for which to show the toast notification (or `null` for current user).
     * @return void
     */
    public function createToastNotification(int|NotificationType $type, string $text, int|null $timeout = null, int|null $eventId = null, int|null $userId = null): void {
        if ($userId === null) {
            $sessionId = session_id();
            if ($sessionId === false) return; // Unknown error
        } else {
            $sessionId = null;
        }
        $type = is_object($type) ? $type->value : $type;
        try {
            if ($eventId === null) {
                if ($sessionId !== null) {
                    $nextTypeCount = $this->db->query(<<<SQL
                        SELECT MAX(type_counter) + 1 AS type_count FROM notifications
                        WHERE event_id IS NULL AND session_id = :session_id AND type = :type
                            AND status = :status AND priority = :priority
                        SQL,
                        ['session_id' => $sessionId, 'type' => $type, 'status' => self::STATUS_SENT, 'priority' => NotificationPriority::NONE->value],
                    );
                } else {
                    $nextTypeCount = $this->db->query(<<<SQL
                        SELECT MAX(type_counter) + 1 AS type_count FROM notifications
                        WHERE event_id IS NULL AND user_id = :user_id AND type = :type
                            AND status = :status AND priority = :priority
                        SQL,
                        ['user_id' => $userId, 'type' => $type, 'status' => self::STATUS_SENT, 'priority' => NotificationPriority::NONE->value],
                    );
                }
            } else {
                if ($sessionId !== null) {
                    $nextTypeCount = $this->db->query(<<<SQL
                        SELECT MAX(type_counter) + 1 AS type_count FROM notifications
                        WHERE event_id = :event_id AND session_id = :session_id AND type = :type
                            AND status = :status AND priority = :priority
                        SQL,
                        ['session_id' => $sessionId, 'type' => $type, 'status' => self::STATUS_SENT, 'priority' => NotificationPriority::NONE->value, 'event_id' => $eventId]
                    );
                } else {
                    $nextTypeCount = $this->db->query(<<<SQL
                        SELECT MAX(type_counter) + 1 AS type_count FROM notifications
                        WHERE event_id = :event_id AND user_id = :user_id AND type = :type
                            AND status = :status AND priority = :priority
                        SQL,
                        ['user_id' => $userId, 'type' => $type, 'status' => self::STATUS_SENT, 'priority' => NotificationPriority::NONE->value, 'event_id' => $eventId]
                    );
                }
            }

            $nextTypeCount = empty($nextTypeCount[0]['type_count']) ? 0 : $nextTypeCount[0]['type_count'];
            $timeout = $timeout === null ? self::DEFAULT_TOAST_NOTIFICATION_TIMEOUT : $timeout;

            $query = <<<SQL
                INSERT INTO notifications
                (event_id, user_id, session_id, type, type_counter, message, timeout, priority, status) 
                VALUES (:event_id, :user_id, :session_id, :type, :type_counter, :message, :timeout, :priority, :status)
            SQL;
            $this->db->execute($query, [
                'event_id' => $eventId,
                'user_id' => $userId,
                'session_id' => $sessionId,
                'type' => $type,
                'type_counter' => $nextTypeCount,
                'message' => $text,
                'timeout' => $timeout,
                'priority' => NotificationPriority::NONE->value,
                'status' => self::STATUS_SENT,
            ]);
        } catch (\Exception) { }
    }

    public function createNotification(array $notificationData): void {
        try {
            if (empty($notificationData['event_id'])) {
                $nextTypeCount = $this->db->query(<<<SQL
                    SELECT MAX(type_counter) + 1 AS type_count FROM notifications
                    WHERE event_id IS NULL AND user_id = :user_id AND type = :type
                    SQL,
                    ['user_id' => $notificationData['user_id'], 'type' => is_object($notificationData['type']) ? $notificationData['type']->value : $notificationData['type']]
                );
            } else {
                $nextTypeCount = $this->db->query(<<<SQL
                    SELECT MAX(type_counter) + 1 AS type_count FROM notifications
                    WHERE event_id = :event_id AND user_id = :user_id AND type = :type
                    SQL,
                    ['event_id' => $notificationData['event_id'], 'user_id' => $notificationData['user_id'], 'type' => is_object($notificationData['type']) ? $notificationData['type']->value : $notificationData['type']]
                );
            }

            $nextTypeCount = empty($nextTypeCount[0]['type_count']) ? 0 : $nextTypeCount[0]['type_count'];
            $timeout = empty($notificationData['timeout']) ? self::DEFAULT_TOAST_NOTIFICATION_TIMEOUT : $notificationData['timeout'];

            $query = <<<SQL
                INSERT INTO notifications
                (event_id, user_id, session_id, type, type_counter, message, description, timeout, priority, status) 
                VALUES (:event_id, :user_id, :session_id, :type, :type_counter, :message, :description, :timeout, :priority, :status)
            SQL;
            $this->db->execute($query, [
                'event_id' => $notificationData['event_id'],
                'user_id' => $notificationData['user_id'],
                'session_id' => $notificationData['session_id'] ?? null,
                'type' => is_object($notificationData['type']) ? $notificationData['type']->value : $notificationData['type'],
                'type_counter' => $nextTypeCount,
                'message' => $notificationData['message'],
                'description' => $notificationData['description'],
                'timeout' => $notificationData['timeout'],
                'priority' => $notificationData['priority'],
                'status' => $notificationData['status'],
            ]);
        } catch (\Exception) { }
    }
}