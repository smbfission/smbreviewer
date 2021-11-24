<?php
include_once("header.php");
include_once("sidebar.php");

if ($_SESSION['type'] != 1 && $_SESSION['type'] != 3) {
    header("HTTP/1.1 301 Moved Permanently");
    header("Location: /campaign.php");
    exit();
}

$row = $db->getReviewTemplates();
?>

<style>
    #template-preview {
        width: 150px;
    }

    .modal-content {
        height: 800px;
    }

    .modal-body {
        height: 680px;
    }
</style>

<section role="main" class="content-body">
    <header class="page-header">
        <h2>Review template settings</h2>
    </header>

    <div class="row col-lg-12 panel panel-body">
        <h2 class="h2 mt-none mb-sm text-dark text-bold">
            Review templates
        </h2>
        <p class="text-muted font-13 m-b-15">
            Use this tool to upload new review templates.
        </p>

        <a style="margin-bottom:5px;" class="btn btn-fill btn-primary pull-left" type="submit" href="/review_template_add/">Add New Review Template</a>

        <div class="row col-lg-12">
            <table class="table display table-striped table-colored table-info nowrap text-center" cellspacing="0" style="font-size:12px;width:100%">
                <thead>
                    <tr>
                        <th class="text-center">#</th>
                        <th class="text-center">Name</th>
                        <th class="text-center">Preview image</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($row as $template) : ?>
                        <tr>
                            <td><?= $template['id'] ?></td>
                            <td><?= $template['name'] ?></td>
                            <td>
                                <img id="template-preview" src="/uploads/templates/previews/<?= $template['preview_image'] ?>">
                            </td>
                            <td>
                                <div class="btn-group action-buttons">
                                    <div class="col-12 mb-xs text-nowrap">
                                        <a onclick="configurePreviewModal(<?= $template['id'] ?>)" data-toggle="modal" data-target="#template-preview-modal" class="btn btn-fill btn-xs btn-link action-link">
                                            <i class="ace-icon fa fa-search bigger-120"></i>
                                        </a>
                                        <a href="/review_template_add.php?id=<?= $template['id']  ?>" class="btn btn-fill btn-xs btn-info action-edit">
                                            <i class="ace-icon fa fa-pencil bigger-120"></i>
                                        </a>
                                        <a onclick="deleteTemplate('<?= $template['id'] ?>')" class="btn btn-fill btn-xs btn-danger action-delete">
                                            <i class="ace-icon fa fa-trash-o bigger-120"></i>
                                        </a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>

<!-- Template preview modal -->
<div class="modal fade" id="template-preview-modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="template-preview-modal-title">Template preview</h5>
            </div>
            <div class="modal-body">
                <iframe id="preview-iframe" src="/crc/1149e6d531608300e900b16d9ee24794 " frameborder="0" style="overflow:hidden;overflow-x:hidden;overflow-y:hidden;height:100%;width:100%;position:absolute;top:0px;left:0px;right:0px;bottom:0px" height="100%" width="100%"></iframe>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script src="/assets/vendor/jquery/jquery.js"></script>
<script src="/assets/vendor/jquery-browser-mobile/jquery.browser.mobile.js"></script>
<script src="/assets/vendor/bootstrap/js/bootstrap.js"></script>
<script src="/assets/vendor/nanoscroller/nanoscroller.js"></script>
<script src="/assets/vendor/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>
<script src="/assets/vendor/magnific-popup/magnific-popup.js"></script>
<script src="/assets/vendor/jquery-placeholder/jquery.placeholder.js"></script>
<script src="/assets/vendor/jquery-autosize/jquery.autosize.js"></script>
<script src="/assets/vendor/bootstrap-fileupload/bootstrap-fileupload.min.js"></script>
<script src="/assets/javascripts/theme.js"></script>
<script src="/assets/javascripts/theme.custom.js"></script>
<script src="/assets/javascripts/theme.init.js"></script>

<script>
    function deleteTemplate(templateId) {
        $.post("action.php?action=delete_review_template", {
                id: templateId
            })
            .done(function(data) {
                location.reload();
            });
    }

    function configurePreviewModal(templateId) {
        $('#preview-iframe').attr('src', '/crc/1149e6d531608300e900b16d9ee24794?preview=true&preview_id=' + templateId);
    }
</script>