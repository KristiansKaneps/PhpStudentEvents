<?php

namespace Services;

class ProfileService extends Service {
    const UPDATE_RESULT_SUCCESS = 0;
    const UPDATE_RESULT_EXCEPTION = 1;

    public function updateUser(int $userId, #[\SensitiveParameter] array $data): int {
        try {
            $query = 'UPDATE users SET ';
            $password = $data['password'] ?? null;
            $data = array_filter($data, function ($key) { return $key !== 'password'; }, ARRAY_FILTER_USE_KEY);
            $keys = array_keys($data);

            $this->db->beginTransaction();
            if (!empty($keys)) {
                $key = array_shift($keys);
                $query .= $key . ' = :' . $key;
                foreach ($keys as $key)
                    $query .= ', ' . $key . ' = :' . $key;
                $query .= ' WHERE id = :id';
                $data['id'] = $userId;

                if (!$this->db->execute($query, $data)) {
                    $this->db->rollbackTransaction();
                    return self::UPDATE_RESULT_EXCEPTION;
                }
            }
            if (!empty($password) && !resolve(Auth::class)->changePassword($userId, $password)) {
                $this->db->rollbackTransaction();
                return self::UPDATE_RESULT_EXCEPTION;
            }
            $this->db->commitTransaction();
        } catch (\Exception) {
            return self::UPDATE_RESULT_EXCEPTION;
        }
        return self::UPDATE_RESULT_SUCCESS;
    }
}