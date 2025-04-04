<?php

namespace App\Tests\Command;

use App\Command\ImportProductsCommand;
use App\Service\CsvReader;
use App\Service\ProductImporter;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;

class ImportProductsCommandTest extends TestCase
{
    private CsvReader $csvReader;
    private ProductImporter $productImporter;
    private LoggerInterface $logger;
    private CommandTester $commandTester;

    protected function setUp(): void
    {
        $this->csvReader = $this->createMock(CsvReader::class);
        $this->productImporter = $this->createMock(ProductImporter::class);
        $this->logger = $this->createMock(LoggerInterface::class);

        $command = new ImportProductsCommand($this->csvReader, $this->productImporter, $this->logger);
        $application = new Application();
        $application->add($command);

        $this->commandTester = new CommandTester($application->find('app:import-products'));
    }

    public function testSuccessfulImport(): void
    {
        $this->csvReader->method('read')->willReturn([
            ['gtin' => '123456', 'language' => 'en', 'title' => 'Test Product', 'price' => 100, 'stock' => 10]
        ]);

        $this->productImporter->expects($this->once())->method('import');

        $this->commandTester->execute(['file' => 'dummy.csv']);

        $this->commandTester->assertCommandIsSuccessful();
        $this->assertStringContainsString('Products imported successfully!', $this->commandTester->getDisplay());
    }

    public function testFileNotFound(): void
    {
        $this->csvReader->method('read')->willThrowException(new \Exception('File not found'));

        $this->commandTester->execute(['file' => 'nonexistent.csv']);

        $this->assertStringContainsString('File not found', $this->commandTester->getDisplay());
        $this->assertSame(Command::FAILURE, $this->commandTester->getStatusCode());
    }

    public function testImportFailure(): void
    {
        $this->csvReader->method('read')->willReturn([
            ['gtin' => '123456', 'language' => 'en', 'title' => 'Test Product', 'price' => 100, 'stock' => 10]
        ]);

        $this->productImporter->method('import')->willThrowException(new \Exception('Database error'));

        $this->commandTester->execute(['file' => 'dummy.csv']);

        $this->assertStringContainsString('Database error', $this->commandTester->getDisplay());
        $this->assertSame(Command::FAILURE, $this->commandTester->getStatusCode());
    }
}