<?php

use App\Models\TweetTakeUser;
use App\DataModels\RelationalUser;

namespace App\ViewModels;

class TwitterAccountsViewModel
{
    public int $Page;

    public int $Count;

    public int $MaxPage;

    public array $Accounts = [];

}

class TwitterAccount
{
    public boolean $TakingTweet; 
    public boolean $TakedFollow;
    public boolean $TakedFavorite;
    public RelationalUser $User;
    public array $Medias = [];
}
