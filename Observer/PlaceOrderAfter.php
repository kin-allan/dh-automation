<?php

namespace DigitalHub\Automation\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use DigitalHub\Automation\Model\Configs;
use DigitalHub\Automation\Model\Api\Order\Sender as ApiOrderSender;

class PlaceOrderAfter implements ObserverInterface {

    /**
     * @var ApiOrderSender
     */
    protected $apiOrderSender;

    /**
     * @var Configs
     */
    protected $configs;

    /**
     * Constructor
     * @param ApiOrderSender $apiOrderSender [description]
     */
    public function __construct(
        ApiOrderSender $apiOrderSender,
        Configs $configs
    ) {
        $this->apiOrderSender = $apiOrderSender;
        $this->configs        = $configs;
    }

    /**
     * Send order data to API endpoint
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        if ($this->configs->isEnabled()) {
            /** @var $order \Magento\Sales\Model\Order */
            $order = $observer->getEvent()->getData('order');

            if ($order && $order->getId()) {
                try {
                    $this->apiOrderSender->sendOrder($order);
                } catch (\Exception $e) {}
            }
        }
    }
}
