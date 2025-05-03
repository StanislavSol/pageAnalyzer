<?php

namespace App;

class Check
{
    private $urlCheckId;
    private $urlId;
    private $statusCode;
    private $h1;
    private $title;
    private $description;
    private $timeCreated;

    public function __construct($urlId)
    {
        $this->id = null;
        $this->urlId = $urlId;
        $this->statusCode = null;
        $this->h1 = null;
        $this->title = null;
        $this->description = null;    
        $this->timeCreated = null;
    }

    public function getId()
    {
        return $this->id;
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

    public function getDescription()
    {
        return $this->description;
    }

    public function getTimeCreated()
    {
        return $this->timeCreated;
    }

    public function setId($id): void
    {
        $this->id = $id;
    }

    public function setUrlId($urlId): void
    {
        $this->urlUrlId = $urlId;
    }

    public function setStatusCode($statusCode): void
    {
        $this->statusCode = $statusCode;
    }

    public function setH1($h1): void
    {
        $this->h1 = $h1;
    }

    public function setTitle($title): void
    {
        $this->title = $title;
    }

    public function setDescription($description): void
    {
        $this->Description = $description;
    }

    public function setTimeCreated($timeCreated): void
    {
        $this->timeCreated = $timeCreated;
    }

}
