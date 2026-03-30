<?php
/**
 * This file is part of the Magento 2 Shipping module of DPD Nederland B.V.
 *
 * Copyright (C) 2019  DPD Nederland B.V.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */
namespace DpdConnect\Shipping\Block;

class ParcelshopInfo extends \Magento\Framework\View\Element\Template
{
    private $parcelshop;
    private $quote;
    private $countryFactory;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Directory\Model\CountryFactory $countryFactory
    ) {
        parent::__construct($context);
        $this->countryFactory = $countryFactory;
    }

    public function setParcelshop($parcelshop)
    {
        $this->parcelshop = $parcelshop;
    }

    public function getParcelshop()
    {
        return $this->parcelshop;
    }

    public function setQuote($quote)
    {
        $this->quote = $quote;
    }

    public function getQuote()
    {
        return $this->quote;
    }

    public function getCountry($countryCode)
    {
        $country = $this->countryFactory->create()->loadByCode($countryCode);
        return $country->getName();
    }

    /**
     * Render the html for openinghours. (to keep template files clean from to much functional php)
     *
     * @return string
     */
    public function getOpeningHoursHtml()
    {
        $html = "";
        foreach ($this->parcelshop['openingHours'] ?? [] as $openinghours) {
            $openMorning = $openinghours['openMorning'] ?? '';
            $closeMorning = $openinghours['closeMorning'] ?? '';
            $openAfternoon = $openinghours['openAfternoon'] ?? '';
            $closeAfternoon = $openinghours['closeAfternoon'] ?? '';

            $isMorningClosed   = ($openMorning == '00:00' && $closeMorning == '00:00');
            $isAfternoonClosed = ($openAfternoon == '00:00' && $closeAfternoon == '00:00');

            // Parcel Locker logic: Checks for 00:00 or 00:01 start and 23:59 end
            $morningFull   = (in_array($openMorning, ['00:00', '00:01']) && $closeMorning == '23:59');
            $afternoonFull = (in_array($openAfternoon, ['00:00', '00:01']) && $closeAfternoon == '23:59');
            $isParcelLocker = $morningFull || $afternoonFull;

            $html .= '<tr>';

            $html .= '<td style="padding: 3px; border: none;"></td>';
            $html .= '<td style="padding: 3px; width: 25%;"><strong>' . $this->_escaper->escapeHtml(__(strtolower($openinghours['weekday']))) . '</strong></td>';

            // Time Columns Logic
            if ($isParcelLocker) {
                $html .= '<td style="padding: 3px; text-align: center;">' . $this->_escaper->escapeHtml(__('Whole day')) . '</td>';
                $html .= '<td style="padding: 3px; text-align: center;"> </td>';
            } elseif ($isMorningClosed && $isAfternoonClosed) {
                $html .= '<td style="padding: 3px; text-align: center;">' . $this->_escaper->escapeHtml(__('Closed')) . '</td>';
                $html .= '<td style="padding: 3px; text-align: center;"> </td>';
            } elseif ($isMorningClosed && !$isAfternoonClosed) {
                $html .= '<td style="padding: 3px; width: 25%; text-align: center;">' . $this->_escaper->escapeHtml($openAfternoon) . ' - ' . $this->_escaper->escapeHtml($closeAfternoon) . '</td>';
                $html .= '<td style="padding: 3px; width: 25%; text-align: center;"></td>';
            } elseif (!$isMorningClosed && $isAfternoonClosed) {
                $html .= '<td style="padding: 3px; width: 25%; text-align: center;">' . $this->_escaper->escapeHtml($openMorning) . ' - ' . $this->_escaper->escapeHtml($closeMorning) . '</td>';
                $html .= '<td style="padding: 3px; width: 25%; text-align: center;"></td>';
            } else {
                $html .= '<td style="padding: 3px; width: 25%; text-align: center;">' . $this->_escaper->escapeHtml($openMorning) . ' - ' . $this->_escaper->escapeHtml($closeMorning) . '</td>';
                $html .= '<td style="padding: 3px; width: 25%; text-align: center;">' . $this->_escaper->escapeHtml($openAfternoon) . ' - ' . $this->_escaper->escapeHtml($closeAfternoon) . '</td>';
            }

            $html .= '</tr>';
        }

        return $html;
    }
}
