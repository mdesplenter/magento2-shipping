<?php

declare(strict_types=1);

namespace DpdConnect\Shipping\Services;

use DpdConnect\Sdk\ClientBuilder;
use DpdConnect\Sdk\Exceptions\AuthenticateException;
use DpdConnect\Sdk\Exceptions\HttpException;
use DpdConnect\Sdk\Objects\MetaData;
use DpdConnect\Sdk\Objects\ObjectFactory;
use DpdConnect\Shipping\Helper\DpdSettings;
use Magento\Framework\App\ProductMetadataInterface;
use Magento\Framework\Encryption\Encryptor;
use Magento\Framework\Module\ModuleListInterface;
use DpdConnect\Shipping\Helper\DPDClient;
use Magento\Store\Model\ScopeInterface;
use DpdConnect\Sdk\CacheWrapper;
use Magento\Store\Model\StoreManagerInterface;

class AuthenticationService
{
    /** @var string  */
    public const ADMIN_RESOURCE = 'Magento_Catalog::config_catalog';

    /**
     * @param StoreManagerInterface $storeManager
     * @param DpdSettings $dpdSettings
     * @param ModuleListInterface $moduleList
     * @param ProductMetadataInterface $productMetadata
     * @param Encryptor $crypt
     */
    public function __construct(
        private readonly StoreManagerInterface $storeManager,
        private readonly DpdSettings $dpdSettings,
        private readonly ModuleListInterface $moduleList,
        private readonly ProductMetadataInterface $productMetadata,
        private readonly Encryptor $crypt
    ) {}

    /**
     * @param array $options
     * @param bool $ignoreCache
     * @return bool
     * @throws HttpException
     */
    public function authenticate(
        array $options,
        bool $ignoreCache = true
    ): bool
    {
        $storeCode = $this->storeManager->getStore()->getCode();
        $url = $this->dpdSettings->getValue(DpdSettings::API_ENDPOINT);

        $user     = $options['user'];
        $password = ($options['password'] === '******' && !is_null($this->dpdSettings->getValue(DpdSettings::ACCOUNT_PASSWORD,ScopeInterface::SCOPE_STORE, $storeCode)))
            ? $this->crypt->decrypt($this->dpdSettings->getValue(DpdSettings::ACCOUNT_PASSWORD,ScopeInterface::SCOPE_STORE, $storeCode))
            : $options['password'];

        $pluginVersion = $this->moduleList->getOne(DPDClient::MODULE_NAME)['setup_version'];

        $clientBuilder = new ClientBuilder($url, ObjectFactory::create(MetaData::class, [
            'webshopType'    => $this->productMetadata->getName() . ' ' . $this->productMetadata->getEdition(),
            'webshopVersion' => $this->productMetadata->getVersion(),
            'pluginVersion'  => $pluginVersion,
        ]));

        try{
            $cacheWrapper = new CacheWrapper();
            $result       = $cacheWrapper->getCachedList($user, false, 'dpd_token');
            $tokenClass   = $clientBuilder->buildAuthenticatedByPassword($user, $password)->getToken();

            if($ignoreCache && $result) {
                $cacheWrapper->storeCachedList(false, $user, 'dpd_token');
            }

            $tokenString = $tokenClass->getPublicJWTToken($user, $password);
            return $this->isTokenValid($tokenString);
        } catch (AuthenticateException) {
            //...
        }

        return false;
    }

    /**
     * @param string $token
     * @return bool
     * @DOC Mostly taken from \DpdConnect\Sdk\Resources\Token::isTokenValid(), its just private.
     */
    private function isTokenValid(string $token): bool
    {
        $explodedToken = explode('.', $token);

        // Check if token has header, payload and signature
        if (count($explodedToken) != 3) {
            return false;
        }

        list($header, $payload, $signature) = $explodedToken;

        $payload = json_decode(base64_decode($payload), true);

        // Check if token is expired
        // Subtract 5 minutes of token to prevent returning a shortly-expiring token
        if (time() >= (int)$payload['exp'] - (5*60)) {
            return false;
        }

        return true;
    }
}
