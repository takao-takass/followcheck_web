<?php

namespace App\ViewModels;

class KeepByUserViewModel
{
    public function __construct(
        string $user_id,
        int $page,
        int $max_page,
        int $count,
        array $list
    ) {
        $this->user_id = $user_id;
        $this->page = $page;
        $this->max_page = $max_page;
        $this->count = $count;
        $this->list = $list;
    }

    public string $user_id;
    public int $page;
    public int $max_page;
    public int $count;
    public array $list;
}

