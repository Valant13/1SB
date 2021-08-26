<?php

namespace App\Command\Auction;

use App\Config;
use App\Entity\Catalog\Material;
use App\Repository\Catalog\MaterialRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ImportMaterialPricesCommand extends Command
{
    const RESULT_IMPORTED = 'imported';
    const RESULT_MISSED = 'missed';
    const RESULT_FAILED = 'failed';

    protected static $defaultName = 'app:auction:import-material-prices';

    /**
     * @var HttpClientInterface
     */
    private $client;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var MaterialRepository
     */
    private $materialRepository;

    /**
     * @param HttpClientInterface $client
     * @param EntityManagerInterface $entityManager
     * @param MaterialRepository $materialRepository
     */
    public function __construct(
        HttpClientInterface $client,
        EntityManagerInterface $entityManager,
        MaterialRepository $materialRepository
    ) {
        parent::__construct();
        $this->client = $client;
        $this->entityManager = $entityManager;
        $this->materialRepository = $materialRepository;
    }

    protected function configure(): void
    {
        $url = Config::AUCTION_PRICES_API_URL;
        $this->setDescription("Import material prices from external API $url");
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $materials = $this->materialRepository->findAll();

        $importResults = $this->importMaterialPrices($materials);
        $this->entityManager->flush();

        $this->createOutputTable($output, $importResults)->render();

        return Command::SUCCESS;
    }

    /**
     * @param Material[] $materials
     * @return string[]
     */
    private function importMaterialPrices(array $materials): array
    {
        $importResults = [];
        foreach ($materials as $material) {
            $materialName = $material->getProduct()->getName();

            try {
                $importedPrice = $this->getMaterialPriceByName($materialName);
                $materialPrice = $material->getProduct()->getAuctionPrice();

                if ($importedPrice['modification_time'] >= $materialPrice->getModificationTime()) {
                    $materialPrice->setValue($importedPrice['value']);
                    $materialPrice->setModificationTime($importedPrice['modification_time']);
                    $materialPrice->setModificationUser(null);

                    $result = self::RESULT_IMPORTED;
                } else {
                    $result = self::RESULT_MISSED;
                }
            } catch (\Exception $exception) {
                $result = self::RESULT_FAILED;
            }

            $importResults[$materialName] = $result;
        }

        return $importResults;
    }

    /**
     * @param string $materialName
     * @return array
     * @throws \Exception
     */
    private function getMaterialPriceByName(string $materialName): array
    {
        sleep(1); // Wait some time to avoid API overloading

        $response = $this->client->request(
            'GET',
            Config::AUCTION_PRICES_API_URL,
            ['query' => ['item' => $materialName]]
        );

        if ($response->getStatusCode() !== 200) {
            throw new \Exception();
        }

        $responseBody = $response->getContent();

        $records = json_decode($responseBody);
        if ($records === null) {
            throw new \Exception();
        }

        $newestRecord = end($records);
        reset($records);

        $recordDate = $this->getLocalDateTime((int)$newestRecord->date);
        $recordPrice = $newestRecord->price;

        return ['modification_time' => $recordDate, 'value' => $recordPrice];
    }

    /**
     * @param int $timestamp
     * @return \DateTime
     */
    private function getLocalDateTime(int $timestamp): \DateTime
    {
        $dateTime = new \DateTime();

        $serverTimezone = $dateTime->getTimezone();
        $apiTimezone = new \DateTimeZone('UTC');

        $dateTime->setTimezone($apiTimezone);
        $dateTime->setTimestamp($timestamp);
        $dateTime->setTimezone($serverTimezone);

        return $dateTime;
    }

    /**
     * @param OutputInterface $output
     * @param array $importResults
     * @return Table
     */
    private function createOutputTable(OutputInterface $output, array $importResults): Table
    {
        $table = new Table($output);
        $table->setHeaders(['Material', 'Result']);

        foreach ($importResults as $materialName => $importResult) {
            $table->addRow([$materialName, $importResult]);
        }

        return $table;
    }
}
