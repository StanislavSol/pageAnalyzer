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
            $stmt->bindParam(1, $check->getUrlId);
            $stmt->bindParam(2, $timeNow);
            $stmt->execute();
            $id = (int) $this->pdo->lastInsertId();
            $check->setId($id);
            $check->setTimeCreated($timeNow);
        }
    }

    public function find($value, $valueSearch = 'url_id')
    {
        $sql = "SELECT * FROM checks WHERE {$valueSearch} = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$value]);
        $allChecks = $stmt->fetchAll();
        $checks = [];

        foreach ($allChecks as $check) {
            $id = $check['id'];
            $urlId = $check['url_id'];
            $timeCheck = $check['created_at'];
            $check = new Check($urlId);
            $check->setId($id);
            $check->setTimeCreated($timeCheck);
            $checks[] = $check;
        }

        return $checks;
    }
}
