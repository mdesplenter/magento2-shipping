<?php

declare(strict_types=1);

namespace DpdConnect\Shipping\Controller\Adminhtml\Search\System\Config;

use DpdConnect\Sdk\ClientBuilder;
use DpdConnect\Sdk\Common\HttpClient;
use DpdConnect\Sdk\Exceptions\AuthenticateException;
use DpdConnect\Sdk\Objects\MetaData;
use DpdConnect\Sdk\Objects\ObjectFactory;
use DpdConnect\Shipping\Helper\DpdCache;
use DpdConnect\Shipping\Helper\DpdSettings;
use DpdConnect\Shipping\Services\AuthenticationService;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\AdvancedSearch\Model\Client\ClientResolver;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\ProductMetadataInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Encryption\Encryptor;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filter\StripTags;
use Magento\Framework\Module\ModuleListInterface;
use DpdConnect\Shipping\Helper\DPDClient;
use Magento\Store\Api\StoreManagementInterface;
use Magento\Store\Model\ScopeInterface;
use DpdConnect\Sdk\CacheWrapper;
use Magento\Store\Model\StoreManagement;
use Magento\Store\Model\StoreManagerInterface;

class TestConnection extends Action implements HttpPostActionInterface
{
    /** @var string  */
    public const ADMIN_RESOURCE = 'Magento_Catalog::config_catalog';

    /**
     * @param Context $context
     * @param AuthenticationService $authenticationService
     * @param JsonFactory $resultJsonFactory
     * @param StripTags $tagFilter
     */
    public function __construct(
        Context $context,
        private readonly AuthenticationService $authenticationService,
        private readonly JsonFactory $resultJsonFactory,
        private readonly StripTags $tagFilter,
    ) {
        parent::__construct($context);
    }

    /**
     * Check for connection to server
     *
     * @return Json
     */
    public function execute()
    {
        $options = $this->getRequest()->getParams();

        $result = [
            'success' => false,
            'errorMessage' => '',
        ];

        try {
            if (empty($options['user']) || empty($options['password'])) {
                $result['errorMessage'] = 'Credentials Missing';
            } else {
                $result['success'] = $this->authenticationService->authenticate($options);
            }
        } catch (\Exception $e) {
            $message = __($e->getMessage());
            $result['errorMessage'] = $this->tagFilter->filter($message);
        }

        $resultJson = $this->resultJsonFactory->create();
        return $resultJson->setData($result);
    }
}
