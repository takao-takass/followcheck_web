<?php

namespace App\Models;

class TweetTakeUser
{
    public string $UserId;
    public string $DispName;
    public string $UserName;
    public string $ThumbnailUrl;
    public string $Status;

    public function __construct(string $UserId, string $DispName, string $Name,string $ThumbnailUrl, string $Status)
    {
        $this->UserId = $UserId;
        $this->DispName = $DispName;
        $this->Name = $Name;
        $this->ThumbnailUrl = $ThumbnailUrl;
        $this->Status = $Status;
    }
}
