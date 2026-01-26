<?= $this->extend('layouts/default') ?>

<?= $this->section('title') ?>
    <?= $title ?>
<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<link type="text/css" href="<?= base_url('assets/modules/summernote/summernote-bs5.min.css') ?>" rel="stylesheet">
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<style>
    .btn_disabled{
        pointer-events: none;
        background-color: #c3bdbd;
        opacity: 15.9;
    }
    .howtocontact{
        display: none !important;
    }
</style>
<div class="page-heading text-center">
    <div class="container zoomIn animated">
        <h1 class="page-title">CASE REQUEST<span class="title-under"></span></h1>
        <p class="page-description">
            Himachal Pradesh Human Rights Commission , Minister House No. 3, Grant Lodge, Shimla-171002, HP.
        </p>
    </div>
</div>
<div class="main-container fadeIn animated">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 col-sm-12 col-form"> 
                <?= form_open(route_to('complaint.req'), ['class' => 'contact-form', 'id' => 'add_complaint', 'name' => 'addcases', 'enctype' => 'multipart/form-data']); ?>
                    <div class="mb-3">
                        <div class="row">
                            <div class="col-sm-4 col-xs-12">
                                <label class="form-label fw-bold" for="cases_title">Title</label>
                            </div>
                            <div class="col-sm-8 col-xs-12">
                                <input type="text" class="form-control <?= $validation->hasError('cases_title') ? 'is-invalid' : '' ?>" name="cases_title" id="cases_title" placeholder="Enter Title" autocomplete="off" value="<?= old('cases_title') ?>">
                                <div class="invalid-feedback">
                                    <?= esc($validation->getError('cases_title')) ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- <div class="mb-3">
                        <div class="row">
                            <div class="col-sm-4 col-xs-12">
                                <label class="form-label fw-bold" for="howtocontact">How to contact</label>
                            </div>
                            <div class="col-sm-8 col-xs-12">
                                <select class="form-select <?= $validation->hasError('howtocontact') ? 'is-invalid' : '' ?>" id="howtocontact" name="howtocontact" tabindex="-1" aria-label="how to contact" onchange="handleHowToContact()">
                                    <?php $oldValue = old('howtocontact'); ?>
                                    <option value="" <?= $oldValue === "" ? "selected" : "" ?>>Select</option>                                                
                                    <option value="Email" <?= $oldValue === "Email" ? "selected" : "" ?>>Email</option>                                                          
                                    <option value="Mobile" <?= $oldValue === "Mobile" ? "selected" : "" ?>>Mobile</option>                                                          
                                    <option value="Both" <?= $oldValue === "Both" ? "selected" : "" ?>>Both</option>                                                          
                                </select>
                                <div class="invalid-feedback">
                                    <?= esc($validation->getError('howtocontact')) ?>
                                </div>
                            </div>
                        </div>
                    </div> -->
                    <div class="mb-3">
                        <div class="row">
                            <div class="col-sm-4 col-xs-12">
                                <label class="form-label fw-bold" for="customer_email">Complainant Email</label>
                            </div>
                            <div class="col-sm-8 col-xs-12">                            
                                <input type="email" class="form-control <?= $validation->hasError('customer_email') ? 'is-invalid' : '' ?>" name="customer_email" id="customer_email" placeholder="Enter Complainant email" autocomplete="off" value="<?= session(SSO_SESSION)['email'] ?>" readonly>
                                <div class="invalid-feedback">
                                    <?= esc($validation->getError('customer_email')) ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="row">
                            <div class="col-sm-4 col-xs-12">
                                <label class="form-label fw-bold" for="customer_contact">Complainant Mobile</label>
                            </div>
                            <div class="col-sm-8 col-xs-12">
                                <input type="tel" class="form-control mobileno <?= $validation->hasError('customer_contact') ? 'is-invalid' : '' ?>" name="customer_contact" id="customer_contact" placeholder="Enter Complainant mobile number" maxlength="10" minlength="10" autocomplete="off" value="<?= session(SSO_SESSION)['mobile'] ?>" readonly>
                                <div class="invalid-feedback">
                                    <?= esc($validation->getError('customer_contact')) ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="row">
                            <div class="col-sm-4 col-xs-12">
                                <label class="form-label fw-bold" for="cases_party_name">Party Name</label>
                            </div>
                            <div class="col-sm-8 col-xs-12">
                                <input type="text" class="form-control <?= $validation->hasError('cases_party_name') ? 'is-invalid' : '' ?>" name="cases_party_name" id="cases_party_name" placeholder="Enter Party Name" autocomplete="off" value="<?= old('cases_party_name') ?>">
                                <div class="invalid-feedback">
                                    <?= esc($validation->getError('cases_party_name')) ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="row">
                            <div class="col-sm-4 col-xs-12">
                                <label class="form-label fw-bold" for="cases_party_address">Party Address</label>
                            </div>
                            <div class="col-sm-8 col-xs-12">
                                <textarea class="form-control <?= $validation->hasError('cases_party_address') ? 'is-invalid' : '' ?>" rows="5" placeholder="Enter Party Address" name="cases_party_address"><?= old('cases_party_address') ?></textarea>
                                <div class="invalid-feedback">
                                    <?= esc($validation->getError('cases_party_address')) ?>
                                </div>
                            </div>
                        </div>
                    </div> 
                    <div class="mb-3">
                        <div class="row">
                            <div class="col-sm-4 col-xs-12">
                                <label class="form-label fw-bold" for="cases_party_number">Party Contact Number</label>
                            </div>
                            <div class="col-sm-8 col-xs-12">
                                <input type="tel" class="form-control mobileno <?= $validation->hasError('cases_party_number') ? 'is-invalid' : '' ?>" name="cases_party_number" id="cases_party_number" placeholder="Enter Party Mobile Number" maxlength="10" minlength="10" autocomplete="off" value="<?= old('cases_party_number') ?>">
                                <div class="invalid-feedback">
                                    <?= esc($validation->getError('cases_party_number')) ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    

                    <div class="mb-3 case_files_file_div">
                        <div class="row">
                            <div class="col-sm-4 col-xs-12">
                                <label class="form-label fw-bold" for="case_files_file">Files</label>
                            </div>
                            <div class="col-sm-8 col-xs-12">
                                <input type="file" id="case_files_file" class="form-control <?= $validation->hasError('case_files_file') ? 'is-invalid' : '' ?>"  multiple name="case_files_file[]" accept="application/pdf,image/jpg,image/jpeg,image/png">
                                <div class="invalid-feedback">
                                    <?= esc($validation->getError('case_files_file')) ?>
                                </div>
                            </div> 
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="row">
                            <div class="col-sm-4 col-xs-12">
                                <label class="form-label fw-bold" for="cases_message">Description</label>
                            </div>
                            <div class="col-sm-8 col-xs-12">
                                <textarea id="summernote" name="cases_message"><?= old('cases_message') ?></textarea>                            
                            </div> 
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="row">
                            <script nonce="<?= SCRIPT_NONCE ?>" type="text/javascript" src="https://www.google.com/recaptcha/api.js" async defer></script>
                            <script nonce="<?= SCRIPT_NONCE ?>" type="text/javascript">
                                function enableRegister() {                                
                                    document.getElementById("btnSubmit").disabled = false;
                                }
                            </script>
                            <label class="form-label col-sm-4 col-xs-12" for="ptsp"></label>
                            <div class="col-sm-8 col-xs-12">
                                <div class="g-recaptcha" data-sitekey="<?= env('RE_CAPTCHA_SITE_KEY', '') ?>" data-callback="enableRegister"></div>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="mb-2">
                        <div class="m-auto text-center">    
                            <button type="submit" class="btn btn-secondary" disabled="true" id="btnSubmit">Submit</button>
                        </div>
                    </div>
                <?= form_close() ?>
            </div>
        </div>     
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script type="text/javascript" nonce="<?= SCRIPT_NONCE ?>" src="<?= base_url('assets/modules/summernote/summernote-bs5.min.js') ?>"></script>
<script nonce="<?= SCRIPT_NONCE ?>">
    const handleHowToContact = () => {
        const inputField = document.getElementById('howtocontact');
        const howtocontact = inputField.value;
        if (howtocontact === 'Email') {
            $(".howtocontact_email").removeClass("howtocontact");
            $("#customer_email").val("<?= session(SSO_SESSION)['email'] ?>");
            $("#customer_contact").val("");
            $(".howtocontact_mobile").addClass("howtocontact");
        } else if(howtocontact === 'Mobile') {
            $(".howtocontact_mobile").removeClass("howtocontact");
            $("#customer_contact").val("<?= session(SSO_SESSION)['mobile'] ?>");
            $("#customer_email").val("");
            $(".howtocontact_email").addClass("howtocontact");
        } else if(howtocontact === 'Both') {
            $(".howtocontact_email").removeClass("howtocontact");
            $(".howtocontact_mobile").removeClass("howtocontact");
            $("#customer_email").val("<?= session(SSO_SESSION)['email'] ?>");
            $("#customer_contact").val("<?= session(SSO_SESSION)['mobile'] ?>");
        } else {
            $("#customer_email").val("");
            $("#customer_contact").val("");
            $(".howtocontact_email").addClass("howtocontact");
            $(".howtocontact_mobile").addClass("howtocontact");
        }
    };

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

        // handleHowToContact();

        $("#case_files_file").on("change", function(){
            $('.case_files_file_title_desc').remove();                  
            var numFiles = $(this)[0].files.length;
            var i;
            var text='';
            for (i = 1; i <= numFiles; i++) {                   
                text +="<div class='form-group case_files_file_title_desc'><div class='row'><label class='control-label col-sm-4 col-xs-12' for='title_file'>Title: ["+$(this)[0].files.item(i-1).name.substr(0,30)+" ] </label><div class='col-sm-8 col-xs-12'><input type='text' class='form-control' name='title_file[]' placeholder='Enter "+$(this)[0].files.item(i-1).name.substr(0,30)+" title' autocomplete='off' required></div></div></div>";
                text +="<div class='form-group case_files_file_title_desc'><div class='row'><label class='control-label col-sm-4 col-xs-12' for='desc_file'>Description: ["+$(this)[0].files.item(i-1).name.substr(0,30)+" ] </label><div class='col-sm-8 col-xs-12'><textarea class='form-control' name='desc_file[]' placeholder='Enter "+$(this)[0].files.item(i-1).name.substr(0,30)+" description'></textarea></div></div></div>"
            }
            $( ".case_files_file_div" ).after(text);
        });
    });
</script>
<?= $this->endSection() ?>