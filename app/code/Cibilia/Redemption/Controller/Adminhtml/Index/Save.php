<?php

namespace Cibilia\Redemption\Controller\Adminhtml\Index;

use Magento\Backend\App\Action;

class Save extends \Magento\Backend\App\Action
{
    /**
     * @var PostDataProcessor
     */
    protected $dataProcessor;

    /**
     * @param Action\Context $context
     * @param PostDataProcessor $dataProcessor
     */
    public function __construct(Action\Context $context, PostDataProcessor $dataProcessor)
    {
        $this->dataProcessor = $dataProcessor;
        parent::__construct($context);
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Cibilia_Redemption::save');
    }

    /**
     * Save action
     *
     * @return void
     */
    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        if ($data) {
            $data = $this->dataProcessor->filter($data);
            $model = $this->_objectManager->create('Cibilia\Redemption\Model\Redemption');

            $id = $this->getRequest()->getParam('redemption_id');
            if ($id) {
                $model->load($id);
            }
            
            // save image data and remove from data array
            if (isset($data['image'])) {
                $imageData = $data['image'];
                unset($data['image']);
            } else {
                $imageData = array();
            }
            /*echo "<pre>";
            print_r($data);
            die;*/

            $model->addData($data);

            if (!$this->dataProcessor->validate($data)) {
                $this->_redirect('*/*/edit', ['redemption_id' => $model->getId(), '_current' => true]);
                return;
            }

            try {
                $imageHelper = $this->_objectManager->get('Cibilia\Redemption\Helper\Data');

                if (isset($imageData['delete']) && $model->getImage()) {
                    $imageHelper->removeImage($model->getImage());
                    $model->setImage(null);
                }
                
                $imageFile = $imageHelper->uploadImage('image');
                if ($imageFile) {
                    $model->setImage($imageFile);
                }
                $model->setUpdatedAt(date('Y-m-d H:i:s'));
                $model->save();

                if($model->getStatus() == 1){
                    $this->_updateCibilianTotals($model->getCibilianId(),$model->getAmount());
                }
                $this->messageManager->addSuccess(__('The Data has been saved.'));
                $this->_objectManager->get('Magento\Backend\Model\Session')->setFormData(false);
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', ['redemption_id' => $model->getId(), '_current' => true]);
                    return;
                }
                $this->_redirect('*/*/');
                return;
            } catch (\Magento\Framework\Model\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('Something went wrong while saving the data.'));
            }

            $this->_getSession()->setFormData($data);
            $this->_redirect('*/*/edit', ['redemption_id' => $this->getRequest()->getParam('redemption_id')]);
            return;
        }
        $this->_redirect('*/*/');
    }
    public function _updateCibilianTotals($cibilian_id,$amount){
    
        $objSummarry = $this->_objectManager->create('Cibilia\Summary\Model\Summary')->load($cibilian_id,'cibilian_id');

        if($objSummarry && $objSummarry->getId()){
            $newPaid = $objSummarry->getPaidAmount() + $amount;
            $newPending = $objSummarry->getPendingAmount() - $amount;


            $objSummarry->setPaidAmount($newPaid);
            $objSummarry->setPendingAmount($newPending);

            $objSummarry->save();
        }
    }
}
