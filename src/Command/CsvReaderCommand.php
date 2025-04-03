<?php

namespace App\Command;

use App\Service\CsvReader;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:test-csv-reader',
    description: 'Command test for CsvReader Service',
)]
class CsvReaderCommand extends Command
{
    private CsvReader $csvReader;

    public function __construct(CsvReader $csvReader)
    {
        parent::__construct();
        $this->csvReader = $csvReader;
    }

    protected function configure(): void
    {
        $this->addArgument('file', InputArgument::REQUIRED, 'Path to the CSV file');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $filePath = $input->getArgument('file');

        try {
            $data = $this->csvReader->read($filePath);
            $output->writeln("<info>CSV file read successfully with ". count($data). " rows</info>");
        } catch (\Exception $e) {
            $output->writeln("<error>" . $e->getMessage() . "</error>");
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
