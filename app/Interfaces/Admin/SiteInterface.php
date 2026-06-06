<?php

namespace App\Interfaces\Admin;

interface SiteInterface
{
    public function all();
    public function save(array $data);
}