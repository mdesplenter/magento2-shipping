<?php
namespace DpdConnect\Shipping\Model\Attribute\Source;

use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;

class DefaultPackageTypeOptions extends AbstractSource
{
    /**
     * @return array
     */
    public function getAllOptions(): array
    {
        if (!$this->_options) {
            $this->_options[] = [
                'label' => __('Small Parcel'),
                'value' => '015010010'
            ];

            $this->_options[] = [
                'label' => __('Normal Parcel'),
                'value' => '100050050'
            ];
        }

        return $this->_options;
    }
}
