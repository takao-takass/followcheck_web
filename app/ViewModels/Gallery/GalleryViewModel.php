<?php

namespace App\ViewModels\Gallery;

class GalleryViewModel
{
    public function __construct(
        public string $user_id,
        public string $user_name,
        public int $page,
        public string $thumbnail_size,
        public array $items,
    ) {
    }
}
