<?php
include_once("header.php");
include_once("sidebar.php");

if ($_SESSION['type'] != 1 && $_SESSION['type'] != 3) {
    header("HTTP/1.1 301 Moved Permanently");
    header("Location: /campaign.php");
    exit();
}

if (isset($_REQUEST['id']) && (int)$_REQUEST['id'] != 0) {
    $template = $db->getReviewTemplate($_REQUEST['id']);
}

?>

<style>
    iframe {
        display: block;
    }

    button.preview {
        margin-top: 10px;
    }

    #image-preview {
        width: 100%;
    }

    #template-preview-modal .modal-body {
        height: 680px;
    }

    #editor {
        width: 100%;
        height: 100%;
    }

    #template-editor-modal .modal-dialog {
        width: 90%;
    }

    #template-editor-modal .modal-body {
        height: 800px;
    }
</style>

<section role="main" class="content-body">
    <header class="page-header">
        <h2>Review template settings</h2>
    </header>

    <div class="row col-lg-12 panel panel-body">
        <h2 class="h2 mt-none mb-sm text-dark text-bold <?= isset($template) ? '' : 'hidden' ?>">
            Edit <?= $template['name'] ?> template
        </h2>





        <h2 class="h2 mt-none mb-sm text-dark text-bold <?= isset($template) ? 'hidden' : '' ?>">
            Upload new template
        </h2>


        <p class="text-muted font-13 m-b-15 <?= isset($template) ? '' : 'hidden' ?>">
            If image is not uploaded, old ones will stay active. For editor changes to take place, you have to save the whole template.
        </p>

        <form action="/action.php?action=<?= isset($template) ? 'review_template_update&id=' . $template['id'] : 'review_template_add' ?>" method="post" class="form-horizontal form-bordered" id="template-form" enctype="multipart/form-data">
            <div class="form-group">
                <label class="col-md-3 control-label" for="template-name">Template name</label>
                <div class="col-md-9">
                    <input class="form-control" name="template_name" id="template-name" type="text" value="<?= isset($template) ? $template['name'] : '' ?>">
                </div>
            </div>




            <div class="form-group">
                <label class="col-md-3 control-label" for="template-islive">Template is Live? </label>
                <div class="col-md-9">
                    <input class="form-check-input" name="template_islive" id="template-islive" type="checkbox" <?php if (isset($template) && $template['islive'] == 1) {
                                                                                                                    echo "checked='checked'";
                                                                                                                } ?>>
                </div>
            </div>


            <div class="form-group">
                <label class="col-md-3 control-label" for="template-forfreeuser">Available to Free Users? </label>
                <div class="col-md-9">
                    <input class="form-check-input" name="template_forfreeuser" id="template-forfreeuser" type="checkbox" <?php if (isset($template) && $template['forfreeuser'] == 1) {
                                                                                                                                echo "checked='checked'";
                                                                                                                            } ?>>
                </div>
            </div>
            <!-- /*
            islive,forfreeuser
            add two checkbox for add and update

            */ -->
            <div class="form-group">
                <label class="col-md-3 control-label" for="template">Upload your template</label>
                <div class="col-md-9">
                    <textarea name="template_code" id="template-code" type="text" hidden></textarea>
                    <button data-toggle="modal" data-target="#template-editor-modal" type="button" class="btn btn-fill btn-primary preview">Edit template</button>
                    <button data-toggle="modal" data-target="#template-preview-modal" type="button" class="btn btn-fill btn-primary preview <?= isset($template) ? '' : 'hidden' ?>">Preview current template</button>
                </div>
            </div>

            <div class="form-group">
                <label class="col-md-3 control-label" for="resources">Files referenced in template (ex. images, css, js files)</label>
                <div class="col-md-9">
                    <input type="file" name="resources[]" id="resources" multiple="multiple">
                </div>
            </div>

            <div class="form-group">
                <label class="col-md-3 control-label" for="preview-image">Template preview image</label>
                <div class="col-md-9">
                    <input type="file" name="preview-image" id="preview-image">
                    <button data-toggle="modal" data-target="#image-preview-modal" type="button" class="btn btn-fill btn-primary preview <?= isset($template) ? '' : 'hidden' ?>">Preview current image</button>
                </div>
            </div>

            <div class="row">
                <div class="col-md-3"></div>
                <div class="col-md-9">
                    <button type="submit" name="submit" value="submit" class="btn btn-fill btn-success" id="template-form-submit" onclick="return upload(event)">
                        <span class="ace-icon fa fa-save bigger-120"></span> Save
                    </button>
                </div>
            </div>
        </form>
    </div>
</section>

<!-- Template editor -->
<div class="modal fade" id="template-editor-modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="template-editor-modal-title">Edit template code</h5>
            </div>
            <div class="modal-body">
                <pre id="editor">
                    <?php
                    if (isset($template)) {
                        $data = htmlentities(file_get_contents('./uploads/templates/' . $template['name'] . '/' . $template['template']));
                        echo $data;
                    }
                    ?>
                </pre>
            </div>
            <div class="modal-footer">
                <button type="button" id="save" class="btn btn-secondary" data-dismiss="modal" <?= isset($template) ? 'onclick="saveFile(' + $template['name'] + ', ' + $template['template'] + ')"' : '' ?>>Save</button>
                <button type="button" id="cancel" class="btn btn-danger" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>

<?php if (isset($template)) : ?>
    <!-- Template preview modal -->
    <div class="modal fade" id="template-preview-modal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="template-preview-modal-title">Template preview</h5>
                </div>
                <div class="modal-body">
                    <iframe id="preview-iframe" src="/crc/1149e6d531608300e900b16d9ee24794?preview=true&preview_id=<?= $template['id'] ?>" frameborder="0" style="overflow:hidden;overflow-x:hidden;overflow-y:hidden;height:100%;width:100%;position:absolute;top:0px;left:0px;right:0px;bottom:0px" height="100%" width="100%"></iframe>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Image preview modal -->
    <div class="modal fade" id="image-preview-modal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="image-preview-modal-title">Image preview</h5>
                </div>
                <div class="modal-body">
                    <img id="image-preview" src="/uploads/templates/<?= rawurlencode($template['name']) . '/' . rawurlencode($template['preview_image']) ?>">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
<?php endif ?>

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
<script src="/ace/ace.js" type="text/javascript" charset="utf-8"></script>

<script>
    let initialTemplateCode = "";
    var editor = ace.edit("editor");
    editor.setTheme("ace/theme/twilight");
    editor.session.setMode("ace/mode/php");
    editor.setFontSize(18);

    $(document).ready(function() {
        initialTemplateCode = editor.getValue();
    });

    $('#save').click(function() {
        $('#template-code').val(editor.getValue());
    });

    $('#cancel').click(function() {
        $('#template-code').val(initialTemplateCode);
    });

    function upload(e) {
        let files = $('#resources').get(0).files;
        let combinedSize = 0;



        for (i = 0; i < files.length; i++) {
            
            if (files[i].size / 1024 > 200) {
                e.preventDefault();
                alert("File size cannot exceed 200kB!")
                return false;
            }
        }
        return true;
    }

    /* Powered by www.Andrezzz.pt */
    function saveFile(name, template) {
        var contents = editor.getSession().getValue();

        $.post("/action.php?action=review_template_save", {
            contents: contents,
            template: {
                name: name,
                template: template
            }
        }, function(data) {
            var response = $.parseJSON(data);

            if (response.result !== true) {
                alert(response.result);
            }
        });
    }
</script>