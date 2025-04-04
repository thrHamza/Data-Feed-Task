<?php

namespace App\Service;

use Psr\Log\LoggerInterface;

class CsvReader
{
    private LoggerInterface $logger;
    private ProductValidator $validator;

    public function __construct(LoggerInterface $importLogger, ProductValidator $validator){
        $this->logger = $importLogger;
        $this->validator = $validator;
    }

    public function read(string $filePath) : array{

        if (!file_exists($filePath)){
            $this->logger->critical("CSV file not found: $filePath");
            throw new \Exception("CSV file not found: $filePath");
        }

        $data = [];
        $rowNumber = 0;

        if (($handle = fopen($filePath, "r")) !== FALSE){
            $header = fgetcsv($handle);

            while (($row = fgetcsv($handle)) !== FALSE){
                $rowNumber++;
                if (count($row) !== count($header)){
                    $this->logger->warning("Skipping row $rowNumber due to incorrect column count: " . implode(',', $row));
                    continue;
                }

                $rowData = array_combine($header, $row);
                $errors = $this->validator->validate($rowData);

                if (!empty($errors)){
                    $this->logger->warning("Skipping row $rowNumber due to errors: " . implode(', ', $errors));
                    continue;
                }

                $data[] = $rowData;
            }
            fclose($handle);
        }

        $this->logger->info("CSV file read successfully with ". count($data)." valid rows");
        return $data;
    }

}