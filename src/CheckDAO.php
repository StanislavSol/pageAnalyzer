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
            $sql = "INSERT INTO checks (url_id, created_at) VALUES (?, ?)";
            $stmt = $this->pdo->prepare($sql);
            $timeNow = Carbon::now()->toDateTimeString();
            $stmt->bindParam(1, $check->getUrlId());
            $stmt->bindParam(2, $timeNow);
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
