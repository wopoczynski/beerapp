<?php
declare(strict_types=1);

namespace App\Service;


use App\Entity\Beer;
use App\Entity\Brewer;
use App\Entity\BrewerToBeer;
use App\Repository\BeerRepository;
use App\Repository\BrewerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpClient\HttpClient;

class BeerApiImporter implements Importer
{
    private const URL = 'http://ontariobeerapi.ca/beers/';
    private const INT_PARSED = 1;
    private const L_MULTIPLER = 1000;
    /**
     * @var EntityManagerInterface
     */
    private $em;
    /**
     * @var
     */
    private $output;

    public function __construct(EntityManagerInterface $em, LoggerInterface $logger)
    {
        $this->em = $em;
    }

    /**
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    public function import()
    {
        $response = $this->getData();
        $this->parseResponse($response);
    }

    /**
     * @return string
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    protected function getData(): string
    {
        $client = HttpClient::create();
        $response = $client->request('GET', self::URL);
        return $response->getContent();
    }

    protected function parseResponse(string $response)
    {
        $json = json_decode($response);
        $uniqueBrewers = $this->getUniqueBrewers();
        $beerRepository = $this->em->getRepository(Beer::class);
                $progressBar = new ProgressBar($this->output, count($json));
        $progressBar->start();
        $this->em->beginTransaction();
        foreach ($json as $item) {
            if (!isset($uniqueBrewers[$item->brewer])) {
                $brewer = new Brewer();
                $brewer->setName($item->brewer);
                $this->em->persist($brewer);
                $this->em->flush();
                $uniqueBrewers[$item->brewer] = $brewer->getId();
            }
            $found = $beerRepository->findBy(['productId' => $item->product_id],['id' => 'ASC'], ['limit' => 1]);
            if(!$found) {
                $product = new Beer();
                $product->setProductId($item->product_id)
                        ->setName($item->name)
                        ->setPrice($item->price)
                        ->setBeerId($item->beer_id)
                        ->setImageUrl($item->image_url)
                        ->setCategory($item->category)
                        ->setAbv($item->abv)
                        ->setStyle($item->style)
                        ->setAttributes($item->attributes)
                        ->setType($item->type)
                        ->setBrewer($item->brewer)
                        ->setCountry($item->country)
                        ->setOnSale((string)$item->on_sale)
                        ->setSize($item->size)
                        ->setPricePerLiter($this->calculatePricePerLiter($product));
                $this->em->persist($product);
                $this->em->flush();
                $relation = new BrewerToBeer();
                $relation
                    ->setBrewerId($uniqueBrewers[$item->brewer])
                    ->setBeerId($product->getId());
                $this->em->persist($relation);
                $this->em->flush();
            }
            $progressBar->advance();
        }
        $this->em->commit();
        $progressBar->finish();
    }

    public function calculatePricePerLiter(Beer $beer): float
    {
        preg_match('~[\D]*[\d]+[\D]+([\d]+)~', $beer->getSize(), $price);
        preg_match('~([0-9]+) .* ~', $beer->getSize(), $amount);
        return (float)($beer->getPrice() / ($price[self::INT_PARSED] * $amount[self::INT_PARSED] / self::L_MULTIPLER));
    }

    public function setOutput(OutputInterface $output): self
    {
        $this->output = $output;
        return $this;
    }

    protected function getUniqueBrewers(): array
    {
        $unique = [];
        /** @var Brewer $brewer */
        foreach($this->em->getRepository(Brewer::class)->findAll() as $brewer){
            $unique[$brewer->getName()] = $brewer->getId();
        }
        return $unique;
    }
}