<?php

namespace App\ViewModels\Gallery;

class GalleryViewModel
{
    public function __construct(
        public string $user_id,
        public string $user_name,
        public int $page,
        public string $thumbnail_size,
        public string $list_sort,
        public array $items,
        public int $sum_media_size,
        public string $tweeted_datetime
    ) {
    }
}
