<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Nxtech\Customform\Controller\Index;

use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Exception\LocalizedException;
use Nxtech\Customform\Model\CustomformFactory;

use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Translate\Inline\StateInterface;
use Magento\Store\Model\StoreManagerInterface;

class Post extends \Magento\Framework\App\Action\Action
{
    /**
     * @var DataPersistorInterface
     */
    private $dataPersistor;

    /**
     * @var Context
     */
    private $context;

    protected $customformFactory;
    protected $transportBuilder;
    protected $storeManager;
    protected $inlineTranslation;


    /**
     * @param Context $context
     * @param DataPersistorInterface $dataPersistor
     */
    public function __construct(
        Context $context,
        DataPersistorInterface $dataPersistor,
        CustomformFactory $customformFactory,
        TransportBuilder $transportBuilder,
        StoreManagerInterface $storeManager,
        StateInterface $state
    ) {
        parent::__construct($context);
        $this->context = $context;
        $this->dataPersistor = $dataPersistor;
        $this->customformFactory = $customformFactory;   
        $this->transportBuilder = $transportBuilder;
        $this->storeManager = $storeManager;
        $this->inlineTranslation = $state;
    }

    /**
     * Post user question
     *
     * @return Redirect
     */
    public function execute()
    {
        if (!$this->getRequest()->isPost()) {
            return $this->resultRedirectFactory->create()->setPath('*/*/');
        }
        $data = (array)$this->getRequest()->getPost();

        $data['status'] = 1;    
        $data['created_at'] = date('y-m-d');
        $data['updated_at'] = date('y-m-d');
        try {
            $model = $this->customformFactory->create();
            $model->setData($data)->save();

            // Send Email
            try {

                $templateId = 'customform_general_template'; // template id
                $fromEmail = 'testtaskservice@gmail.com';  // sender Email id
                $fromName = 'Hazel Grace';             // sender Name
                $toEmail = 'testnxtechnolab@gmail.com'; // receiver email id

                // template variables pass here
                $templateVars = $data;
     
                $storeId = $this->storeManager->getStore()->getId();
     
                $from = ['email' => $fromEmail, 'name' => $fromName];
                $this->inlineTranslation->suspend();
     
                $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
                $templateOptions = [
                    'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                    'store' => $storeId
                ];
                $transport = $this->transportBuilder->setTemplateIdentifier($templateId, $storeScope)
                    ->setTemplateOptions($templateOptions)
                    ->setTemplateVars($templateVars)
                    ->setFrom($from)
                    ->addTo($toEmail)
                    ->getTransport();
                $transport->sendMessage();
                $this->inlineTranslation->resume();
            } catch (\Exception $e) {
                $this->_logger->info($e->getMessage());
            }
            //End Send Email

            $this->messageManager->addSuccessMessage(
                __('Thanks for contacting us with your comments and questions. We\'ll respond to you very soon.')
            );
            $this->dataPersistor->clear('customform');
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            $this->dataPersistor->set('customform', $this->getRequest()->getParams());
        } 
        return $this->resultRedirectFactory->create()->setPath('customform/index');
    }
}
