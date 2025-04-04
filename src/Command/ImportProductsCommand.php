<?php

namespace App\Command;

use App\Service\CsvReader;
use App\Service\ProductImporter;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:import-products',
    description: 'Import products from Csv file'
)]
class ImportProductsCommand extends Command
{
    private CsvReader $csvReader;
    private ProductImporter $productImporter;
    private LoggerInterface $logger;


    public function __construct(CsvReader $csvReader, ProductImporter $productImporter, LoggerInterface $logger){
        parent::__construct();
        $this->csvReader = $csvReader;
        $this->productImporter = $productImporter;
        $this->logger = $logger;
    }

    protected function configure(): void
    {
        $this->addArgument('file', InputArgument::REQUIRED, 'Path to the CSV file');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int{
        $filePath = $input->getArgument('file');

        try {
            $products = $this->csvReader->read($filePath);
            $this->productImporter->import($products);
            $output->writeln("<info>Products imported successfully!</info>");
        } catch (\Exception $e){
            $output->writeln("<error>".$e->getMessage()."</error>");
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }

}