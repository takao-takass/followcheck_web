<?php

use App\Models\ShowThumbnail;

namespace App\ViewModels;

class ShowThumbnailViewModel
{
    public int $Page;

    public int $remove_retweets;

    public int $MaxPage;

    public int $Count;

    public string $user_id;

    public array $show_thumbnails = [];

}

