<?php

namespace App;

use App\Url;
use Carbon\Carbon;

class UrlDAO 
{
    private \PDO $pdo;
    public bool $isSaveUrl;

    public function __construct(\PDO $pdo) {
        $this->pdo = $pdo;
        $this->isSaveUrl = true;
    }

    public function save(Url $url): void
    {
        if (is_null($url->getId())) {
            try {
                $sql = "INSERT INTO urls (name, created_at) VALUES (?, ?)";
                $stmt = $this->pdo->prepare($sql);
                $username = $url->getUrlName();
                $timeNow = Carbon::now()->toDateTimeString();
                $stmt->bindParam(1, $username);
                $stmt->bindParam(2, $timeNow);
                $stmt->execute();
                $id = (int) $this->pdo->lastInsertId();
                $url->setId($id);
                $url->setTimeCreated($timeNow);
            } catch (\PDOException) {
                $this->isSaveUrl = false;
            }
        }
    }

    public function find(int $id): ?Url
    {
        $sql = "SELECT * FROM users WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id]);
        $result = $stmt->fetch();
        if ($result) {
            $url = new Url($result['name'], $result['$timeCreated']);
            $url->setId($id);
            return $url;
        }
        return null;
    }

    public function getAllUrl(): array
    {
        $sql = "SELECT * FROM urls";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $allUrls = $stmt->fetchAll();
        $urls = [];

        foreach ($result as $urlData) {
            $idUrl = $urlData['id'];
            $nameUrl = $urlData['name'];
            $timeCreated = $urlData['created_at'];
            $url = new Url($nameUrl);
            $url->setId($idUrl);
            $url->setTimeCreated($timeCreated);
            $urls[] = $url;
        }

        return $urls;
    }
}
