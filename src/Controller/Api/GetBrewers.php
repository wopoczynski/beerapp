<?php
declare(strict_types=1);

namespace App\Controller\Api;

use FOS\RestBundle\Controller\AbstractFOSRestController;
use Knp\Component\Pager\PaginatorInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use FOS\RestBundle\Controller\Annotations as Rest;


class GetBrewers extends AbstractFOSRestController
{
    /**
     * @var LoggerInterface
     */
    protected $logger;
    /**
     * @var PaginatorInterface
     */
    protected $paginator;

    public function __construct(LoggerInterface $logger, PaginatorInterface $paginator)
    {
        $this->logger = $logger;
        $this->paginator = $paginator;
    }
    /**
     * @Rest\Get("brewers")
     */
    public function getBrewersAction(): JsonResponse
    {
        try {
            $em = $this->getDoctrine()->getManager();
            $query = "SELECT b.id, b.name, COUNT(beer_id) AS beers_total  FROM brewer b
                        INNER JOIN brewer_to_beer b2b ON b.id = b2b.brewer_id
                        GROUP BY brewer_id;";
            $stmt = $em->getConnection()->prepare($query);
            $stmt->execute();
            $response = $stmt->fetchAll();
        } catch (\Throwable $e) {
            $this->logger->error($e);
            $response = ['error' => $e->getMessage()];
        }
        return $this->json($response);
    }
}