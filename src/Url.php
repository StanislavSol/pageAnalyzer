<?php

namespace App;

class Url
{
    private string $urlName;
    private int $urlId;
    private string $timeCreated

    public function __construct(string $urlName, string $timeCreated = null)
    {
        $this->$urlName = $urlName;
        $this->$urlId = null;
        $this->$timeCreated = $timeCreated;
    }

    public function getUrl(): string
    {
        return $this->$urlName;
    }

    public function getId(): int
    {
        return $this->$urlId;
    }

    public function getTimeCreated(): string
    {
        return $this->$timeCreated;
    }

    public function setId($id): void
    {
        $this->$urlId = $id;
    }

    public function setTimeCreated($time): void
    {
        $this->$timeCreated = $time;
    }

}
