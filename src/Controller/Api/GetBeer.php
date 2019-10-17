<?php
declare(strict_types=1);

namespace App\Controller\Api;

use App\Entity\Beer;
use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\QueryBuilder;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use Knp\Component\Pager\PaginatorInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;


class GetBeer extends AbstractFOSRestController
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
     * @QueryParam(name="page", requirements="\d+", default="1", description="Page of the overview.")
     * @QueryParam(name="brewer", requirements="\s+", default="", nullable=true)
     * @QueryParam(name="name", requirements="\s+", default="", nullable=true)
     * @QueryParam(name="price_from", requirements="\s+", default="", nullable=true)
     * @QueryParam(name="price_to", requirements="\s+", default="", nullable=true)
     * @QueryParam(name="country", requirements="\s+", default="", nullable=true)
     * @QueryParam(name="type", requirements="\s+", default="", nullable=true)
     *
     * @Rest\Get("beers")
     */
    public function getBeersAction(Request $request): JsonResponse
    {
        try {
            $repository = $this->getDoctrine()->getRepository(Beer::class);
            if (!$request->getQueryString()) {
                $data = $repository->findAll();
            } else {
                $filters = [
                    'brewer' => $request->get('brewer'),
                    'name' => $request->get('name'),
                    'price_from' => $request->get('price_from'),
                    'price_to' => $request->get('price_to'),
                    'country' => $request->get('country'),
                    'type' => $request->get('type'),
                ];
                /** @var QueryBuilder $qb */
                $qb = $repository->createQueryBuilder('b');
                $expr = new Expr\Andx();

                foreach ($filters as $field => $value) {
                    if ($value !== null) {
                        switch ($field) {
                            case 'brewer':
                            case 'type':
                            case 'country':
                                $expr->add($qb->expr()->eq("b.$field", '\'' . $value . '\''));
                                break;
                            case 'price_from':
                                $expr->add($qb->expr()->gte('b.price', $value));
                                break;
                            case 'price_to':
                                $expr->add($qb->expr()->lte('b.price', $value));
                                break;
                            case 'name':
                                $expr->add($qb->expr()->like("b.$field", '\'%' . $value . '%\''));
                                break;
                        }
                    }
                }
                $qb->andWhere($expr);
                if ($request->get('page')) {
                    $data = $this->paginator->paginate(
                        $qb->getQuery(),
                        $request->query->getInt('page', 1),
                        20
                    );
                }
            }
            $response = [];
            /** @var Beer $item */
            foreach ($data as $item) {
                $response[] = $this->beerToArray($item);
            }
        } catch (\Throwable $e) {
            $this->logger->error($e);
            $response = ['error' => $e->getMessage()];
        }
        return $this->json($response);
    }

    /**
     * @QueryParam(name="productId", requirements="\d+", nullable=true)
     *
     * @Rest\Get("beer")
     */
    public function getBeerAction(Request $request): JsonResponse
    {
        try {
            $repository = $this->getDoctrine()->getRepository(Beer::class);
            $item = $repository->findOneBy([
                'productId' => $request->query->getInt('productId', 1),
            ]);
            if ($item) {
                $response = $this->beerToArray($item);
            }
        } catch (\Throwable $e) {
            $this->logger->error($e);
            $response = ['error' => $e->getMessage()];
        }
        return $this->json($response);
    }

    protected function beerToArray(Beer $item): array
    {
        return [
            'product_id' => $item->getId(),
            'name' => $item->getName(),
            'price' => $item->getPrice(),
            'beer_id' => $item->getBeerId(),
            'image_url' => $item->getImageUrl(),
            'category' => $item->getCategory(),
            'abv' => $item->getAbv(),
            'style' => $item->getStyle(),
            'attributes' => $item->getAttributes(),
            'type' => $item->getType(),
            'brewer' => $item->getBrewer(),
            'country' => $item->getCountry(),
            'on_sale' => $item->getOnSale(),
            'size' => $item->getSize(),
            'pricePerLiter' => $item->getPricePerLiter(),
        ];
    }
}

//http://127.0.0.1:8000/api/beers?page=3&brewer=3&type=asd&price_from=1&price_to=1234&name=a