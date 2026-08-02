<?php

declare(strict_types=1);

namespace App\Infrastructure\Http\Controller;

use App\Application\Order\Command\CancelOrder\CancelOrderCommand;
use App\Application\Order\Command\CreateOrder\CreateOrderCommand;
use App\Application\Order\DTO\OrderListResponse;
use App\Application\Order\DTO\OrderResponse;
use App\Application\Order\Query\GetOrderById\GetOrderByIdQuery;
use App\Application\Order\Query\ListOrders\ListOrdersQuery;
use App\Infrastructure\Http\Request\CreateOrderRequest;
use App\Shared\Application\Command\CommandBus;
use App\Shared\Application\Query\QueryBus;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

final readonly class OrderController
{
    public function __construct(
        private CommandBus $commandBus,
        private QueryBus $queryBus,
    ) {
    }

    #[Route('/api/orders', name: 'api_orders_create', methods: ['POST'])]
    public function create(#[MapRequestPayload] CreateOrderRequest $request): JsonResponse
    {
        /** @var list<array{productSku: string, quantity: int, unitPrice: float}> $lines */
        $lines = [];
        foreach ($request->lines as $line) {
            $lines[] = [
                'productSku' => (string) ($line['productSku'] ?? ''),
                'quantity' => (int) ($line['quantity'] ?? 0),
                'unitPrice' => (float) ($line['unitPrice'] ?? 0),
            ];
        }

        /** @var OrderResponse $response */
        $response = $this->commandBus->dispatch(new CreateOrderCommand($lines));

        return new JsonResponse($response->toArray(), Response::HTTP_CREATED);
    }

    #[Route('/api/orders/{id}', name: 'api_orders_get', methods: ['GET'])]
    public function get(string $id): JsonResponse
    {
        /** @var OrderResponse $response */
        $response = $this->queryBus->ask(new GetOrderByIdQuery($id));

        return new JsonResponse($response->toArray());
    }

    #[Route('/api/orders', name: 'api_orders_list', methods: ['GET'])]
    public function list(Request $request): JsonResponse
    {
        $page = max(1, $request->query->getInt('page', 1));
        $limit = max(1, $request->query->getInt('limit', 20));

        /** @var OrderListResponse $response */
        $response = $this->queryBus->ask(new ListOrdersQuery($page, $limit));

        return new JsonResponse($response->toArray());
    }

    #[Route('/api/orders/{id}/cancel', name: 'api_orders_cancel', methods: ['POST'])]
    public function cancel(string $id): JsonResponse
    {
        /** @var OrderResponse $response */
        $response = $this->commandBus->dispatch(new CancelOrderCommand($id));

        return new JsonResponse($response->toArray());
    }
}
