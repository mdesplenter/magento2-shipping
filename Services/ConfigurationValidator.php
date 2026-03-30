<?php

declare(strict_types=1);

namespace DpdConnect\Shipping\Services;

use Magento\Directory\Model\CountryFactory;

class ConfigurationValidator
{
    /**
     * @param CountryFactory $countryFactory
     */
    public function __construct(
        private readonly CountryFactory $countryFactory,
    ) {}

    /**
     * @param string $iso2code
     * @return bool
     */
    public function validateISO2Code(string $iso2code): bool
    {
        if(strlen($iso2code) != 2) {
            return false;
        }

        $country = $this->countryFactory->create()->loadByCode($iso2code);
        return (bool)$country->getCountryId();
    }

    public function validateName(string $name): bool
    {
        return (bool)(strlen($name) <= 35);
    }

    /**
     * @param string $zipcode
     * @return bool
     */
    public function validateZIPCode(string $zipcode): bool
    {
        return (bool)(strlen($zipcode) <= 9);
    }

    /**
     * @param string $houseNumber
     * @return bool
     */
    public function validateHouseNumber(string $houseNumber): bool
    {
        return (bool)(strlen($houseNumber) <= 8);
    }

    /**
     * @param string $city
     * @return bool
     */
    public function validateCity(string $city): bool
    {
        return (bool)(strlen($city) <= 35);
    }

    /**
     * @param string $email
     * @return bool
     */
    public function validateEmail(string $email): bool
    {
        return (bool)(strlen($email) <= 50);
    }

    /**
     * @param string $phoneNumber
     * @return bool
     */
    public function validatePhoneNumber(string $phoneNumber): bool
    {
        return (bool)(strlen($phoneNumber) <= 30);
    }
}
