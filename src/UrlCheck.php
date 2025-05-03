<?php

namespace App;

class UrlCheck
{
    private $urlCheckId;
    private $urlId;
    private $statusCode;
    private $h1;
    private $title;
    private $description;
    private $timeCreated;

    public function __construct()
    {
        $this->urlCheckId = null;
        $this->urlId = null;
        $this->statusCode = null;
        $this->h1 = null;
        $this->title = null;
        $this->description = null;    
        $this->timeCreated = null;
    }

    public function getUrlCheckId(): string
    {
        return $this->getUrlCheckId;
    }

    public function getUrlId()
    {
        return $this->urlId;
    }

    public function getStatusCode()
    {
        return $this->statusCode;
    }

    public function getH1()
    {
        return $this->h1;
    }

    public function getTitle()
    {
        return $this->title;
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
