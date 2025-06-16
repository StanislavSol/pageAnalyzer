<?php

namespace App;

use App\Check;
use Carbon\Carbon;
use PDO;
use InvalidArgumentException;
use RuntimeException;

class CheckDAO
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function save(Check $check): void
    {
        if ($check->getId() === null) {
            $sql = "INSERT INTO checks (url_id, created_at, status_code, h1, title, description) 
                    VALUES (:url_id, :created_at, :status_code, :h1, :title, :description)";
            
            $stmt = $this->pdo->prepare($sql);
            $timeNow = Carbon::now()->toDateTimeString();
            
            $stmt->execute([
                ':url_id' => $check->getUrlId(),
                ':created_at' => $timeNow,
                ':status_code' => $check->getStatusCode(),
                ':h1' => $check->getH1(),
                ':title' => $check->getTitle(),
                ':description' => $check->getDescription()
            ]);

            if ($stmt->rowCount() === 0) {
                throw new RuntimeException("Failed to save check");
            }

            $id = (int)$this->pdo->lastInsertId();
            $check->setId($id);
            $check->setTimeCreated($timeNow);
        }
    }

    public function getChecks(string|int $value, string $valueSearch = 'url_id'): array
    {
        // Validate allowed search columns
        $allowedColumns = ['url_id', 'id', 'status_code'];
        if (!in_array($valueSearch, $allowedColumns, true)) {
            throw new InvalidArgumentException("Invalid search field");
        }

        $sql = "SELECT * FROM checks WHERE {$valueSearch} = :value ORDER BY id DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':value' => $value]);

        if ($stmt === false) {
            throw new RuntimeException("Database query failed");
        }

        return array_map(function (array $check) {
            return (new Check($check['url_id']))
                ->setId($check['id'])
                ->setTimeCreated($check['created_at'])
                ->setStatusCode($check['status_code'])
                ->setH1($check['h1'])
                ->setTitle($check['title'])
                ->setDescription($check['description']);
        }, $stmt->fetchAll());
    }

    public function findLastCheck(int $url_id): ?Check
    {
        $sql = "SELECT * FROM checks WHERE url_id = :url_id ORDER BY created_at DESC LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':url_id' => $url_id]);
        $check = $stmt->fetch();
        
        if ($check === false) {
            return null;
        }
        
        return (new Check($check['url_id']))
            ->setId($check['id'])
            ->setTimeCreated($check['created_at'])
            ->setStatusCode($check['status_code'])
            ->setH1($check['h1'])
            ->setTitle($check['title'])
            ->setDescription($check['description']);
    }
}
