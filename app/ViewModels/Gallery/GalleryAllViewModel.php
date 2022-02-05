<?php

namespace App\ViewModels\Gallery;

class GalleryAllViewModel
{
    public function __construct(
        public int $page,
        public array $items,
    ) {
    }
}
