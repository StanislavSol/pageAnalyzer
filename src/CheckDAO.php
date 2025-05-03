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
            var_dump($check);
        }
        var_dump($check);
    }

    public function find($value, $valueSearch = 'url_id')
    {
        $sql = "SELECT * FROM checks WHERE {$valueSearch} = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$value]);
        $allChecks = $stmt->fetchAll();
        $checks = [];
        var_dump($allChecks);

        foreach ($allChecks as $check) {
            $data = new Check($check['url_id']);
            $data->setId($check['id']);
            $data->setTimeCreated($check['created_at']);
            $checks[] = $data;
        }

        return $checks;
    }
}
