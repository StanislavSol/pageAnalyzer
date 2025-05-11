<?php

namespace App;

use App\Check;
use Carbon\Carbon;

class CheckDAO
{
    private \PDO $pdo;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function save(Check $check): void
    {
        if (is_null($check->getId())) {
            $sql = "INSERT INTO checks (url_id, created_at, status_code, h1, title, description) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $this->pdo->prepare($sql);
            $timeNow = Carbon::now()->toDateTimeString();
            $urlId = $check->getUrlId();
            $statusCode = $check->getStatusCode();
            $h1 = $check->getH1();
            $title = $check->getTitle();
            $description = $check->getDescription();
            $stmt->bindParam(1, $urlId);
            $stmt->bindParam(2, $timeNow);
            $stmt->bindParam(3, $statusCode);
            $stmt->bindParam(4, $h1);
            $stmt->bindParam(5, $title);
            $stmt->bindParam(6, $description);
            $stmt->execute();
            $id = (int) $this->pdo->lastInsertId();
            $check->setId($id);
            $check->setTimeCreated($timeNow);

        }

    }

    public function getChecks($value, $valueSearch = 'url_id')
    {
        $sql = "SELECT * FROM checks WHERE {$valueSearch} = ? ORDER BY id DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$value]);
        $allChecks = $stmt->fetchAll();
        $checks = [];
        foreach ($allChecks as $check) {
            $data = new Check($check['url_id']);
            $data->setId($check['id']);
            $data->setTimeCreated($check['created_at']);
            $data->setStatusCode($check['status_code']);
            $data->setH1($check['h1']);
            $data->setTitle($check['title']);
            $data->setDescription($check['description']);
            $checks[] = $data;
        }
        return $checks;
    }

    public function findLastCheck($url_id)
    {
        $sql = "SELECT * FROM checks WHERE url_id = ? ORDER BY created_at DESC LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$url_id]);
        $check = $stmt->fetch();
        $data = new Check($check['url_id']);
        $data->setTimeCreated($check['created_at']);
        $data->setStatusCode($check['status_code']);

        return $data;

    }
}
