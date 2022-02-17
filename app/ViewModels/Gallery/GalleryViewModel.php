<?php

namespace App\ViewModels\Gallery;

class GalleryViewModel
{
    public function __construct(
        public int $page,
        public array $items,
    ) {
    }
}
