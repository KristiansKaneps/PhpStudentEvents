<?php

namespace Services;

class ProfileService extends Service {
    const UPDATE_RESULT_SUCCESS = 0;
    const UPDATE_RESULT_EXCEPTION = 1;

    public function updateUser(int $userId, array $data): int {
        try {
            $query = 'UPDATE users SET ';
            $keys = array_keys($data);
            $key = array_shift($keys);
            $query .= $key . ' = :' . $key;
            foreach ($keys as $key)
                $query .= ', ' . $key . ' = :' . $key;
            $query .= ' WHERE id = :id';
            $data['id'] = $userId;
            if (!$this->db->execute($query, $data))
                return self::UPDATE_RESULT_EXCEPTION;
        } catch (\Exception) {
            return self::UPDATE_RESULT_EXCEPTION;
        }
        return self::UPDATE_RESULT_SUCCESS;
    }
}