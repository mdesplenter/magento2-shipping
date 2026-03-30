<?php

declare(strict_types=1);

namespace DpdConnect\Shipping\Observer;

use \Magento\Framework\Message\ManagerInterface;
use \Magento\Framework\App\RequestInterface;
use \Magento\Framework\Event\ObserverInterface;
use \Magento\Framework\Event\Observer as EventObserver;

use DpdConnect\Shipping\Services\AuthenticationService;
use DpdConnect\Sdk\Exceptions\HttpException;

use Magento\Framework\App\Config\Storage\WriterInterface;
use DpdConnect\Shipping\Services\ConfigurationValidator;

class ConfigChanged implements ObserverInterface
{
    /** @var string  */
    public const XML_PATH_ACCOUNT_VALID = 'dpdshipping/account_settings/valid_account';
    public const DOCUMENTATION_URL = 'https://integrations.dpd.nl/dpd-shipper/dpd-shipper-webservices/shipment-service-2/';

    /**
     * @param RequestInterface $request
     * @param AuthenticationService $authenticationService
     * @param ManagerInterface $messageManager
     * @param WriterInterface $configWriter
     */
    public function __construct(
        private readonly RequestInterface       $request,
        private readonly AuthenticationService  $authenticationService,
        private readonly ManagerInterface       $messageManager,
        private readonly WriterInterface        $configWriter,
        private readonly ConfigurationValidator $configurationValidator,
    ) {}

    /**
     * @throws HttpException
     */
    public function execute(EventObserver $observer): void
    {
        $requestData = $this->request->getParams();

        if(!is_null($requestData) && !empty($requestData['groups'])){
            $this->validateConfigurations($requestData['groups']);
        }
    }

    /**
     * @param array $configs
     * @return void
     * @throws HttpException
     */
    private function validateConfigurations(array $configs): void
    {
        if(!empty($configs['account_settings']['fields']) && !empty($configs['store_information']['fields']) && !empty($configs['shipping_origin']['fields'])) {
            $this->validateCredentials($configs['account_settings']['fields']);
            $this->validateShippingOrigin($configs['shipping_origin']['fields']);
            $this->validateStoreInformation($configs['store_information']['fields']);
        }
    }

    /**
     * @param array $accountConfigs
     * @return void
     * @throws HttpException
     */
    private function validateCredentials(array $accountConfigs): void
    {
        $params['user']     = $accountConfigs['username']['value'];
        $params['password'] = $accountConfigs['password']['value'];

        if($this->authenticationService->authenticate($params)){
            $this->configWriter->save(self::XML_PATH_ACCOUNT_VALID, 1);
            $this->messageManager->addSuccessMessage(__('Your credentials are valid!'));
        } else {
            $this->configWriter->save(self::XML_PATH_ACCOUNT_VALID, 0);
            $this->messageManager->addErrorMessage(__('Your credentials are NOT valid!'));
        }
    }

    /**
     * @param array $shippingOrigin
     * @return void
     */
    private function validateShippingOrigin(
        array $shippingOrigin
    ): void
    {
        self::sortGroupConfigsOnValue($shippingOrigin);

        $result['name']         = $this->configurationValidator->validateName($shippingOrigin['name1']);
        $result['country']      = $this->configurationValidator->validateISO2Code($shippingOrigin['country']);
        $result['zip_code']     = $this->configurationValidator->validateZipCode($shippingOrigin['zip_code']);
        $result['city']         = $this->configurationValidator->validateCity($shippingOrigin['city']);
        $result['house_number'] = $this->configurationValidator->validateHouseNumber($shippingOrigin['house_number']);

        foreach ($result as $key => $value) {
            if(!$value){
                $subject = ucfirst(str_replace('_', ' ', $key));
                $this->messageManager->addError(__('The value for "'.$subject.'" in group "Shipping Origin" is NOT valid!
                See <a href="'.self::DOCUMENTATION_URL.'">Documentation</a>'));
            }
        }
    }

    /**
     * @param array $storeInformation
     * @return void
     */
    private function validateStoreInformation(
        array $storeInformation
    ): void
    {
        self::sortGroupConfigsOnValue($storeInformation);

        $result['email'] = $this->configurationValidator->validateEmail($storeInformation['email']);
        $result['phone'] = $this->configurationValidator->validatePhoneNumber($storeInformation['phone']);

        $result['name']         = $this->configurationValidator->validateName($storeInformation['name']);
        $result['country']      = $this->configurationValidator->validateISO2Code($storeInformation['country']);
        $result['zip_code']     = $this->configurationValidator->validateZipCode($storeInformation['zip_code']);
        $result['city']         = $this->configurationValidator->validateCity($storeInformation['city']);
        $result['house_number'] = $this->configurationValidator->validateHouseNumber($storeInformation['house_number']);

        foreach ($result as $key => $value) {
            if(!$value){
                $subject = ucfirst(str_replace('_', ' ', $key));
                $this->messageManager->addError(__('The value for "'.$subject.'" in group "Store Information" is NOT valid!
                See <a href="'.self::DOCUMENTATION_URL.'">Documentation</a>'));
            }
        }
    }

    /**
     * @param array $configs
     * @return void
     */
    private static function sortGroupConfigsOnValue(
        array& $configs
    ): void
    {
        foreach ($configs as $key => $config){
            $configs[$key] = $config['value'] ?? $config['inherit']; // Inherit happens when a config is using "Use Website" "Use Default" - it still contains the actual value
        }
    }
}
