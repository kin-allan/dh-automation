<?php

namespace DigitalHub\Automation\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;

class Configs {

    /**
     * Path to config value, which sets if the moduel is enabled or not
     */
    CONST XML_PATH_ENABLED = 'digitalhub_automation/general/enabled';

    /**
     * Path to config value of the api key
     */
    CONST XML_PATH_API_KEY = 'digitalhub_automation/general/api_key';

    /**
     * Path to config value for the endpoint url
     */
    CONST XML_PATH_ENDPOINT_URL = 'digitalhub_automation/general/endpoint_url';

    /**
     * Path to config to determine which fieldname is the cpf_cnpj
     */
    CONST XML_PATH_CPF_CNPJ = 'digitalhub_automation/general/cpf_cnpj_fieldname';

    /**
     * Path to config to determine which fieldname is the "razao social"
     */
    CONST XML_PATH_RAZAO_SOCIAL = 'digitalhub_automation/general/razao_social_fieldname';

    /**
     * Path to config to determine which fieldname is the "nome fantasia"
     */
    CONST XML_PATH_NOME_FANTASIA = 'digitalhub_automation/general/nome_fantasia_fieldname';

    /**
     * Path to config to determine which fieldname is the "inscrição estadual"
     */
    CONST XML_PATH_INSCRICAO_ESTADUAL = 'digitalhub_automation/general/ie_fieldname';

    /**
     * @param ScopeConfigInterface $scopeConfig
     */
    protected $scopeConfig;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * Config constructor.
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        StoreManagerInterface $storeManager
    ) {
        $this->scopeConfig  = $scopeConfig;
        $this->storeManager = $storeManager;
    }

    /**
     * Check if the moduel is enabled
     * @return boolean
     */
    public function isEnabled()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_ENABLED, ScopeInterface::SCOPE_STORE) == 1;
    }

    /**
     * Return the API Key
     * @return string|null
     */
    public function getApiKey()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_API_KEY, ScopeInterface::SCOPE_STORE);
    }

    /**
     * Get the endpoint URL that should send the order data
     * @return string|null
     */
    public function getEndpointUrl()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_ENDPOINT_URL, ScopeInterface::SCOPE_STORE);
    }

    /**
     * Return field name that represents the "cpf/cnpj"
     * @return string
     */
    public function getCpfCnpjFieldName()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_CPF_CNPJ, ScopeInterface::SCOPE_STORE);
    }

    /**
     * Return field name that represents the "razao social"
     * @return string
     */
    public function getRazaoSocialFieldName()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_RAZAO_SOCIAL, ScopeInterface::SCOPE_STORE);
    }

    /**
     * Return field name that represents the "nome fantasia"
     * @return string
     */
    public function getNomeFantasiaFieldName()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_NOME_FANTASIA, ScopeInterface::SCOPE_STORE);
    }

    /**
     * Return field name that represents the "ie (inscrição estadual)"
     * @return string
     */
    public function getInscricaoEstadualFieldName()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_INSCRICAO_ESTADUAL, ScopeInterface::SCOPE_STORE);
    }

    /**
     * Return store base url;
     * @return string
     */
    public function getStoreBaseUrl()
    {
        return $this->storeManager->getStore()->getBaseUrl();
    }
}
