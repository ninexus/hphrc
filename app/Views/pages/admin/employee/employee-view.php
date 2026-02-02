<?= $this->extend('layouts/employee') ?>

<?= $this->section('title') ?>
    <?= $title ?>
<?= $this->endSection() ?>


<?= $this->section('styles') ?>
<link type="text/css" href="<?= base_url('assets/modules/summernote/summernote.min.css') ?>" rel="stylesheet">
<link rel="stylesheet" type="text/css" href="<?= base_url('assets/modules/DataTables/datatables.min.css') ?>" />
<style>
    @media print {
        * {
            display: none;
        }
        #nk-msg-head {
            display: block;
        }
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="nk-content p-0">
    <div class="nk-content-inner">
        <div class="nk-content-body">
            <div class="nk-msg">
                <div class="nk-msg-body bg-white profile-shown">
                    <div>
                        <button class="Button Button--outline" onclick="printDiv()">Print</button>
                    </div>
                    <div id="nk-msg-head" class="nk-msg-head">
                        <h2 id="case_title_id" class="title d-none d-lg-block">#<?= $caseDetails['cases_id'] . ': ' . $caseDetails['cases_title']; ?></h2>
                        <div  id="case_desc_id" class="nk-msg-head-meta">
                            <div class="d-none d-lg-block col-md-12">
                                <div class="aside-wg">
                                    <h6 class="overline-title-alt mb-2">Complainant Information</h6>
                                    <ul class="user-contacts">
                                        <li>
                                            <em class="icon ni ni-user-fill"></em><span><?= $caseDetails['customer_first_name'] == '' ? 'Not Avail': $caseDetails['customer_first_name'] . ' ' . $caseDetails['customer_last_name'] ?></span>
                                        </li>
                                        <li>
                                            <em class="icon ni ni-mail"></em><span><?= empty($caseDetails['customer_email_id']) ? 'Not Avail' : $caseDetails['customer_email_id'] ?></span>
                                        </li>
                                        <li>
                                            <em class="icon ni ni-call"></em><span><?= $caseDetails['customer_mobile_no'] == 0 ? 'Not Avail': $caseDetails['customer_mobile_no'] ?></span>
                                        </li>
                                    </ul>
                                    <hr>
                                </div>
                                <ul class="nk-msg-tags">
                                    <li>
                                        <span class="label-tag">
                                            <span>
                                                Priority: <em class="icon ni ni-more-v"></em><?= $caseDetails['cases_priority'] ?>
                                            </span>
                                        </span>
                                    </li>
                                    <li>
                                        <span class="label-tag">
                                            <span>
                                                Status: <em class="icon ni ni-bar-chart-fill"></em><?= $caseDetails['cases_status'] ?>
                                            </span>
                                        </span>
                                    </li>
                                    <li>
                                        <span class="label-tag"><span>Case No: <?= $caseDetails['case_no'] ?></span></span>
                                    </li>
                               </ul>                                
                                <hr>
                                <span class="label-tag">
                                    <span><strong>Description: </strong></span>
                                </span>
                                <?= $caseDetails['cases_message']; ?>
                            </div>                           
                        </div>                        
                        <div id="file_attachement" class="col-md-12">
                            <hr>                                
                            <span class="label-tag">
                                <span><strong>File details and description: </strong></span>
                            </span>
                            <?php if (!empty($fileDetails)):?>
                                <table id="example" class="table table-striped table-bordered dt-responsive nowrap datatableEx" style="width:100%">
                                <!--<table id="example" class="table table-striped table-bordered dt-responsive nowrap datatableEx" style="width:100%">-->
                                    <thead>
                                        <tr>                                                
                                            <th>File</th>
                                            <th>Title</th>
                                            <th>Description</th>                                                                                            
                                            <th>View</th> 
                                            <th>Download</th>  
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($fileDetails as $fdrow):?>
                                            <tr>
                                                <td><?= $fdrow['case_files_name'] ?></td>
                                                <td><?= $fdrow['case_files_title'] ?></td>
                                                <td><?= $fdrow['case_files_desc'] ?></td>
                                                <td><a href="<?=  base_url('uploads/doc/causes/' . $fdrow['case_files_name'] ) ?>" target="_blank">View</a></td>                                                
                                                <td><a href="<?=  base_url('uploads/doc/causes/' . $fdrow['case_files_name'] ) ?>" download>Download</a></td>                                                
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            <?php endif; ?>                            
                        </div>
                        <a href="#" class="nk-msg-profile-toggle profile-toggle active">
                            <em class="icon ni ni-arrow-left"></em>
                        </a>
                    </div><!-- .nk-msg-head -->
                    <div class="nk-msg-reply nk-reply" data-simplebar>                        
                        <?= $comments; ?>
                    </div><!-- .nk-reply -->                    
                    <div class="nk-msg-profile visible" data-simplebar>
                        <div class="card">
                            <div class="card-inner-group">
                                <div class="card-inner">
                                    <div class="user-card user-card-s2 mb-2">
                                        <div class="user-avatar md bg-primary">
                                            <span>
                                                <?= strtoupper(substr($caseDetails['user_firstname'], 0, 1) . substr($caseDetails['user_lastname'], 0, 1)) ?>
                                            </span>
                                        </div>
                                        <div class="user-info">
                                            <h5><?= $caseDetails['user_firstname'] . ' ' . $caseDetails['user_lastname'] ?></h5>
                                            <span class="sub-text">Assigned To</span>
                                        </div>
                                        <div class="user-card-menu dropdown">
                                            <a href="#" class="btn btn-icon btn-sm btn-trigger dropdown-toggle" data-toggle="dropdown"><em class="icon ni ni-more-h"></em></a>
                                            <div class="dropdown-menu dropdown-menu-right">
                                                <ul class="link-list-opt no-bdr">
                                                    <li><a href="#"><em class="icon ni ni-eye"></em><span>View Profile</span></a></li>
                                                    <li><a href="#"><em class="icon ni ni-na"></em><span>Ban From System</span></a></li>
                                                    <li><a href="#"><em class="icon ni ni-repeat"></em><span>View Orders</span></a></li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div><!-- .card-inner -->
                                <div class="card-inner">
                                    <div class="aside-wg">
                                        <h6 class="overline-title-alt mb-2">Complainant Information</h6>
                                        <ul class="user-contacts">
                                            <li>
                                                <em class="icon ni ni-user-fill"></em><span><? empty($caseDetails['customer_first_name']) ? 'Not Avail' : $caseDetails['customer_first_name'] . ' ' . $caseDetails['customer_last_name'] ?></span>
                                            </li>
                                            <li>
                                                <em class="icon ni ni-mail"></em><span><?= empty($caseDetails['customer_email_id']) ? 'Not Avail' : $caseDetails['customer_email_id'] ?></span>
                                             </li>
                                            <li>
                                                <em class="icon ni ni-call"></em><span><?= ($caseDetails['customer_mobile_no'] == 0) ? 'Not Avail' : $caseDetails['customer_mobile_no'] ?></span>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="aside-wg">
                                        <h6 class="overline-title-alt mb-2">Additional</h6>
                                        <div class="row gx-1 gy-3">
                                            <div class="col-12">
                                                <span class="sub-text">Created Date: </span>
                                                <span><?= date("d-M-Y h:i:sa", strtotime($caseDetails['cases_dt_created'])); ?></span>
                                            </div>
                                            <div class="col-12">
                                                <span class="sub-text">Created By:</span>
                                                <span><?php echo $caseDetails['createdby_user_type'] . ': ';
                                                if ($caseDetails['createdby_user_type'] == 'customer') {
                                                    if ($caseDetails['customer_first_name'] == '') echo $caseDetails['customer_first_name'] . ' ' . $caseDetails['customer_last_name'];
                                                }if ($caseDetails['createdby_user_type'] == 'employee') {
                                                    echo $caseDetails['user_firstname'] . ' ' . $caseDetails['user_lastname'];
                                                } ?></span>
                                            </div>
                                            <div class="col-12">
                                                <span class="sub-text">Status:</span>
                                                <?php
                                                    $text = 'text-primary';
                                                    if ($caseDetails['cases_status'] == 'open') {
                                                        $text = 'text-success';
                                                    }
                                                    if ($caseDetails['cases_status'] == 'closed') {
                                                        $text = 'text-danger';
                                                    }
                                                ?>
                                                <span class="lead-text <?= $text; ?>"><?= ucfirst($caseDetails['cases_status']); ?></span>
                                            </div>                                            
                                        </div>
                                    </div>
                                    <div class="aside-wg">
                                        <h6 class="overline-title-alt mb-2">Involved Employee</h6>
                                        <ul class="align-center g-2">
        <?php
        if (!empty($involved_peopel)) {
            foreach ($involved_peopel as $row) {
                ?>
                                                            <li>
                                                                <div class="user-avatar bg-purple">
                                                                    <span><?php echo strtoupper(substr($row['user_firstname'], 0, 1) . substr($row['user_lastname'], 0, 1)); ?></span>
                                                                </div>
                                                            </li>
                <?php
            }
        }
        ?>

                                        </ul>
                                    </div>
                                </div><!-- .card-inner -->
                            </div>
                        </div>
                    </div><!-- .nk-msg-profile -->
                </div><!-- .nk-msg-body -->
            </div><!-- .nk-msg -->
        <?php if ($caseDetails['cases_status'] != 'closed') { ?>
                        <div class="col-xs-12">
                            <form class="gy-3" id="add_comment" enctype="multipart/form-data">
                                <input type="hidden" name="cases_id" value="<?= $caseDetails['cases_id']; ?>">
                                <input type="hidden" name="customer_id" value="<?= $caseDetails['refCustomer_id']; ?>">
                                <div class="nk-reply-form">                            
                                    <div class="g-3 align-center">                              
                                        <div class="col-sm-12">
                                            
                                            <div class="form-control-wrap">
                                                <div class="card card-bordered">
                                                    <div class="card-inner">
                                                        <textarea id="summernote" name="cases_message"></textarea>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>                            
                                    <div class="tab-content">
                                        <div class="tab-pane active" id="reply-form">
                                            <div class="nk-reply-form-editor">
                                                <div class="nk-reply-form-tools">
                                                </div><!-- .nk-reply-form-tools -->
                                            </div><!-- .nk-reply-form-editor -->

                                            <div class="row g-3 align-center">									
                                                <div class="col-sm-4">
                                                    <div class="form-group">
                                                        <label class="form-label float-right" for="cases_hearing_date">Next Hearing Date:</label>
                                                    </div>
                                                </div>
                                                <div class="col-sm-4">
                                                    <div class="form-group">
                                                        <div class="form-control-wrap">
                                                            <input type="text" class="form-control date-picker" data-date-format="yyyy-mm-dd" name="cases_hearing_date" id="cases_hearing_date" placeholder="Select Date" autocomplete="off">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="row g-3 align-center">
                                                <div class="col-sm-4">
                                                    <div class="form-group">
                                                        <label class="form-label float-right" for="customCheck1">Close Case</label>
                                                    </div>
                                                </div>
                                                <div class="col-sm-4">
                                                    <div class="form-group">
                                                        <div class="form-control-wrap">
                                                            <input type="checkbox" name="cases_status" value="closed" class="form-control checkbox" id="customCheck1">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="row g-3 align-center">										
                                                <div class="col-sm-4">
                                                    <div class="form-group">                                            
                                                    <label class="form-label float-right" for="customCheck1">Upload Files</label>	
                                                    </div>
                                                </div>
                                                <div class="col-sm-4">
                                                    <div class="form-group">
                                                        <div class="form-control-wrap">
                                                            <input type="file" name="case_files_file[]" multiple class="tn btn-icon btn-sm" id="case_files_file" accept="application/pdf,image/jpg,image/jpeg,image/png">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>	
                                            
                                            <div class="row g-3 align-center">										
                                                <div class="col-sm-4">
                                                    <div class="form-group">                                            
                                                        
                                                    </div>
                                                </div>
                                                <div class="col-sm-4">
                                                    <div class="form-group">
                                                        <div class="form-control-wrap">
                                                            <input type="submit" class="btn btn-primary" name="submit" id="submit" value="Update Case">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>	
                                            
                                                    
                                                    
                                        </div>                                
                                    </div>
                                </div><!-- .nk-reply-form -->                        
                            </form>
                        </div>
        <?php } ?>
        </div>
    </div>
    <!-- script for print -->
    <iframe name="print_frame" width="0" height="0" frameborder="0" src="about:blank"></iframe>
</div>

<?= $this->endSection() ?>
<?= $this->section('scripts') ?>
<script type="text/javascript" nonce="<?= SCRIPT_NONCE ?>" src="<?= base_url('assets/modules/summernote/summernote.min.js') ?>"></script>
<script nonce="<?= SCRIPT_NONCE ?>">
    $(document).ready(() => {
        $('#summernote').summernote({
            tabsize: 2,
            height: 120,
            toolbar: [
                ['style', ['style']],
                ['font', ['bold', 'underline', 'strikethrough', 'clear']],
                ['font', ['superscript', 'subscript']],
                ['color', ['color']],
                ['fontsize', ['fontsize', 'height']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['view', ['fullscreen']]
            ],
        });
    });

    function printDiv() {
        document.getElementById('file_attachement').style.display = 'none';
        window.frames["print_frame"].document.body.innerHTML = document.getElementById("nk-msg-head").innerHTML + document.getElementById("card-inner").innerHTML ;
        window.frames["print_frame"].window.focus();
        window.frames["print_frame"].window.print();
    }

    $("#add_comment").on('submit', function(e){                        
        var fileUpload = document.getElementById('case_files_file');
        if (parseInt(fileUpload.files.length)>3){
            toastr.error('You can only upload a maximum of 3 files.'); 
            return false;
        }        
        var comment = $(".summernote-basic-id").val();
        if (comment===''){            
            toastr.error('Plz add description in comment'); 
            return false;
        }
        var hearingdate=$("#cases_hearing_date").val();
        if(hearingdate===''){            
            if ($('#customCheck1').is(":checked")){}
            else{            
                toastr.error('Plz select hearing date'); 
                return false;
            }
        }
        
        
        for (var i = 0; i <= fileUpload.files.length - 1; i++) {
            if (fileUpload.files.item(i).size > 2097152) {
                toastr.error('Try to upload all files less than 2MB!');                
                return false;
            }
        }                               
        if(confirm("Confirm before submit")) {    
            e.preventDefault();
            var last_comment_id=$( ".lastcomment" ).first().data("value");
            var form_data = new FormData(this);
            form_data.append('last_comment_id', last_comment_id);
            $.ajax({
                type: 'POST',
                url: '<?= route_to("emp.add.comment") ?>',
                data: form_data,
                contentType: false,
                cache: false,
                processData:false,           
                success: function(res){
                    var res = $.parseJSON(res);
    //                $('.ajax_csrfname').val(res.token);
                    if(res.message==="success"){
                        if(res.case_sts==="yes"){
                            location.reload();
                        }
                        $("#cases_hearing_date").val('');
                        $( ".lastcomment" ).first().before( res.comments );                                                
                        $('.summernote-basic-id').summernote("code",'');
                        $('.simplebar-content-wrapper').scrollTop(0); 
                        toastr.success('Comment sent successfully');                          
                    }
                }        
            });
        }
        else{
            return false;
        }
    });
</script>
<?= $this->endSection() ?>