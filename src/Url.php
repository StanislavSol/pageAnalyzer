<?php

namespace App;

class Url
{
    private \PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    private function isUrlInDatabase(string $urlName)
    {
        $sql = "SELECT id FROM urls WHERE name = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$urlName]);
        $result = $stmt->fetch();

        if ($result) {}

    }

    public function save($urlName)
    {
        $
    }

}
