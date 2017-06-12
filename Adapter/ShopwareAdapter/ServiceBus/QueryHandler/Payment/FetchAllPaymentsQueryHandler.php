<?php

namespace ShopwareAdapter\ServiceBus\QueryHandler\Payment;

use PlentyConnector\Connector\ServiceBus\Query\Payment\FetchAllPaymentsQuery;
use PlentyConnector\Connector\ServiceBus\Query\QueryInterface;
use PlentyConnector\Connector\ServiceBus\QueryHandler\QueryHandlerInterface;
use Shopware\Components\Api\Resource\Order as OrderResource;
use Shopware\Models\Order\Status;
use ShopwareAdapter\ResponseParser\Payment\PaymentResponseParserInterface;
use ShopwareAdapter\ShopwareAdapter;

/**
 * Class FetchAllPaymentsQueryHandler
 */
class FetchAllPaymentsQueryHandler implements QueryHandlerInterface
{
    /**
     * @var PaymentResponseParserInterface
     */
    private $responseParser;

    /**
     * @var OrderResource
     */
    private $orderResource;

    /**
     * FetchAllPaymentsQueryHandler constructor.
     *
     * @param PaymentResponseParserInterface $responseParser
     * @param OrderResource                  $orderResource
     */
    public function __construct(PaymentResponseParserInterface $responseParser, OrderResource $orderResource)
    {
        $this->responseParser = $responseParser;
        $this->orderResource = $orderResource;
    }

    /**
     * {@inheritdoc}
     */
    public function supports(QueryInterface $query)
    {
        return $query instanceof FetchAllPaymentsQuery &&
            $query->getAdapterName() === ShopwareAdapter::NAME;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(QueryInterface $query)
    {
        $filter = [
            [
                'property' => 'status',
                'expression' => '=',
                'value' => Status::ORDER_STATE_OPEN,
            ],
            [
                'property' => 'cleared',
                'expression' => '=',
                'value' => Status::PAYMENT_STATE_COMPLETELY_PAID,
            ],
        ];

        $orders = $this->orderResource->getList(0, null, $filter);

        foreach ($orders['data'] as $order) {
            $order = $this->orderResource->getOne($order['id']);

            $result = $this->responseParser->parse($order);

            if (empty($result)) {
                continue;
            }

            $parsedElements = array_filter($result);

            foreach ($parsedElements as $parsedElement) {
                yield $parsedElement;
            }
        }
    }
}
