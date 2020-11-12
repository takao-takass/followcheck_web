<?php

use App\Models\TweetTakeUser;

namespace App\ViewModels;

class TweetUsersViewModel
{
    public int $Page;

    public int $Count;

    public int $MaxPage;

    public array $TweetTakeUsers = [];

}

