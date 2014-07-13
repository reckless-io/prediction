<?php
class Reckless_Prediction_Adminhtml_PredictionController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
        $this->_title('Reckless Predictions');
        $this->loadLayout();
        $this->_setActiveMenu('customers/sales');
        $this->_addContent($this->getLayout()->createBlock('reckless_prediction/adminhtml_predictions'));
        $this->renderLayout();
    }
    public function exportPredictionsCsvAction()
    {
        $fileName = 'reckless_predictions.csv';
        $grid = $this->getLayout()->createBlock('reckless_prediction/adminhtml_predictions_grid');
        $this->_prepareDownloadResponse($fileName, $grid->getCsvFile());
    }
    public function exportPredictionsExcelAction()
    {
        $fileName = 'reckless_predictions.xls';
        $grid = $this->getLayout()->createBlock('reckless_prediction/adminhtml_predictions_grid');
        $this->_prepareDownloadResponse($fileName, $grid->getExcelFile($fileName));
    }
    public function redemptionsAction()
    {
        $this->_title('Reckless Redemptions');
        $this->loadLayout();
        $this->_setActiveMenu('reports/redemptions');
        $this->_addContent($this->getLayout()->createBlock('reckless_prediction/adminhtml_redemptions'));
        $this->renderLayout();
    }
    public function exportRedemptionsCsvAction()
    {
        $fileName = 'reckless_redemptions.csv';
        $grid = $this->getLayout()->createBlock('reckless_prediction/adminhtml_redemptions_grid');
        $this->_prepareDownloadResponse($fileName, $grid->getCsvFile());
    }
    public function exportRedemptionsExcelAction()
    {
        $fileName = 'reckless_redemptions.xls';
        $grid = $this->getLayout()->createBlock('reckless_prediction/adminhtml_redemptions_grid');
        $this->_prepareDownloadResponse($fileName, $grid->getExcelFile($fileName));
    }
}
