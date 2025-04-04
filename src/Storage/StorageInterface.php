<?php

namespace App\Storage;

abstract class StorageInterface
{
    abstract public function save(array $products): void;

}