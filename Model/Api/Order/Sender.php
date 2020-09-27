<?php

namespace DigitalHub\Automation\Model\Api\Order;

use Magento\Sales\Model\Order;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\LocalizedException;
use DigitalHub\Automation\Model\Configs;
use DigitalHub\Automation\Model\Logger;
use DigitalHub\Automation\Model\Customer\IdentifierValidator;
use DigitalHub\Automation\Model\Curl;

class Sender {

    /**
     * @var CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * @var Configs
     */
    protected $configs;

    /**
     * @var IdentifierValidator
     */
    protected $identifierValidator;

    /**
     * @var Logger
     */
    protected $logger;

    /**
     * @var Curl
     */
    protected $curl;


    /**
     * Constructor
     * @param CustomerRepositoryInterface $customerRepository
     * @param Configs                     $configs
     * @param Logger                      $logger
     * @param IdentifierValidator         $identifierValidator
     * @param Curl                        $curl
     */
    public function __construct(
        CustomerRepositoryInterface $customerRepository,
        Configs $configs,
        Logger $logger,
        IdentifierValidator $identifierValidator,
        Curl $curl
    ) {
        $this->customerRepository   = $customerRepository;
        $this->configs              = $configs;
        $this->logger               = $logger;
        $this->identifierValidator  = $identifierValidator;
        $this->curl                 = $curl;
    }

    /**
     * Send order to the ERP
     * @param  Order  $order
     * @return boolean
     */
    public function sendOrder(Order $order)
    {
        $errors     = [];
        $validOrder = true;
        $cpfCnpj = false;
        $shippingAddress = $order->getShippingAddress();
        if (!$shippingAddress) {
            $shippingAddress = $order->getBillingAddress();
        }

        if (!$order->getCustomerIsGuest()) {
            try {
                $customer = $this->customerRepository->getById($order->getCustomerId());
                $cpfCnpj = $customer->getData($this->configs->getCpfCnpjField());
            } catch (NoSuchEntityException $nse) {
                $errors[] = __('Customer with id "' . $order->getCustomerId() . '" doesn\'t exists');
                $validOrder = false;
            } catch (LocalizedException $le) {
                $errors[] = __('Unable to retrieve customer from order ID: "' . $order->getId(). '"');
                $validOrder = false;
            }
        }

        if (!$cpfCnpj) {
            $cpfCnpj = $order->getCustomerTaxvat();
        }

        if ($validOrder) {
            $data = [
                'customer' => [
                    'name'          => $this->getCustomerFullName($order),
                    'telephone'     => $order->getBillingAddress()->getTelephone()
                ],
                'shipping_address' => [
                    'street'        => implode(" ", $shippingAddress->getStreet()),
                    'number'        => null, // TODO: non-native field on magento, which one use?
                    'additional'    => null, // TODO: non-native field on magento, which one use?
                    'neighborhood'  => null, // TODO: non-native field on magento, which one use?
                    'city'          => $shippingAddress->getCity(),
                    'city_ibge_code'=> null, // TODO: non-native field on magento, which one use?
                    'uf'            => $shippingAddress->getRegionCode(),
                    'country'       => $shippingAddress->getCountryId()
                ],
                'items' => [],
                'shipping_method'       => $order->getShippingMethod(),
                'payment_method'        => $order->getPayment()->getMethod(),
                'payment_installments'  => $order->getPayment()->getInstallments(),
                'subtotal'              => (float) $order->getSubtotal(),
                'shipping_amount'       => (float) $order->getShippingAmount(),
                'discount'              => (float) $order->getDiscountAmount(),
                'total'                 => (float) $order->getGrandTotal()
            ];

            foreach ($order->getAllVisibleItems() as $item) {
                $data['items'][] = [
                    'sku'   => $item->getSku(),
                    'name'  => $item->getName(),
                    'price' => $item->getPrice(),
                    'qty'   => $item->getQtyOrdered()
                ];
            }

            if ($order->getCustomerId()) {
                $data['customer']['dob'] = $this->getFormmatedDob($order->getCustomer()->getDob());
            }

            if ($this->identifierValidator->isCPF($cpfCnpj)) {
                $data['customer']['cpf_cnpj']   = $cpfCnpj;
            } else if ($this->identifierValidator->isCNPJ($cpfCnpj)) {
                $data['customer']['cpf_cnpj']   = $cpfCnpj;
                $data['customer']['cnpj']       = $cpfCnpj;

                $razaoSocialFieldName   = $this->configs->getRazaoSocialFieldName();
                $nomeFantasiaFieldName  = $this->configs->getNomeFantasiaFieldName();
                $ieFieldName            = $this->configs->getInscricaoEstadualFieldName();

                $data['customer']['razao_social']  = $razaoSocialFieldName ? $order->getData($razaoSocialFieldName) : null;
                $data['customer']['nome_fantasia'] = $nomeFantasiaFieldName ? $order->getData($nomeFantasiaFieldName) : null;
                $data['customer']['ie']            = $ieFieldName ? $order->getData($ieFieldName) : null;

                if (!$data['customer']['razao_social']) {
                    $errors[] = __('"Razao social" is a required field for CNPJ customers. Order: ' . $order->getId());
                    $validOrder = false;
                }

                if (!$data['customer']['nome_fantasia']) {
                    $errors[] = __('"Nome Fantasia" is a required field for CNPJ customers. Order: ' . $order->getId());
                    $validOrder = false;
                }
            } else {
                $validOrder = false;
                $errors[] = __('Invalid. Sending "' . $cpfCnpj . '" as CPF/CNPJ for order ' . $order->getId());
            }
        }

        if ($validOrder) {
            if ($this->_send($data)) {
                $this->logger->log(Logger::INFO, '[SUCCESS] Order withd id "' . $order->getId() . '" sent to the endpoint.');
            } else {
                $this->logger->log(Logger::INFO, '[ERROR] Faild to send Order withd id "' . $order->getId() . '".');
            }
        } else {
            $this->logger->logErrors($errors);
        }
    }

    /**
     * Get customer full name
     * @param  Order  $order
     * @return string
     */
    private function getCustomerFullName(Order $order)
    {
        $fullName  = '';
        $firstname = $order->getCustomerFirstname();
        $lastname  = $order->getCustomerLastname();

        if (!$firstname) {
            $billingAddress = $order->getBillingAddress();
            $fullName = $billingAddress->getFirstname() . ' ' . $billingAddress->getLastname();
        } else {
            $fullName = $firstname . ' ' . $lastname;
        }

        return $fullName;
    }

    /**
     * Get date formatted on dd/mm/YYYY
     * @param  string $date
     * @return string|null
     */
    private function getFormmatedDob($date)
    {
        return $date ? date("d/m/Y", strtotime($date)) : null;
    }

    /**
     * Send order to the API
     * @param  array  $data
     * @return boolean
     */
    private function _send(array $data)
    {
        $endpointUrl = $this->configs->getEndpointUrl();
        $apiKey = $this->configs->getApiKey();

        if (!$endpointUrl) {
            $endpointUrl = $this->configs->getStoreBaseUrl() . "webhook/sales";
        }

        $result = $this->curl->send($endpointUrl, 'POST', $data, $apiKey);

        if ($result) {
            return true;
        }

        return false;
    }
}
