<?php

namespace App\Command\Auction;

use App\Config;
use App\Entity\Catalog\Material;
use App\Repository\Catalog\MaterialRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
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

    /**
     *
     */
    protected function configure(): void
    {
        $url = Config::AUCTION_PRICES_API_URL;
        $this->setDescription("Import material prices from external API <href=$url>$url</>");
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $url = Config::AUCTION_PRICES_API_URL;
        $output->writeln("Importing prices from <href=$url>$url</>...");

        $materials = $this->materialRepository->findAll();

        $this->importMaterialPrices($materials, function ($materialName, $result) use ($output) {
            $this->writeImportLine($output, $materialName, $result);
        });

        $this->entityManager->flush();

        return Command::SUCCESS;
    }

    /**
     * @param Material[] $materials
     * @param callable $callback
     */
    private function importMaterialPrices(array $materials, callable $callback): void
    {
        foreach ($materials as $material) {
            $materialName = $material->getProduct()->getName();

            try {
                $importedPrice = $this->importMaterialPriceByName($materialName);
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

            $callback($materialName, $result);
        }
    }

    /**
     * @param string $materialName
     * @return array
     * @throws \Exception
     */
    private function importMaterialPriceByName(string $materialName): array
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
        if (empty($records)) { // There can be invalid JSON or empty JSON array
            throw new \Exception();
        }

        $newestRecord = end($records);
        reset($records);

        $timestamp = (int)$newestRecord->date;
        if ($timestamp === 0) { // There can be dummy record with zero time and price
            throw new \Exception();
        }

        return [
            'modification_time' => $this->convertTimestampToDateTime($timestamp),
            'value' => (int)$newestRecord->price ?: null
        ];
    }

    /**
     * @param int $timestamp
     * @return \DateTime
     */
    private function convertTimestampToDateTime(int $timestamp): \DateTime
    {
        $dateTime = new \DateTime();
        $dateTime->setTimestamp($timestamp);

        $consoleDateTime = new \DateTime();
        $emulatedDateTime = new \DateTime('now', new \DateTimeZone(Config::CONSOLE_TIMEZONE));

        // Since the ORM reads DateTime field from the database in a string form, in case of incorrect timezone
        // configured for PHP, the timestamp of the field is incorrect and has extra offset. So for the correct
        // comparison and saving to the database, the same offset should be added to the new DateTime value.
        //
        // For example, if PHP uses UTC timezone but the database uses Europe/Kiev, ORM reads Europe/Kiev time
        // as a UTC time adding extra offset 10800 (in summer) to the timestamp
        $emulatedOffset = $emulatedDateTime->getOffset() - $consoleDateTime->getOffset();

        $dateTime->setTimestamp($timestamp + $emulatedOffset);

        return $dateTime;
    }

    /**
     * @param OutputInterface $output
     * @param string $materialName
     * @param string $result
     */
    private function writeImportLine(OutputInterface $output, string $materialName, string $result): void
    {
        $output->write("$materialName: ");

        switch ($result) {
            case self::RESULT_IMPORTED:
                $output->writeln('<info>imported</info>');
                break;
            case self::RESULT_MISSED:
                $output->writeln('<comment>missed</comment>');
                break;
            case self::RESULT_FAILED:
            default:
                $output->writeln('<error>failed</error>');
        }
    }
}
