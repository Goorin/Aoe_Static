<?php

/**
 * CallController
 * Renders the block that are requested via an ajax call
 *
 * @author Fabrizio Branca <fabrizio.branca@aoemedia.de>
 */
class Aoe_Static_CallController extends Mage_Core_Controller_Front_Action
{
    /**
     * Index action. This action is called by an ajax request
     *
     * @return void
     * @author Fabrizio Branca <fabrizio.branca@aoemedia.de>
     *
     * 2016-03-11 - Updated by Nick Rolando - Add action handle to layout load and register current product.
     */
    public function indexAction()
    {
        // if (!$this->getRequest()->isXmlHttpRequest()) { Mage::throwException('This is not an XmlHttpRequest'); }
        $response = array();
        $response['sid'] = Mage::getModel('core/session')->getEncryptedSessionId();

        $currentProductId = $this->getRequest()->getParam('currentProductId');
        if (isset($currentProductId) && !is_null($currentProductId)) {
            Mage::getSingleton('catalog/session')->setLastViewedProductId($currentProductId);
            $currentProductId = intval($currentProductId);
            $product = Mage::getModel('catalog/product')->load($currentProductId);
            if(!Mage::registry('product')) {
                Mage::register('product', $product);
            }
        }

        // Get action handle
        $actionHandle = $this->getRequest()->getParam('fullActionName');

        $this->loadLayout($actionHandle);
        $layout = $this->getLayout();

        $requestedBlockNames = $this->getRequest()->getParam('getBlocks');
        if (is_array($requestedBlockNames)) {
            foreach ($requestedBlockNames as $id => $requestedBlockName) {
                $tmpBlock = $layout->getBlock($requestedBlockName);
                if ($tmpBlock) {
                    $response['blocks'][$id] = $tmpBlock->toHtml();
                } else {
                    $response['blocks'][$id] = 'BLOCK NOT FOUND';
                }
            }
        }
        $this->getResponse()->setBody(Zend_Json::encode($response));
    }
}
