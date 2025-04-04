<?php

namespace App\Storage;

use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

class MySQLStorage extends StorageInterface
{
    private EntityManagerInterface $entityManager;
    private LoggerInterface $logger;

    public function __construct(EntityManagerInterface $entityManager, LoggerInterface $importLogger){
        $this->entityManager = $entityManager;
        $this->logger = $importLogger;
    }

    public function save(array $products): void{

        $importedCount = 0;

        try {
            $this->entityManager->beginTransaction();

            foreach ($products as $data) {
                try {
                    $product = new Product();
                    $product->setGtin($data['gtin'])
                        ->setLanguage($data['language'])
                        ->setTitle($data['title'])
                        ->setPicture($data['picture'])
                        ->setDescription($data['description'])
                        ->setPrice($data['price'])
                        ->setStock($data['stock']);

                    $this->entityManager->persist($product);
                    $importedCount++;
                } catch (\Exception $e) {
                    $this->logger->error("Failed to import product with GTIN: " . $data['gtin'] . ": " . $e->getMessage());
                    continue;
                }
            }

            $this->entityManager->flush();
            $this->entityManager->commit();
            $this->logger->info("Import completed: {$importedCount} products added");

        } catch (\Exception $e) {
            $this->entityManager->rollback();
            $this->logger->error("Import failed: " . $e->getMessage());
        }
    }
}