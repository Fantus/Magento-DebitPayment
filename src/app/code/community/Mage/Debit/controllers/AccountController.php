<?php
/**
 * This file is part of the Mage_Debit module.
 *
 * PHP version 5
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category  Mage
 * @package   Mage_Debit
 * @author    Rouven Alexander Rieker <rouven.rieker@itabs.de>
 * @copyright 2012 ITABS GmbH / Rouven Alexander Rieker (http://www.itabs.de)
 * @copyright 2010 Phoenix Medien GmbH & Co. KG (http://www.phoenix-medien.de)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      http://www.magentocommerce.com/extension/676/
 */
/**
 * AccountController
 *
 * @category  Mage
 * @package   Mage_Debit
 * @author    Rouven Alexander Rieker <rouven.rieker@itabs.de>
 * @copyright 2012 ITABS GmbH / Rouven Alexander Rieker (http://www.itabs.de)
 * @copyright 2010 Phoenix Medien GmbH & Co. KG (http://www.phoenix-medien.de)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      http://www.magentocommerce.com/extension/676/
 */
class Mage_Debit_AccountController extends Mage_Core_Controller_Front_Action
{
    /**
     * Retrieve customer session object
     *
     * @return Mage_Customer_Model_Session
     */
    protected function _getSession()
    {
        return Mage::getSingleton('customer/session');
    }

    /**
     * preDispatch
     *
     * @return void
     */
    public function preDispatch()
    {
        parent::preDispatch();

        if (!Mage::getSingleton('customer/session')->authenticate($this)) {
            $this->setFlag('', 'no-dispatch', true);
        }
    }

    /**
     * editAction
     *
     * @return void
     */
    public function editAction()
    {
        $this->loadLayout();
        $this->getLayout()->getBlock('head')->setTitle($this->__('Debit Account Data'));
        $this->renderLayout();
    }

    /**
     * saveAction
     *
     * @throws Mage_Core_Exception
     * @throws Exception
     * @return void
     */
    public function saveAction()
    {
        $customer = Mage::getSingleton('customer/session')->getCustomer();
        if (!$customer) {
            return;
        }

        $now = Mage::app()->getLocale()->date()->toString(Varien_Date::DATETIME_INTERNAL_FORMAT);
        $customer->setData('debit_payment_acount_update', $now);
        $customer->setData('debit_payment_acount_name', $this->getRequest()->getPost('account_name'));
        $customer->setData('debit_payment_acount_number', $this->getRequest()->getPost('account_number'));
        $customer->setData('debit_payment_acount_blz', $this->getRequest()->getPost('account_blz'));

        try {
            $customer->save();
            $this->_getSession()->setCustomer($customer)
                ->addSuccess($this->__('Debit account information was successfully saved'));
            $this->_redirect('customer/account');

            return;
        } catch (Mage_Core_Exception $e) {
            $this->_getSession()->setCustomerFormData($this->getRequest()->getPost())
                ->addError($e->getMessage());
        } catch (Exception $e) {
            $this->_getSession()->setCustomerFormData($this->getRequest()->getPost())
                ->addException($e, $this->__('Can\'t save customer'));
        }
    }
}
