<?php

namespace App;

use App\Url;
use Carbon\Carbon;

const INDEX_ID = 0;

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
            $timeNow = Carbon::now()->toDateTimeString();
            try {
                $sql = "INSERT INTO urls (name, created_at) VALUES (?, ?)";
                $stmt = $this->pdo->prepare($sql);
                $username = $url->getUrlName();
                $stmt->bindParam(1, $username);
                $stmt->bindParam(2, $timeNow);
                $stmt->execute();
                $id = (int) $this->pdo->lastInsertId();
                $url->setId($id);
                $url->setTimeCreated($timeNow);
            } catch (\PDOException) {
                $sql = "SELECT id FROM urls WHERE name = ?";
                $stmt = $this->pdo->prepare($sql);
                $username = $url->getUrlName();
                $stmt->bindParam(1, $username);
                $stmt->execute();
                $id = $stmt->fetch();
                $url->setId((integer) $id[INDEX_ID]);
                $url->setTimeCreated($timeNow);
                $this->isSaveUrl = false;
            }
        }
    }

    public function find(int $id): ?Url
    {
        $sql = "SELECT * FROM urls WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id]);
        $result = $stmt->fetch();
        if ($result) {
            $url = new Url($result['name'], $result['created_at']);
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

        foreach ($allUrls as $urlData) {
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
