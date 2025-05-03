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
            $findUrl = $this->find($url->getUrlName(), 'name');
            $timeNow = Carbon::now()->toDateTimeString();
            if (is_null($findUrl)) {
                $sql = "INSERT INTO urls (name, created_at) VALUES (?, ?)";
                $stmt = $this->pdo->prepare($sql);
                $username = $url->getUrlName();
                $stmt->bindParam(1, $username);
                $stmt->bindParam(2, $timeNow);
                $stmt->execute();
                $id = (int) $this->pdo->lastInsertId();
                $url->setId($id);
                $url->setTimeCreated($timeNow);
            } else {
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

    public function find($value, $valueSearch = 'id'): ?Url
    {
        $sql = "SELECT * FROM urls WHERE {$valueSearch} = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$value]);
        $result = $stmt->fetch();
        if ($result) {
            $url = new Url($result['name'], $result['created_at']);
            $url->setId($result['id']);
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
