<?= $this->extend('layouts/default') ?>

<?= $this->section('title') ?>
    <?= $title ?>
<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<link rel="stylesheet" type="text/css" href="<?= base_url('assets/modules/DataTables/datatables.min.css') ?>" />
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="page-heading text-center">
    <div class="container zoomIn animated">
        <h1 class="page-title">Downloads and Other Information <span class="title-under"></span></h1>
        <p class="page-description">
            Himachal Pradesh Human Rights Commission , Minister House No. 3, Grant Lodge, Shimla-171002, HP.
        </p>
    </div>
</div>

<div class="main-container">
    <div class="container">

        <div class="row">
            <div class="col-md-12 fadeIn animated">
                <p>Documents avaliable for public download.</p>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12 fadeIn">
                <h2 class="title-style-2">View / Download <span class="title-under"></span></h2>

                <div role="tabpanel">
                    <!-- Tabs -->
                    <ul class="nav nav-tabs custom-tabs" id="status-tabs" role="tablist">
                        <?php if (!empty($file_type)): ?>
                            <?php foreach ($file_type as $key => $ftrow): ?>
                                <li role="presentation" class="nav-item">
                                    <button
                                        class="nav-link <?= $key === 0 ? 'active' : '' ?>"
                                        id="<?= $ftrow['category_code'] ?>-tab"
                                        data-bs-toggle="tab"
                                        data-bs-target="#<?= $ftrow['category_code'] ?>-panel"
                                        type="button"
                                        role="tab">
                                        <?= $ftrow['category_title'] ?>
                                    </button>
                                </li>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </ul>

                    <!-- Tab panes -->
                    <div class="tab-content" id="status-tab-content">
                        <?php if (!empty($file_type)): ?>
                            <?php foreach ($file_type as $key => $ftrow): ?>
                                <div class="tab-pane fade <?= $key === 0 ? 'show active' : '' ?>"
                                     id="<?= $ftrow['category_code'] ?>-panel"
                                     role="tabpanel">

                                    <div class="table-responsive">
                                        <table id="example<?= $key ?>"
                                               class="table table-striped table-bordered"
                                               style="width:100%">
                                            <thead>
                                                <tr>
                                                    <th>Index</th>
                                                    <th>Ref No</th>
                                                    <th>Title</th>
                                                    <th>Category</th>
                                                    <th>Description</th>
                                                    <th>Download</th>
                                                </tr>
                                            </thead>
                                            <tfoot>
                                                <tr>
                                                    <th>Index</th>
                                                    <th>Ref No</th>
                                                    <th>Title</th>
                                                    <th>Category</th>
                                                    <th>Description</th>
                                                    <th>Download</th>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>

                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>

                </div>
            </div>
        </div>

    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script nonce="<?= SCRIPT_NONCE ?>" src="<?= base_url('assets/modules/DataTables/datatables.min.js') ?>"></script>

<script nonce="<?= SCRIPT_NONCE ?>">
    DataTable.defaults.responsive = true;

    const tables = {};

    const initTable = (id, category_code) => {

        if (tables[id]) {
            // Force redraw when revisiting tab
            tables[id].columns.adjust().draw(false);
            return;
        }

        tables[id] = new DataTable(`#${id}`, {
            responsive: true,
            processing: true,
            serverSide: true,
            pagingType: "full_numbers",
            pageLength: 10,
            lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
            order: [],

            ajax: {
                url: "<?= base_url(route_to('downloads.list')) ?>",
                type: "GET",
                data: function (d) {
                    d.category_code = category_code;
                }
            },

            columns: [
                { data: "index", orderable: false, searchable: false },
                { data: "upload_file_ref_no" },
                { data: "upload_file_title" },
                { data: "category_title_sub" },
                { data: "upload_file_desc", orderable: false },
                { data: "download", orderable: false, searchable: false },
            ],
        });
    };

    /* Initialize FIRST (visible) tab */
    <?php if (!empty($file_type)): ?>
        initTable("example0", "<?= $file_type[0]['category_code'] ?>");
    <?php endif; ?>

    /* Initialize table ONLY when tab is shown */
    document.querySelectorAll('button[data-bs-toggle="tab"]').forEach(tab => {
        tab.addEventListener('shown.bs.tab', function (event) {
            const target = event.target.getAttribute('data-bs-target');
            const panel = document.querySelector(target);
            const table = panel.querySelector('table');

            if (table) {
                const id = table.getAttribute('id');
                const category = event.target.getAttribute('id').replace('-tab', '');
                initTable(id, category);
            }
        });
    });
</script>
<?= $this->endSection() ?>
