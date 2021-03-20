<?php

namespace App\ViewModels;

use App\Models\TweetTakeUser;
use App\DataModels\RelationalUsers;

class TwitterAccountsViewModel
{
    public int $Page;
    public int $Count;
    public int $MaxPage;
    public array $Accounts = [];

}

class TwitterAccount
{
    public bool $TakingTweet; 
    public bool $TakedFollow;
    public bool $TakedFavorite;
    public array $MediaUrls = [];
}
