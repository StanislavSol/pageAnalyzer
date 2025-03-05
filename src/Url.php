<?php

namespace App;

class Url
{
    private $urlName;
    private $urlId;
    private $timeCreated;

    public function __construct(string $urlName, string $timeCreated = null)
    {
        $this->urlName = $urlName;
        $this->urlId = null;
        $this->timeCreated = $timeCreated;
    }

    public function getUrlName(): string
    {
        return $this->urlName;
    }

    public function getId()
    {
        return $this->urlId;
    }

    public function getTimeCreated(): string
    {
        return $this->timeCreated;
    }

    public function setId($id): void
    {
        $this->urlId = $id;
    }

    public function setTimeCreated($time): void
    {
        $this->timeCreated = $time;
    }

}
