<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Inventory extends CI_Controller {
    
    function __construct() {
        parent::__construct();
        
        // Load inventory model
        $this->load->model('inventory_model');
        
        // Load form validation library
        $this->load->library('form_validation');
        
        // Load file helper
        $this->load->helper('file');
    }
    
    public function index(){
        $data = array();
        
        // Get messages from the session
        if($this->session->userdata('success_msg')){
            $data['success_msg'] = $this->session->userdata('success_msg');
            $this->session->unset_userdata('success_msg');
        }
        if($this->session->userdata('error_msg')){
            $data['error_msg'] = $this->session->userdata('error_msg');
            $this->session->unset_userdata('error_msg');
        }
        
        // Get rows
        $data['inventory']        = $this->inventory_model->getRows();
        $data['category_list']  = $this->inventory_model->get_sum_category_amt();
        $data['month_list']     = $this->inventory_model->get_sum_month_amt();
        
        // Load the list page view
        $this->load->view('inventory/inventory', $data);
    }
    public function import(){
        $data       = array();
        $memData    = array();
        $status     = 0;
        $successMsg = "";
        $inventory  = "";

        // Allowed mime types
        $csvMimes = array('text/x-comma-separated-values', 'text/comma-separated-values', 'application/octet-stream', 'application/vnd.ms-excel', 'application/x-csv', 'text/x-csv', 'text/csv', 'application/csv', 'application/excel', 'application/vnd.msexcel', 'text/plain');
        
        // Validate whether selected file is a CSV file
        if(!empty($_FILES['file']['name']) && in_array($_FILES['file']['type'], $csvMimes)){
            
            $insertCount = $updateCount = $rowCount = $notAddCount = 0;
            // If the file is uploaded
            if(is_uploaded_file($_FILES['file']['tmp_name'])){
                
                // Open uploaded CSV file with read-only mode
                $csvFile = fopen($_FILES['file']['tmp_name'], 'r');
                
                // Skip the first line
                fgetcsv($csvFile);
                
                // Parse data from CSV file line by line
                while(($line = fgetcsv($csvFile)) !== FALSE){
                    // Get row data
                    $datevar               = $line[0];
                    $memData['category']           = $line[1];
                    $memData['lot_title']          = $line[2];
                    $memData['lot_location']       = $line[3];
                    $memData['lot_condition']      = $line[4];
                    $memData['pre_tax_amount']      = $line[5];
                    $memData['tax_name']           = $line[6];
                    $memData['tax_amount']         = $line[7];

                    $ndate = str_replace('/', '-', $datevar);
                    $memData['date'] = date('Y-m-d', strtotime($ndate));
                    
                    // Check whether the record already exists in the database
                    $condition = array('date' => $memData['date'],
                                            'category' => $line[1],
                                            'lot_title' => $line[2],
                                            'tax_name' => $line[6]);
                    
                    $prevCount = $this->inventory_model->getRow($condition);
                    
                    if($prevCount > 0){
                        // Update inventory data
                        $update = $this->inventory_model->update($memData, $condition);
                        
                        if($update){
                            $updateCount++;
                        }
                    }else{
                        
                        // Insert inventory data
                        $insert = $this->inventory_model->insert($memData);
                        
                        if($insert){
                            $insertCount++;
                        }
                    }

                    // Status message with imported data count
                    $notAddCount = ($rowCount - ($insertCount + $updateCount));
                    $successMsg = 'Inventory imported successfully. Total Rows ('.$rowCount.') | Inserted ('.$insertCount.') | Updated ('.$updateCount.') | Not Inserted ('.$notAddCount.')';
                    $status = 1;
                }
            }
            else{
                $successMsg = "Error on file upload, please try again.";
                $status = 2;
            }
        }
        else{ 
            $successMsg = "Invalid file, please select only CSV file.";
            $status = 2;
        }
        $inventory      = $this->inventory_model->getRows();
        $category_list  = $this->inventory_model->get_sum_category_amt();
        $month_list     = $this->inventory_model->get_sum_month_amt();
        
        echo  json_encode(array("status"=>$status,
                                "successMsg"=>$successMsg,
                                "inventory"=>$inventory,
                                "category_list"=>$category_list,
                                "month_list"=>$month_list));
    }
    
}