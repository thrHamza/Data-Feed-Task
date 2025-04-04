<?php

namespace App\Service;

use App\Storage\StorageInterface;
use Psr\Log\LoggerInterface;

class ProductImporter
{
    private StorageInterface $storage;
    protected LoggerInterface $logger;

    public function __construct(StorageInterface $storage, LoggerInterface $logger)
    {
        $this->storage = $storage;
        $this->logger = $logger;
    }

    public function import(array $products): void{

        $this->logger->info("Start Importing products...");

        try {
            $this->storage->save($products);
            $this->logger->info("Products imported successfully!");
        } catch (\Exception $e) {
            $this->logger->error("Error during import: " . $e->getMessage());
        }
    }


}