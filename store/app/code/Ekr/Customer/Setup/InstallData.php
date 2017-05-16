<?php
namespace Ekr\Customer\Setup;

use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Eav\Model\Config;

class InstallData implements InstallDataInterface
{
    /**
     * Customer setup factory
     *
     * @var \Magento\Customer\Setup\CustomerSetupFactory
     */
    private $eavSetupFactory;
    private $eavConfig;
    
    /**
     * construct method
     * @param EavSetupFactory $eavSetupFactory [description]
     */
    public function __construct(\Magento\Eav\Setup\EavSetupFactory $eavSetupFactory, Config $eavConfig)
    {
        $this->eavSetupFactory = $eavSetupFactory;
        $this->eavConfig = $eavConfig;
    }

    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        // account approved status
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
        
        $eavSetup->addAttribute(
            \Magento\Customer\Model\Customer::ENTITY,
            'ekr_account_status',
            [
                'type' => 'int',
                'label' => 'Account Approved',
                'input' => 'select',
                'source' => 'Magento\Eav\Model\Entity\Attribute\Source\Boolean',
                'required' => false,
                'default' => '0',
                'sort_order' => 100,
                'system' => false,
                'position' => 100,
                'option' => [
                    'values' =>[
                        0 => 'Inactive',
                        1 => 'Approved',
                    ],
                ],
            ]
        );

        $sampleAttribute = $this->eavConfig->getAttribute(\Magento\Customer\Model\Customer::ENTITY, 'ekr_account_status');
        $sampleAttribute->setData('used_in_forms',['adminhtml_customer']);
        $sampleAttribute->save();

        // parent account
        $eavSetup->addAttribute(
            \Magento\Customer\Model\Customer::ENTITY,
            'ekr_account_parent',
            [
                'type' => 'int',
                'label' => 'Account Parent',
                'input' => 'select',
                'source' => 'Ekr\Customer\Source\CustomerParent',
                'required' => false,
                'default' => '0',
                'sort_order' => 101,
                'system' => false,
                'position' => 101
            ]
        );
        $sampleAttribute = $this->eavConfig->getAttribute(\Magento\Customer\Model\Customer::ENTITY, 'ekr_account_parent');
        $sampleAttribute->setData(
            'used_in_forms',
            /*['adminhtml_customer_address', 'customer_address_edit', 'customer_register_address']*/
            ['adminhtml_customer']
        );
        $sampleAttribute->save();

        // is parent account
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
        $eavSetup->addAttribute(
            \Magento\Customer\Model\Customer::ENTITY,
            'ekr_is_parent_account',
            [
                'type' => 'int',
                'label' => 'Is Parent Account',
                'input' => 'select',
                'source' => 'Magento\Eav\Model\Entity\Attribute\Source\Boolean',
                'required' => false,
                'default' => '0',
                'sort_order' => 102,
                'system' => false,
                'position' => 102,
                'option' => [
                    'values' =>[
                        0 => 'Simple Account',
                        1 => 'Parent Account',
                    ],
                ],
            ]
        );

        // store name
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
        $eavSetup->addAttribute(
            \Magento\Customer\Model\Customer::ENTITY,
            'ekr_store_name',
            [
                'type' => 'varchar',
                'label' => 'Store Name',
                'input' => 'text',
                'required' => false,
                'default' => '0',
                'sort_order' => 103,
                'system' => false,
                'position' => 103,
            ]
        );

        $sampleAttribute = $this->eavConfig->getAttribute(\Magento\Customer\Model\Customer::ENTITY, 'ekr_store_name');
        $sampleAttribute->setData(
            'used_in_forms',
            ['adminhtml_customer',"checkout_register","customer_account_create","customer_account_edit","adminhtml_checkout"]
        );
        $sampleAttribute->save();

        // order approval.
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
        $eavSetup->addAttribute(
            \Magento\Customer\Model\Customer::ENTITY,
            'ekr_parent_approval',
            [
                'type' => 'int',
                'label' => 'Orders Approved by Parent Account?',
                'input' => 'select',
                'source' => 'Magento\Eav\Model\Entity\Attribute\Source\Boolean',
                'required' => false,
                'default' => '0',
                'sort_order' => 104,
                'system' => false,
                'position' => 104,
            ]
        );

        $sampleAttribute = $this->eavConfig->getAttribute(\Magento\Customer\Model\Customer::ENTITY, 'ekr_parent_approval');
        $sampleAttribute->setData(
            'used_in_forms',
            /*['adminhtml_customer_address', 'customer_address_edit', 'customer_register_address']*/
            ['adminhtml_customer']
        );
        $sampleAttribute->save();

        // customer price multiplier.
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
        $eavSetup->addAttribute(
            \Magento\Customer\Model\Customer::ENTITY,
            'ekr_price_multiplier',
            [
                'type' => 'varchar',
                'label' => 'Price Multiplier',
                'input' => 'text',
                'required' => false,
                'default' => '0',
                'sort_order' => 105,
                'system' => false,
                'position' => 105,
            ]
        );

        $sampleAttribute = $this->eavConfig->getAttribute(\Magento\Customer\Model\Customer::ENTITY, 'ekr_price_multiplier');
        $sampleAttribute->setData(
            'used_in_forms',
            /*['adminhtml_customer_address', 'customer_address_edit', 'customer_register_address']*/
            ['adminhtml_customer']
        );
        $sampleAttribute->save();
    }
}
