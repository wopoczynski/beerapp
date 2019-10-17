<?php
declare(strict_types=1);

namespace App\Controller\Console;

use App\Service\BeerApiImporter;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Contracts\HttpClient\Exception\HttpExceptionInterface;

class ImportCommand extends Command
{
    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var BeerApiImporter
     */
    protected $beerApiImporter;

    public function __construct(BeerApiImporter $beerApiImporter, LoggerInterface $logger, $name = null)
    {
        $this->beerApiImporter = $beerApiImporter;
        $this->logger = $logger;
        parent::__construct($name);
    }

    protected function configure()
    {
        $this->setName('import')
             ->setDescription('import beer list from http://ontariobeerapi.ca/');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $this->beerApiImporter->setOutput($output);
            $this->beerApiImporter->import();
        } catch (HttpExceptionInterface $e){
            $this->logger->warning($e);
            $output->writeln($e->getMessage());
        } catch (\Throwable $e) {
            $this->logger->error($e);
            $output->writeln($e->getMessage());
        }
    }

}