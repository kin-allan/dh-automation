<?php

namespace DigitalHub\Automation\Controller\Sales;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\ForwardFactory;
use Magento\Framework\App\Request\InvalidRequestException;
use Magento\Framework\App\CsrfAwareActionInterface;
use Magento\Framework\Controller\Result\JsonFactory as ResultJsonFactory;
use DigitalHub\Automation\Model\Configs;

class Index extends Action implements CsrfAwareActionInterface {

    CONST TOKEN = '12341234';

    /**
     * @var Configs
     */
    protected $configs;

    /**
     * @var ForwardFactory
     */
    protected $forwardFactory;

    /**
     * @var ResultJsonFactory
     */
    protected $resultJsonFactory;

    /**
     * Constructor
     * @param Context $context
     * @param Configs $configs
     * @param ForwardFactory $forwardFactory
     */
    public function __construct(
        Context $context,
        Configs $configs,
        ForwardFactory $forwardFactory,
        ResultJsonFactory $resultJsonFactory
    )
    {
        $this->configs          = $configs;
        $this->forwardFactory   = $forwardFactory;
        $this->resultJsonFactory= $resultJsonFactory;

        parent::__construct($context);
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        if ($this->getRequest()->isPost()) {
            if ($this->configs->isEnabled()) {
                return $this->resultJsonFactory->create()->setData(['status' => true]);
            } else {
                return $this->resultJsonFactory->create()->setData(['status' => false, 'message' => __('Module is disabled.')]);
            }
        }

        $resultForward = $this->forwardFactory->create();
        return $resultForward->forward('noroute');
    }

    /**
     * {@inheritdoc}
     */
    public function createCsrfValidationException(
        RequestInterface $request
    ): ?InvalidRequestException
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function validateForCsrf(RequestInterface $request): ?bool
    {
        if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
            $auth = $_SERVER['HTTP_AUTHORIZATION'];
            $auth = explode(" ", $auth);
            if ($auth && count($auth) == 2) {
                if (trim($auth[1]) == self::TOKEN) {
                    return true;
                }
            }
        }
        return false;
    }
}
