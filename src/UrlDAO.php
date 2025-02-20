<?php

namespace App;

use App\Url;

class UserDAO 
{
    private \PDO $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function save(User $user): void
    {
        // Если пользователь новый, выполняем вставку
        // Иначе обновляем
        if (is_null($user->getId())) {
            $sql = "INSERT INTO users (username, phone) VALUES (?, ?)";
            $stmt = $this->pdo->prepare($sql);
            $username = $user->getUsername();
            $phone = $user->getPhone();
            $stmt->bindParam(1, $username);
            $stmt->bindParam(2, $phone);
            $stmt->execute();
            // Извлекаем идентификатор и добавляем в сохраненный объект
            $id = (int) $this->pdo->lastInsertId();
            $user->setId($id);
        } else {
            // Здесь код обновления существующей записи
        }
    }

    public function find(int $id): ?User
    {
        $sql = "SELECT * FROM users WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id]);
        $result = $stmt->fetch();
        if ($result) {
            $user = new User($result['username'], $result['phone']);
            $user->setId($id);
            return $user;
        }
        return null;
    }
}
