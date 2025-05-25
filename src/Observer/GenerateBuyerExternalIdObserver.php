<?php

/*
 * (c) WEBiDEA
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tilta\CustomBuyerExternalId\Observer;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Api\AbstractSimpleObject;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\DataObject;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;

class GenerateBuyerExternalIdObserver implements ObserverInterface
{
    public function __construct(
        private readonly CustomerRepositoryInterface $customerRepository,
        private readonly ScopeConfigInterface $scopeConfig,
    ) {
    }

    public function execute(Observer $observer): void
    {
        $isEnabled = $this->scopeConfig->isSetFlag('payment/tilta/advanced/custom_buyer_external_id/enabled');
        if (!$isEnabled) {
            return;
        }

        $attributeCode = $this->scopeConfig->getValue('payment/tilta/advanced/custom_buyer_external_id/customer_attribute_code');
        if (empty($attributeCode) || !is_string($attributeCode)) {
            return;
        }

        /** @var DataObject $transport */
        $transport = $observer->getData('transport');

        /** @var int $customerId */
        $customerId = $observer->getData('customer_id');

        try {
            $customer = $this->customerRepository->getById($customerId);
        } catch (NoSuchEntityException) {
            // this should never occur, because any address needs a customer. Just to be safe.
            // ToDo if necessary: add logging
            return;
        }

        $customBuyerExternalId = $customer->getCustomAttribute($attributeCode)?->getValue();
        if (empty($customBuyerExternalId)) {
            if ($customer instanceof DataObject) {
                $customBuyerExternalId = $customer->getData($attributeCode);
            } elseif ($customer instanceof AbstractSimpleObject) {
                $customBuyerExternalId = $customer->__toArray()[$attributeCode] ?? null;
            }
        }

        if (!empty($customBuyerExternalId) && (is_string($customBuyerExternalId) || is_numeric($customBuyerExternalId))) {
            $transport->setData('buyer_external_id', $customBuyerExternalId);
            return;
        }

        $throwException = $this->scopeConfig->isSetFlag('payment/tilta/advanced/custom_buyer_external_id/throw_exception_if_missing');
        if (!$throwException) {
            throw new LocalizedException(__('Buyer external ID is not set in customer attribute %1.', $attributeCode));
        }
    }
}
