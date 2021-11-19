<?php

namespace App\ViewModels\Batch;

class BatchLog
{
    public string $logging_datetime;
    public string $class;
    public string $section;
    public string $message;

    public function __construct(string $logging_datetime, string $class, string $section, string $message) {
        $this->logging_datetime = $logging_datetime;
        $this->class = $class;
        $this->section = $section;
        $this->message = $message;
    }
}
