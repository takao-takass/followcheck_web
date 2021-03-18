<?php

namespace App\ViewModels;

use App\Models\TweetTakeUser;

class TweetUsersViewModel
{
    public int $Page;

    public int $Count;

    public int $MaxPage;

    public array $TweetTakeUsers = [];

}

