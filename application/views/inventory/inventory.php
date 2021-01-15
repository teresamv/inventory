<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>CSV Import</title>
	
    <!-- Bootstrap library -->
    <link rel="stylesheet" href="<?php echo base_url();?>css/bootstrap.min.css">
    
   
</head>
<body>
<div class="container">
    <h2>Inventory List</h2>
	
    <!-- Display status message div-->
    <div class="col-xs-12">
        <div id="succes_msg" style="display:none;" class="alert alert-success"></div>
    </div>
    
	
    <div class="row">
        <!-- Import link -->
        <div class="col-md-12 head">
            <div class="float-right">
                <a href="javascript:void(0);" class="btn btn-success" onclick="formToggle('importFrm');"><i class="plus"></i> Import</a>
            </div>
        </div>
		
        <!-- File upload form -->
        <div class="col-md-12" id="importFrm" style="display: none;">
            <form id="frmImport" method="post" enctype="multipart/form-data">
                <input type="file" name="file" id="importCsv"/>
                <input type="button" onclick="fn_import();" class="btn btn-primary" name="importSubmit" value="IMPORT">
            </form>
        </div>
        
        <!-- Data list table -->
        <table class="table table-striped table-bordered">
            <thead class="thead-dark">
                <tr>
                    <th>#ID</th>
                    <th>Date</th>
                    <th>Category</th>
                    <th>Lot Title</th>
                    <th>Lot Location</th>
                    <th>Lot Condition</th>
                    <th>Pre Tax Amount</th>
                    <th>Tax Name</th>
                    <th>Tax Amount</th>
                </tr>
            </thead>
            <tbody id="inventoryDiv">
                <?php if(!empty($inventory)){ foreach($inventory as $row){ ?>
                 <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo $row['date']; ?></td>
                    <td><?php echo $row['category']; ?></td>
                    <td><?php echo $row['lot_title']; ?></td>
                    <td><?php echo $row['lot_location']; ?></td>
                    <td><?php echo $row['lot_condition']; ?></td>
                    <td><?php echo $row['pre_tax_amount']; ?></td>
                    <td><?php echo $row['tax_name']; ?></td>
                    <td><?php echo $row['tax_amount']; ?></td>
                </tr>
                <?php } }
                 else{ ?>
                 <tr><td colspan="9">No inventory(s) found...</td></tr>
                 <?php } ?>

            </tbody>
        </table>
        <table class="table table-striped table-bordered">
            <thead class="thead-dark">
                <tr>
                    <th>#ID</th>
                    <th>Category</th>
                    <th>Total Tax Amount</th>
                </tr>
            </thead>
            <tbody id="categoryDiv">
                <?php if(!empty($category_list)){ 
                    $i=0;
                    foreach($category_list as $category_list_row){
                    $i++; ?>
                 <tr>
                    <td><?php echo $i; ?></td>
                    <td><?php echo $category_list_row->category; ?></td>
                    <td><?php echo $category_list_row->total_amt; ?></td>
                </tr>
                <?php } }
                 else{ ?>
                 <tr><td colspan="3">No record(s) found...</td></tr>
                 <?php } ?>
                 
            </tbody>
        </table>
        <table class="table table-striped table-bordered">
            <thead class="thead-dark">
                <tr>
                    <th>#ID</th>
                    <th>Year - Month</th>
                    <th>Total Tax Amount</th>
                </tr>
            </thead>
            <tbody id="MonthDiv">
                <?php if(!empty($month_list)){ 
                    $j=0;
                    foreach($month_list as $month_list_row){
                    $j++; ?>
                 <tr>
                    <td><?php echo $j; ?></td>
                    <td><?php echo $month_list_row->YearMonth; ?></td>
                    <td><?php echo $month_list_row->total_amt; ?></td>
                </tr>
                <?php } }
                 else{ ?>
                 <tr><td colspan="3">No record(s) found...</td></tr>
                 <?php } ?>
                 
            </tbody>
        </table>
    </div>
</div>
<script type="text/javascript" src="<?php echo  base_url();?>js/jquery-3.4.1.min.js"></script>
<script>
function fn_import(){
    var file_data = $('#importCsv').prop('files')[0];   
    var form_data = new FormData();                  
    form_data.append('file', file_data);
    
    var url         = "<?php echo  base_url();?>inventory/import";
    var newdiv      = "";
    var categdiv    = "";
    var monthdiv    = "";
    var categcnt    = "";
    var monthcnt    = "";

    $.ajax({
        url: url, // point to server-side PHP script 
        dataType: 'json',  // what to expect back from the PHP script, if anything
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,                         
        type: 'post',
        success: function(responseData){
            $('#inventoryDiv').empty();
            if(responseData.inventory.length > 0){
                for(var i=0;i<responseData.inventory.length;i++){
                    newdiv += '<tr>'+
                                '<td>'+responseData.inventory[i].id+'</td>'+
                                '<td>'+responseData.inventory[i].date+'</td>'+
                                '<td>'+responseData.inventory[i].category+'</td>'+
                                '<td>'+responseData.inventory[i].lot_title+'</td>'+
                                '<td>'+responseData.inventory[i].lot_location+'</td>'+
                                '<td>'+responseData.inventory[i].lot_condition+'</td>'+
                                '<td>'+responseData.inventory[i].pre_tax_amount+'</td>'+
                                '<td>'+responseData.inventory[i].tax_name+'</td>'+
                                '<td>'+responseData.inventory[i].tax_amount+'</td>'+
                            '</tr>';
                }
                $('#inventoryDiv').append(newdiv);
                $('#succes_msg').show();
                $('#succes_msg').html(responseData.successMsg);
                $('#succes_msg').removeClass("alert alert-danger");
                $('#succes_msg').addClass("alert alert-success");
            }
            else{
                $('#inventoryDiv').html('<tr><td colspan="9">No inventory(s) found...</td></tr>');
                $('#succes_msg').show();
                $('#succes_msg').html(responseData.successMsg);
                $('#succes_msg').removeClass("alert alert-success");
                $('#succes_msg').addClass("alert alert-danger");
            }
            
            $('#categoryDiv').empty();
            if(responseData.category_list.length > 0){
                for(var j=0;j<responseData.category_list.length;j++){
                    categcnt = j+1;
                    categdiv += '<tr>'+
                                '<td>'+categcnt+'</td>'+
                                '<td>'+responseData.category_list[j].category+'</td>'+
                                '<td>'+responseData.category_list[j].total_amt+'</td>'+
                              '</tr>';
                }
                $('#categoryDiv').append(categdiv);
            }
            else{
                $('#categoryDiv').html('<tr><td colspan="3">No Record(s) found...</td></tr>');
            }

            $('#MonthDiv').empty();
            if(responseData.month_list.length > 0){
                for(var k=0;k<responseData.month_list.length;k++){
                    monthcnt = k+1;
                    monthdiv += '<tr>'+
                                '<td>'+monthcnt+'</td>'+
                                '<td>'+responseData.month_list[k].YearMonth+'</td>'+
                                '<td>'+responseData.month_list[k].total_amt+'</td>'+
                              '</tr>';
                }
                $('#MonthDiv').append(monthdiv);
            }
            else{
                $('#MonthDiv').html('<tr><td colspan="3">No Record(s) found...</td></tr>');
            }

        }
     });
    
}
function formToggle(ID){
    var element = document.getElementById(ID);
    if(element.style.display === "none"){
        element.style.display = "block";
    }else{
        element.style.display = "none";
    }
}
</script>
</body>
</html>