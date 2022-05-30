<?php

namespace Ziffity\ModelPopup\Block;

use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\View\Element\Template;
use Magento\Customer\Model\Session;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory;
use Magento\Store\Model\StoreManagerInterface;

class Model extends Template
{
    const XML_MESSAGE   = 'extension/general/popup';

    /**
     * @var Session
     */
    protected $customerSession;

    /**
     * @var CollectionFactory
     */
    protected $orderCollection;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    const XML_IMAGE     = 'extension/general/image';

    /**
     * @param Session $customerSession
     */
    public function __construct(
        Context $context,
        Session $customerSession,
        CollectionFactory $orderCollection,
        StoreManagerInterface $storeManager,
        array $data = [])
    {
        $this->orderCollection = $orderCollection;
        $this->customerSession = $customerSession;
        $this->storeManager = $storeManager;
        parent::__construct($context, $data);
    }

    /**
     * Get config value by path
     *
     * @param  string $path
     * @return string
     */
    protected function getConfig($path)
    {
        return $this->_scopeConfig->getValue($path, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    public function getBaseUrlMedia()
    {
        $baseUrl = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
        $urlPath = $baseUrl."/Ziffity/backendimage/";
        return $urlPath;
    }


    /**
     * Get message for popup modal
     *
     * @return string
     */
    public function getMessage()
    {
        return $this->getConfig(static::XML_MESSAGE);
    }

    public function getImage()
    {
        $image = $this->getConfig(static::XML_IMAGE);
        return $this->getBaseUrlMedia().$image;
    }

    public function isExistingCustomer(){
        if($this->customerSession->isLoggedIn())
        {
            $custid = $this->customerSession->getId();
            $orderid = $this->orderCollection->create()->addFieldToFilter('customer_id',$custid);
            
            $orderCount = $orderid->getSize();
            if($orderCount>0){
                file_put_contents(
                    '../var/log/mylog.log',
                    date('d/m/Y H:i:s') . json_encode([
                    'test' =>"kk" .$orderCount
                    ], JSON_PRETTY_PRINT). "\n",
                    FILE_APPEND
                );
                $res = $this->getMessage();
                $img = $this->getImage();
                return true;
            }
            return true;
        }
    }
}