<?php

declare(strict_types=1);

namespace Ecommerce121\CustomerCompanyAttribute\Observer;

use Exception;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\OrderRepository;
use Psr\Log\LoggerInterface;

class SetCompanyFromShippingAddress implements ObserverInterface
{
    private LoggerInterface $logger;

    private OrderRepository $orderRepository;

    /**
     * @param OrderRepository $orderRepository
     * @param LoggerInterface $logger
     */
    public function __construct(
        OrderRepository $orderRepository,
        LoggerInterface $logger
    ) {
        $this->logger = $logger;
        $this->orderRepository = $orderRepository;
    }

    /**
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        try {
            /** @var Order $order */
            $order = $observer->getEvent()->getOrder();
            $orderShippingAddress = $order->getShippingAddress();

            if ($company = $orderShippingAddress->getCompany()) {
                $order->getBillingAddress()->setCompany($company);
                $this->orderRepository->save($order);
            }
        } catch (Exception $e) {
            $this->logger->critical($e->getMessage());
        }
    }
}
