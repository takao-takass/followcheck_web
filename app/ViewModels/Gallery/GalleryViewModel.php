<?php

namespace App\ViewModels\Gallery;

class GalleryViewModel
{
    public function __construct(
        public int $page,
        public string $thumbnail_size,
        public array $items,
    ) {
    }
}
