<?php
//session_start();
ob_start();
include_once("header.php");
include_once("sidebar.php");
require_once('core/tools.php');
$templates = $db->getReviewTemplatesonlylive($_SESSION['user_id']);

if (isset($_REQUEST['id']) && (int)$_REQUEST['id'] != 0) {
    $row = $db->getCaptureReviewsByIdUserId($_REQUEST['id'], $_SESSION['user_id']);

    if ($row == null) {
        header("HTTP/1.1 301 Moved Permanently");
        header('location:' . $_SERVER['HTTP_REFERER']);
        exit();
    }
}

elseif (!isset($_REQUEST['id']) && isset($_SESSION['user_id']) ) {

      $_REQUEST['id'] = (int)$db->addCaptureReviews([
          'user_id' => $_SESSION['user_id'],
      ]);
  
      $row = $db->getCaptureReviewsByIdUserId($_REQUEST['id'], $_SESSION['user_id']);

          if ($row == null) {
              header("HTTP/1.1 301 Moved Permanently");
              header('location:' . $_SERVER['HTTP_REFERER']);
              exit();
          }


}


$user = $db->getUserProfile($_SESSION['user_id']);
$row['short_io_api_key'] = $user['short_io_api_key'];

if (isset($row['google_access_token']) && trim($row['google_access_token']) != "") {

    $data = [
        'google_refresh_token' => $row['google_refresh_token'],
        'google_access_token' => $row['google_access_token'],
        'id' => $row['id'],
        'user_id' => $row['user_id'],

    ];
    $res = Tools::capture_reviews_check_access_google_sheets($row['google_access_token'], $row['google_refresh_token'], $row['id'], $row['user_id']);
    $google_user_data = json_decode($res, true);

    if (trim($row['google_spread_sheet_id']) != '') {
        $google_spread_sheet_data = Tools::capture_reviews_get_google_sheets($row['google_spread_sheet_id'], $row['id'], $row['user_id']);
        $google_spread_sheet_data = json_decode($google_spread_sheet_data, true);
    }
} else {
    $row['google_access_token'] = "";
    $google_user_data = [];
}

if (isset($row['short_io_api_key']) && trim($row['short_io_api_key']) != "") {
    $short_io_domains = Tools::capture_reviews_get_short_io_domains($row['short_io_api_key']);
    $short_io_domains = json_decode($short_io_domains, true);

    if (isset($row['short_io_domain']) && trim($row['short_io_domain']) != "") {
        $short_io_domain_link = Tools::siteURL() . '/crc/' . Tools::encrypt_link($row['id']);
        $short_io_domain_link = json_decode(Tools::capture_reviews_get_short_io_by_origin_url($row['short_io_api_key'], ['domain' => $row['short_io_domain'], 'originalURL' => $short_io_domain_link]), true);

        if (!isset($short_io_domain_link['error'])) {
            $short_io_domain_link = $short_io_domain_link['shortURL'];
        } else if ($short_io_domain_link['error'] === "Link not found") {
            $short_io_domain_link = "";
        } else {
            $short_io_domain_link = $short_io_domain_link['error'];
        }
    }
}



$fonts = ["Arial", "Times", "Courier", "Verdana", "Georgia", "Palatino", "Comic Sans MS", "Trebuchet MS", "Arial Black", "Impact"];
?>
<style media="screen">
    .colorpicker-component>.input-group-addon:first-child,
    .font-select-component>.input-group-addon:first-child {
        min-width: 100px;
    }

    .popover {
        max-width: 40vw;
    }

    #set_google_sheet_id.set {
        margin: -10px -12px;
        padding: 5px;
        display: flex;

        align-items: center;
        width: 5rem;
        justify-content: center;
    }

    #social-media-share label:not(:first-child) {
        margin-right: 20px;
    }

    .flex-center {
        display: flex;
        justify-content: center;
    }

    .flex-center button,
    .alert {
        margin-top: 10px;
    }

    #template-preview {
        width: 200px;
    }

    .template-selector button {
        border-radius: 50%;
    }

    .template-indicator {
        display: inline-block;
        margin-top: -3px;
        margin-left: 95px;
    }

    .template-indicator button {
        margin-top: -13px;
    }

    #current {
        display: inline-block;
    }

    .modal-content {
        height: 800px;
    }

    .modal-body {
        height: 680px;
    }
</style>
<link rel="stylesheet" href="/assets/vendor/bootstrap-fileupload/bootstrap-fileupload.min.css" />
<link rel="stylesheet" href="/assets/vendor/bootstrap-colorpicker/css/bootstrap-colorpicker.css" />
<section role="main" class="content-body">
    <header class="page-header">
        <h2>Review Capture Campaigns</h2>

        <div class="right-wrapper pull-right" style="display:none;">
            <ol class="breadcrumbs">
                <li>
                    <a href="index.html">
                        <i class="fa fa-home"></i>
                    </a>
                </li>
                <li><span>Tables</span></li>
                <li><span>Advanced</span></li>
            </ol>

            <a class="sidebar-right-toggle" data-open="sidebar-right"><i class="fa fa-chevron-left"></i></a>
        </div>
    </header>

    <div class="row">
        <div class="col-lg-12">
            <section class="panel">
                <div class="panel-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <h2 class="h2 mt-none mb-sm text-dark text-bold">
                                Create a Review Capture Campaign
                            </h2>
                            <p class="text-muted font-13 m-b-15">
                                <!-- Description Goes Here <br /> -->
                                Fill out the values below to create your review capture funnel. The only value *required
                                is the "Campaign Name."
                            </p>
                        </div>

                        <?php
                        if (isset($_SESSION['msg']) && $_SESSION['msg'] != "") {
                        ?>
                            <div class="col-sm-12">
                                <div class="alert alert-info"><?php echo $_SESSION['msg']; ?></div>
                            </div>
                        <?php
                            unset($_SESSION['msg']);
                        }
                        ?>
                    </div>


                    <form id="capture_reviews" action="/action.php?action=capture_reviews" class="form-horizontal form-bordered" method="post" enctype="multipart/form-data">
                        <div class="form-group">
                            <label class="col-md-3 control-label">Campaign Name* <a data-toggle="popover" data-content="This is a required internal field where you come up with a unique name for your campaign. The customer won't see this." data-container="body">
                                    <i class="fa fa-question-circle " aria-hidden="true"></i>
                                </a></label>
                            <div class="col-md-6">
                                <input class="form-control" name="title" value="<?php echo @$row['title'] ?>" type="text" required="" placeholder="Internal Name of Your Campaign">
                            </div>
                        </div>

                        <?php
                        if (@$row['logo'] != "") {
                            $logo_required = "";
                        } else {
                            $logo_required = "required";
                        }
                        ?>

                        <!-- Type of review -->
                        <div class="form-group">
                            <label class="col-md-3 control-label">Type of the review</label>
                            <div class="col-md-6" style="text-align:left">
                                <input type="radio" name="review-type" id="video-review" value="video" <?= (isset($row['type']) && $row['type'] == 'video' ? 'checked' : '') ?>>
                                <label for="video-review">Video review</label>
                                <input type="radio" name="review-type" id="text-review" value="text" style="margin-left: 50px" <?= ((isset($row['type']) && $row['type'] == 'text') || (!isset($row['type'])) ? 'checked' : '') ?>>
                                <label for="text-review">Text review</label>
                            </div>
                        </div>
                        <div class="form-group" id="social-media-share">
                            <label class="col-md-3 control-label">Share on social media</label>
                            <div class="col-md-6" style="text-align:left">
                                <input type="checkbox" name="linkedin" id="linkedin-share" value="1" <?= (isset($row['share_linkedin']) && $row['share_linkedin'] == 1 ? 'checked' : '') ?>>
                                <label for="linkedin-share">Linked In</label>
                                <input type="checkbox" name="facebook" id="facebook-share" value="1" <?= (isset($row['share_facebook']) && $row['share_facebook'] == 1 ? 'checked' : '') ?>>
                                <label for="facebook-share">Facebook</label>
                                <input type="checkbox" name="twitter" id="twitter-share" value="1" <?= (isset($row['share_twitter']) && $row['share_twitter'] == 1 ? 'checked' : '') ?>>
                                <label for="twitter-share">Twitter</label>
                            </div>
                        </div>

                        <!-- Review template selector -->
                        <div class="form-group">
                            <label class="col-md-3 control-label">Review template</label>
                            <div class="col-md-6">
                                <div class="template-selector">
                                    <button type="button" class="btn" id="previous"><i class="fa fa-chevron-left"></i></button>
                                    <img id="template-preview" src="/uploads/templates/previews/">
                                    <button type="button" class="btn" id="next"><i class="fa fa-chevron-right"></i></button>
                                </div>
                                <div class="template-indicator">
                                    <div class="number-indicator center">
                                        <p id="current">1</p>/<?= sizeof($templates) ?>
                                    </div>
                                    <button id="open-preview-modal" data-toggle="modal" data-target="#template-preview-modal" type="button" class="btn btn-fill btn-primary">Preview</button>
                                </div>
                                <input type="text" name="review_template_id" id="review-template-id" hidden>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-3 control-label" style="text-align:left">Select Logo or Image <i>(<b>Upload
                                        limit:</b> 2MB | <b>Recommended dimensions:</b> 300px w x 300px h)</i></label>
                            <div class="col-md-6" style="text-align:left">
                                <div class="fileupload fileupload-new" data-provides="fileupload">
                                    <div class="input-append">
                                        <div class="uneditable-input">
                                            <i class="fa fa-file fileupload-exists"></i>
                                            <span class="fileupload-preview"></span>
                                        </div>
                                        <span class="btn btn-default btn-file">
                                            <span class="fileupload-exists">Change</span>
                                            <span class="fileupload-new">Select file</span>
                                            <input name="logo" type="file" accept="image/gif, image/jpeg, image/png" <?php echo ""; ?> />
                                            <input name="hidden_logo" type="hidden" value="<?php echo @$row['logo']; ?>" />
                                        </span>
                                        <a href="#" class="btn btn-default fileupload-exists" data-dismiss="fileupload">Remove</a>
                                    </div>
                                </div>
                                <?php if (@$row['logo'] != "") : ?>
                                    <img src="<?= (filter_var($row['logo'], FILTER_VALIDATE_URL) ? $row['logo'] : "/uploads/capture_reviews/" . $row['logo']) ?>" class="img-thumbnail" style="max-height: 100px;" />
                                    <br /><br />

                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-3 control-label">Embed a YouTube Video <a data-toggle="popover" data-content="Enter a YouTube link with &quot;/embed/&quot in your URL:
 e.g. https://www.youtube.com/embed/c_pBRQVKoLE" data-container="body">
                                    <i class="fa fa-question-circle " aria-hidden="true"></i>
                                </a>
                                <br><i>(instead of a logo)</i></label>
                            <div class="col-md-6">


                                <input class="form-control" type="text" name="youtube" value="<?= isset($row['youtube']) ? $row['youtube'] : '' ?>" placeholder="https://www.youtube.com/embed/c_pBRQVKoLE">

                                <label class="col-md-3 control-label"></label>

                            </div>
                            <div class="clearfix"></div>
                        </div>


                        <div class="form-group" value="<?= @$row['tags'] ?>">
                            <label class="col-md-3 control-label">
                                <span class="">Tags</span>


                                <a data-toggle="popover" data-content="Enter words that groups your reviews together. These labels will automatically categorize your reviews so that you can tactically display them." data-container="body">
                                    <i class="fa fa-question-circle " aria-hidden="true"></i>
                                </a>

                            </label>
                            <div class="col-md-6">

                                <input type="text" value="<?= isset($row['tags']) ? @$row['tags'] : '' ?> " data-role="tagsinput" name="tags" class="form-control" placeholder="<?= (isset($row['tags']) && trim($row['tags']) == '' ? 'Type a Tag' : 'Type a Tag') ?> ">
                                <br><i>(Enter a word press enter or space)</i>

                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-3 control-label">
                                <span class="">Name of Business</span>
                                <a data-toggle="popover" data-content="This text is usually at the very top of the template page" data-container="body">
                                    <i class="fa fa-question-circle " aria-hidden="true"></i>
                                </a>


                            </label>
                            <div class="col-md-6">
                                <input class="form-control" name="name_of_business" value="<?php echo @$row['name_of_business'] ?>" type="text" placeholder="Type the Name of Your Business">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-3 control-label">

                                <span class="">Review Capture Title or Headline</span>
                                <a data-toggle="popover" data-content="<?= htmlspecialchars('<head>') ?> Title or headline at the top of page and content of <?= htmlspecialchars('<h1>') ?> at the template page" data-container="body">
                                    <i class="fa fa-question-circle " aria-hidden="true"></i>
                                </a>


                            </label>
                            <div class="col-md-6">
                                <input class="form-control" name="page_title" value="<?php echo @$row['page_title'] ?>" type="text" placeholder="Type the headline or title of the page here">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-3 control-label">
                                <span class="">Description &amp; Instructions</span>

                                <a data-toggle="popover" data-content="These are the specific instructions you'll want to provide your customer. Best practices show that you want to want to be specific and request for the reviewer to be as detailed as possible. Encourage your reviewer to discuss their personal experience with your business." data-container="body">
                                    <i class="fa fa-question-circle " aria-hidden="true"></i>
                                </a>


                            </label>
                            <div class="col-md-6">
                                <textarea class="form-control" name="description"><?= @$row['description'] ?></textarea>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-3 control-label">
                                <span class="">List a Reward (Optional)</span>
                                <a data-toggle="popover" data-content="This is a reward or incentive that you communicate to encourage people to leave their email, if it's not blank the reviewer will be asked to enter email for obtaining the reward." data-container="body">
                                    <i class="fa fa-question-circle " aria-hidden="true"></i>
                                </a>

                            </label>
                            <div class="col-md-6">
                                <div class="input-group mb-sm">
                                    <textarea class="form-control" name="reward"><?= @$row['reward'] ?></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-3 control-label">
                                <span class="">Webhook for Email Capture</span>
                                <a data-toggle="popover" data-content="Enter your webhook URL to send json data with reviewer's name, email, review and rating. It can be used for handling reward request event. Zapier's webhook URL is fully supported." data-container="body">
                                    <i class="fa fa-question-circle " aria-hidden="true"></i>
                                </a>
                            </label>
                            <div class="col-md-6">

                                <label>You can automatically import emails by listing the webhook URL that your email
                                    marketing tool or CRM uses to capture data. If you're unaware what this URL is, see
                                    your provider's help documentation or contact their support. The data is sent as
                                    JSON data. <a href="https://zapier.com/help/doc/how-get-started-webhooks-zapier" target="_blank">See Zapier's Webhook Guide</a></label>
                                <div class="input-group mb-sm">

                                    <input type="text" value="<?= @$row['reward_webhook'] ?> " name="reward_webhook" class="form-control" placeholder="https://hooks.zapier.com/hooks/catch/4598128/ovk1zwu/">
                                    <div class="input-group-addon">
                                        <a data-toggle="tooltip" data-placement="top" title="Use a webhook URL to collect data e.g. Zapier's: https://hooks.zapier.com/hooks/catch/4598128/ovk1zwu/ ">
                                            <i class="fa fa-question-circle " aria-hidden="true"></i>
                                        </a>


                                    </div>
                                </div>

                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-3 control-label">


                                <span class="">Footer/Terms Copy and Links</span>
                                <a data-toggle="popover" data-content="The legal copy and protection for your business. This text is usually at the very end of the page. If you are in Europe, you'll want to include GDPR and/or terms of service and privacy links." data-container="body">
                                    <i class="fa fa-question-circle " aria-hidden="true"></i>
                                </a>

                            </label>
                            <div class="col-md-6">
                                <textarea class="form-control" name="footer_text"><?= @$row['footer_text'] ?></textarea>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-3 control-label">

                                <span class="">At Which Rating Do You Want to Prompt a Feedback Form?</span>

                                <a data-toggle="popover" data-content="If the reviewer sets a rating less than this value, then the reviewer will be asked to leave additional feedback after the review will be provided <?= htmlspecialchars('"feedback"') ?> tag" data-container="body">
                                    <i class="fa fa-question-circle " aria-hidden="true"></i>
                                </a>

                            </label>
                            <div class="col-md-6">
                                <div class="col-md-6">
                                    <input class="form-control" name="min_rating" value="<?= round(@$row['min_rating'], 1) ?>">
                                </div>
                                <div class="clearfix">

                                </div>
                                <div class="col-md-6">
                                    <span class="help-block text-center"><?= @round($row['min_rating'], 1) ?></span>


                                </div>
                            </div>
                        </div>

                        <div class="form-group ">
                            <label class="col-md-3 control-label">

                                <span class="">Google Sheets Integration (Premium)<br> View <a href="https://tutorials.smbreviewer.com/l/t3jbt483x1-z8j3szbquw" target="_blank">Step-by-Step Tutorial</a> </span>

                                <a data-toggle="popover" data-html="true" data-content="Enter <a target='_blank' href='https://developers.google.com/sheets/api/guides/concepts'>Spreadsheet Id</a> of your Google Sheet document and select the specific sheet (usually sheet1) to automatically update your Google Sheet with new emails." data-container="body">
                                    <i class="fa fa-question-circle " aria-hidden="true"></i>
                                </a>

                            </label>
                            <?php if ($db->isUserCurrentPlanDefault($_SESSION['user_id']) == false) : ?>


                                <div class="col-md-6">

                                    <div class="input-group mb-sm">
                                        <div class="input-group-addon">
                                            <input type="checkbox" class="" name="enable_google_sheets" value="<?= $row['enable_google_sheets'] ?>" <?= ((int)$row['enable_google_sheets'] == 0 ? "" : " checked") ?>>
                                        </div>


                                        <input class="form-control" type="text" name="google_spread_sheet_id" value="<?= $row['google_spread_sheet_id'] ?>" placeholder="enter google spread sheet id" <?= (trim($row['google_access_token']) != '' ? "" : " disabled") ?>>

                                        <?php if ($row['google_spread_sheet_id'] != "" && $row['enable_google_sheets'] == 1) : ?>
                                            <select class="form-control" name="google_sheet_id">
                                                <?php foreach ($google_spread_sheet_data['sheets'] as $sheet) : ?>
                                                    <option value="<?= $sheet['properties']['title'] ?>" <?= ($sheet['properties']['title'] == $row['google_sheet_id'] ? "selected" : "") ?>><?= $sheet['properties']['title'] ?></option>
                                                <?php endforeach; ?>

                                            </select>
                                        <?php endif; ?>
                                        <div class="input-group-addon">

                                            <button type="button" id="set_google_sheet_id" class="btn btn-primary  <?= ($row['enable_google_sheets'] == 1 ? "btn-fill " : " disabled") ?> <?= (trim($row['google_spread_sheet_id']) != '' ? ' reset' : ' set') ?>"> <?= (trim($row['google_spread_sheet_id']) != '' ? '<i class="fa fa-refresh" aria-hidden="true"></i>' : '<span class="ace-icon fa fa-check-square-o bigger-120"></span> Set') ?></button>

                                        </div>

                                    </div>
                                </div>


                            <?php else : ?>
                                <div class="col-md-6 form-inline">
                                    Want to automate your email capture with Google Sheets so that you can funnel your
                                    leads almost anywhere? <a href="/profile/" target="_blank">Upgrade now for just
                                        $5</a>
                                </div>
                            <?php endif; ?>

                        </div>

                        <div class="form-group" style="text-align:left;">
                            <label class="col-md-3 control-label" style="text-align:left;">



                                <span class="" style="text-align:left;">Online Directory Post Requests
                                    <br><i>Which review directories do you want to request to customer to post on if the review is positive?</i>
                                </span>


                                <a data-toggle="popover" data-content="If a reviewer leaves a positive review, then the review message is automatically copied to your clipboard, and the reviewer will be asked to leave the review on your business's review directory page." data-container="body">
                                    <i class="fa fa-question-circle " aria-hidden="true"></i>
                                </a>
                            </label>
                            <div class="col-md-6">


                                <div class="input-group mb-sm">
                                    <div class="input-group-addon">
                                        <input type="checkbox" class="" name="enable_review_directories_google" value="<?= isset($row['enable_review_directories_google']) ? $row['enable_review_directories_google'] : '' ?>" <?= (isset($row['enable_review_directories_google']) && (int)$row['enable_review_directories_google'] != 0 ? "checked" : "") ?>>
                                    </div>
                                    <input class="form-control" type="text" name="review_directories_google" value="<?= isset($row['review_directories_google']) ? $row['review_directories_google'] : '' ?>" placeholder="Google My Business short name" data-toggle="tooltip" data-placement="top" title="Google My Business Username">
                                    <div class="input-group-addon">
                                        <a target="_blank" href="https://support.google.com/business/answer/9273900" class="text-small"><i class="fa fa-question-circle" aria-hidden="true"></i></a>
                                    </div>

                                </div>

                                <div class="input-group  mb-sm">
                                    <div class="input-group-addon">
                                        <input type="checkbox" class="" name="enable_review_directories_facebook" value="<?= isset($row['enable_review_directories_facebook']) ? $row['enable_review_directories_facebook'] : "" ?>" <?= (isset($row['enable_review_directories_facebook']) && (int)$row['enable_review_directories_facebook'] != 0 ? "checked" : "") ?>>
                                    </div>
                                    <input class="form-control" type="text" name="review_directories_facebook" value="<?= isset($row['review_directories_facebook']) ? $row['review_directories_facebook'] : "" ?>" placeholder="Facebook Page Username" data-toggle="tooltip" data-placement="top" title="Facebook Page Username">
                                    <div class="input-group-addon">
                                        <a data-toggle="popover" data-content="The alias right after https://www.facebook.com/ <br> for example, if your page is https://www.facebook.com/<i>smbreviewer</i> your page name is <i>smbreviewer</i>" data-html="true" data-placement="bottom" data-container="body">
                                            <i class="fa fa-question-circle " aria-hidden="true"></i>
                                        </a>
                                    </div>
                                </div>


                                <div class="input-group  mb-sm">
                                    <div class="input-group-addon">
                                        <input type="checkbox" class="" name="enable_review_directories_yelp" value="<?= isset($row['enable_review_directories_yelp']) ? $row['enable_review_directories_yelp'] : "" ?>" <?= (isset($row['enable_review_directories_yelp']) && (int)$row['enable_review_directories_yelp'] != 0 ? "checked" : "") ?>>
                                    </div>
                                    <input class="form-control" type="text" name="review_directories_yelp" value="<?= isset($row['review_directories_yelp']) ? $row['review_directories_yelp'] : "" ?>" placeholder="Yelp Biz Page ID" data-toggle="tooltip" data-placement="top" title="Yelp Biz Page ID">
                                    <div class="input-group-addon">
                                        <a data-toggle="popover" data-content="Go to your Yelp Biz page and click write &quot;review,&quot; a page like https://www.yelp.com/writeareview/biz/<b>xxxxxxxx</b>?return_url=yyyyy will open <br> The identifier located here:<i>xxxxxxxx</i> is your Biz Page ID" data-html="true" data-placement="bottom" data-container="body">
                                            <i class="fa fa-question-circle " aria-hidden="true"></i>
                                        </a>
                                    </div>


                                </div>

                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <input type="checkbox" class="" name="enable_review_directories_custom" value="<?= isset($row['enable_review_directories_custom']) ? $row['enable_review_directories_custom'] : "" ?>" <?= (isset($row['enable_review_directories_custom']) && (int)$row['enable_review_directories_custom'] != 0 ? "checked" : "") ?>>
                                    </div>
                                    <input class="form-control" type="text" name="review_directories_custom" value="<?= isset($row['review_directories_custom']) ? $row['review_directories_custom'] : "" ?>" placeholder="Custom URL">

                                    <?php
                                    if (@$row['custom_logo'] != "") {
                                        $custom_logo_required = "";
                                    } else {
                                        $custom_logo_required = "required";
                                    }
                                    ?>


                                    <div class="fileupload fileupload-new <?= (isset($row['enable_review_directories_custom']) && (int)$row['enable_review_directories_custom'] == 1 ? ' ' : " hidden") ?>" data-provides="fileupload">

                                        <div class="input-append">
                                            <div class="uneditable-input">
                                                <i class="fa fa-file fileupload-exists"></i>
                                                <span class="fileupload-preview"></span>
                                            </div>
                                            <span class="btn btn-default btn-file">
                                                <span class="fileupload-exists">Change</span>
                                                <span class="fileupload-new">Select file</span>
                                                <input name="custom_logo" type="file" accept="image/gif, image/jpeg, image/png" <?php echo ""; ?> />
                                                <input name="hidden_custom_logo" type="hidden" value="<?php echo @$row['custom_logo']; ?>" />
                                            </span>
                                            <a href="#" class="btn btn-default fileupload-exists" data-dismiss="fileupload">Remove</a>
                                        </div>
                                        <!-- <span>(Will be resized to 150x150)</span> -->
                                    </div>

                                    <?php if (@$row['custom_logo'] != "") : ?>
                                        <img src="<?= (filter_var($row['custom_logo'], FILTER_VALIDATE_URL) ? $row['logo'] : "/uploads/capture_reviews/" . $row['custom_logo']) ?>" class="img-thumbnail" style="max-height: 100px;" />
                                        <br /><br />

                                    <?php endif; ?>


                                </div>
                                <label class="col-md-12">*No more than 3 are recommended. One is preferred.</label>

                            </div>


                            <div class="clearfix">

                            </div>
                        </div>

                        <div class="form-group">

                            <label class="col-md-3 control-label">Please choose your primary and secondary colors (by
                                entering a 6-digit hex code or by choosing a color. </label>
                            <div class="col-md-6">
                                <div class="input-group colorpicker-component mb-xs" data-toggle="tooltip" data-placement="top" title="Color of Page Title">
                                    <div class="input-group-addon">Primary</div>
                                    <input type="text" name="primary_font_color" value="<?= (isset($row['primary_font_color']) && $row['primary_font_color'] != '' ? $row['primary_font_color'] : "#000000") ?>" class="form-control" />
                                    <span class="input-group-addon"><i></i></span>
                                </div>
                                <div class="input-group colorpicker-component" data-toggle="tooltip" data-placement="top" title="Color for Description & Instructions">
                                    <div class="input-group-addon">Secondary</div>
                                    <input type="text" name="secondary_font_color" value="<?= (isset($row['secondary_font_color']) && $row['secondary_font_color'] != '' ? $row['secondary_font_color'] : "#000000") ?>" class="form-control" />
                                    <span class="input-group-addon"><i></i></span>
                                </div>
                            </div>
                            <div class="clearfix">

                            </div>


                        </div>

                        <div class="form-group">
                            <label class="col-md-3 control-label">Font Family:</label>
                            <div class="col-md-6">
                                <div class="input-group mb-xs font-select-component" data-toggle="tooltip" data-placement="top" title="Font for Page Title">
                                    <div class="input-group-addon">Primary</div>
                                    <select name="primary_font_family" class="form-control populate font-selection">
                                        <option value="">Default</option>
                                        <?php foreach ($fonts as $font) : ?>

                                            <option value="<?= $font ?>" <?= (@$row['primary_font_family'] == $font ? 'selected' : '') ?>><?= $font ?></option>


                                        <?php endforeach; ?>
                                    </select>
                                </div>


                                <div class="input-group font-select-component" data-toggle="tooltip" data-placement="top" title="Font for Description & Instructions">
                                    <div class="input-group-addon">Secondary</div>
                                    <select name="secondary_font_family" class="form-control populate font-selection">
                                        <option value="">Default</option>
                                        <?php foreach ($fonts as $font) : ?>

                                            <option value="<?= $font ?>" <?= (@$row['secondary_font_family'] == $font ? 'selected' : '') ?>><?= $font ?></option>


                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>


                        <div class="form-group">
                            <label class="col-md-3 control-label">Receive an email when a customer leaves a negative
                                review? (Optional)</label>
                            <div class="col-md-6">
                                <div class="input-group" data-toggle="tooltip" data-placement="top" title="Email address for negative review notiifcations">
                                    <div class="input-group-addon">
                                        <input type="checkbox" class="" name="enable_email_for_receiving_negative_review" value="<?= isset($row['enable_email_for_receiving_negative_review']) ? $row['enable_email_for_receiving_negative_review'] : "" ?>" <?= (isset($row['enable_email_for_receiving_negative_review']) && (int)$row['enable_email_for_receiving_negative_review'] != 0 ? "checked" : "") ?>>
                                    </div>
                                    <input class="form-control" type="email" name="email_for_receiving_negative_review" value="<?= isset($row['email_for_receiving_negative_review']) ? $row['email_for_receiving_negative_review'] : "" ?>" placeholder="enter your email address">
                                </div>
                            </div>
                            <div class="clearfix"></div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-3 control-label">
                                <span class="">Redirect URL</span>

                                <a data-toggle="popover" data-html="true" data-content="After a reviewer leaves a review or feedback, the user will be redirected to the following page within about 5 seconds." data-container="body">
                                    <i class="fa fa-question-circle " aria-hidden="true"></i>
                                </a>

                            </label>
                            <div class="col-md-6" data-toggle="tooltip" data-placement="top" title="Redirect URL">
                                <input class="form-control" name="redirect_url" value="<?= @$row['redirect_url'] ?>">
                            </div>
                        </div>


                        <div class="form-group" style="text-align:left">
                            <label class="col-md-3 control-label" style="text-align:left">Short.io Brandable URL
                                Integration <br> Would you like to customize and brand your URL with your *free* <a href="https://go.smbreviewer.com/short-io" target="_blank">Short.io</a> account?
                                Enter your *Secret* <a href="https://short.io/features/api" target="_blank">API key</a>
                                and select a domain.</label>
                            <div class="col-md-4">
                                <?php if (isset($row['short_io_api_key']) && $row['short_io_api_key'] != '') : ?>
                                    <div class="input-group" id="short-io-group">
                                        <div class="input-group-addon" style="width: 38px; height: 98px" id="input-group-addon-shortio">
                                            <input <?= isset($_REQUEST['id']) ? '' : 'disabled' ?> type="checkbox" class="" name="enable_short_io" id="enable_short_io" value="<?= isset($row['enable_short_io']) ? $row['enable_short_io'] : "" ?>" <?= (isset($row['enable_short_io']) && (int)$row['enable_short_io'] != 0 ? "checked" : "") ?>>
                                        </div>

                                        <div class="<?= isset($_REQUEST['id']) ? 'hidden' : '' ?>">
                                            <div class="alert alert-secondary" role="alert">To use short.io you have to save a campaign first.</div>
                                        </div>

                                        <div id="short-io-container">
                                            <?php if (isset($row['short_io_api_key']) && $row['short_io_api_key'] != "") : ?>
                                                <select class="form-control" name="short_io_domain" data-toggle="tooltip" data-placement="top" title="Short.io Domain">
                                                    <?php foreach ($short_io_domains as $domain) : ?>
                                                        <option value="<?= $domain['hostname'] ?>" <?= ($domain['hostname'] == $row['short_io_domain'] ? "selected" : "") ?>><?= $domain['hostname'] ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            <?php endif; ?>

                                            <input type="text" class="form-control" name="custom_link" id="custom_link" placeholder="Enter custom url handle (optional)">
                                            <p class="form-control <?= ($short_io_domain_link != "" ? "hidden" : "") ?>" id="short_io_message">Short.io URL is not set</p>
                                            <a target="_blank" class="form-control <?= ($short_io_domain_link == "" ? "hidden" : "") ?>" id="short_io_domain_link" href="<?= $short_io_domain_link ?>" data-toggle="tooltip" data-placement="top" title="Short.io link"><?= $short_io_domain_link ?></a>
                                        </div>
                                    </div>
                                <?php else : ?>
                                    Please connect your account to short.io in settings to enable this feature.
                                <?php endif; ?>

                                <div class="alert alert-danger" role="alert" id="short-io-error"></div>
                            </div>

                            <div class="clearfix"></div>

                            <div class="col-md-3"></div>

                            <div class="col-md-4 flex-center">
                                <button type="button" id="set_short_io" class="btn  btn-fill btn-primary <?= (isset($row['enable_short_io']) && (int)$row['enable_short_io'] != 0 ? '' : "hidden") ?>">
                                    <span class="ace-icon fa fa-check-square-o bigger-120"></span> Set
                                </button>
                            </div>

                            <div class="clearfix"></div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-3 control-label"></label>
                            <div class="col-md-6">
                                <input name="id" type="hidden" value="<?php echo @$_REQUEST['id']; ?>" />
                                <button type="submit" name="submit" value="submit" class="btn  btn-fill btn-success">
                                    <span class="ace-icon fa fa-save bigger-120"></span> Save
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </section>
        </div>
    </div>
    <!-- end: page -->
</section>
</div>
</section>

<!-- Template preview modal -->
<div class="modal fade" id="template-preview-modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="template-preview-modal-title">Modal title</h5>
            </div>
            <div class="modal-body">
                <iframe id="preview-iframe" src="http://localhost/crc/1149e6d531608300e900b16d9ee24794" frameborder="0" style="overflow:hidden;overflow-x:hidden;overflow-y:hidden;height:100%;width:100%;position:absolute;top:0px;left:0px;right:0px;bottom:0px" height="100%" width="100%"></iframe>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal google app auth  -->
<div class="modal fade" id="googleAppModal" tabindex="-1" role="dialog" aria-labelledby="googleAppModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="googleAppModalLabel">Google app installation</h4>
            </div>
            <div class="modal-body">
                <iframe src="" frameborder="0" style="overflow:hidden;overflow-x:hidden;overflow-y:hidden;height:100%;width:100%;position:absolute;top:0px;left:0px;right:0px;bottom:0px" height="100%" width="100%"></iframe>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<?php include_once('footer_default.php'); ?>

<link href="/assets/vendor/bootstrap-slider/css/bootstrap-slider.min.css" rel="stylesheet">
<script src="/assets/vendor/bootstrap-slider/bootstrap-slider.min.js"></script>
<script src="/assets/vendor/bootstrap-colorpicker/js/bootstrap-colorpicker.js"></script>
<link href="/assets/vendor/summernote/summernote.css" rel="stylesheet">
<script src="/assets/vendor/summernote/summernote.js"></script>


<script type="text/javascript">
    let templates = [];
    let selectedTemplateIndex;

    <?php foreach ($templates as $template) : ?>
        templates.push(<?= json_encode($template, true) ?>);
    <?php endforeach; ?>

    let selectedTemplate = <?= isset($row['review_template_id'])&&$db->getReviewTemplate($row['review_template_id'])!=null ? json_encode($db->getReviewTemplate($row['review_template_id']), true) : json_encode($template, true) ?>

    $('.template-selector #next').click(function() {
        selectedTemplateIndex++;
        updateTemplateSelectorButtons();
        setTemplate(templates[selectedTemplateIndex]);
    });

    $('.template-selector #previous').click(function() {
        selectedTemplateIndex--;
        updateTemplateSelectorButtons();
        setTemplate(templates[selectedTemplateIndex]);
    });

    $('#open-preview-modal').click(function() {
        setTemplateInModal(templates[selectedTemplateIndex]);
    });

    function initializeTemplatePreview() {
        templates.forEach((template) => {
            if (template.id == selectedTemplate.id) {
                selectedTemplateIndex = templates.indexOf(template);
            }
        });

        updateTemplateSelectorButtons();


        //temporary solution if selected template is undefined in the list of live templates
        if (typeof templates[selectedTemplateIndex] == "undefined"  ) {
          selectedTemplateIndex = 0;
        }


        setTemplate(templates[selectedTemplateIndex]);
    }

    function updateTemplateSelectorButtons() {
        if (selectedTemplateIndex == 0) {
            $('.template-selector #previous').css('visibility', 'hidden');
        } else {
            $('.template-selector #previous').css('visibility', 'visible');
        }

        if (selectedTemplateIndex == templates.length - 1) {
            $('.template-selector #next').css('visibility', 'hidden');
        } else {
            $('.template-selector #next').css('visibility', 'visible');
        }
    }

    function setTemplate(template) {

        $('#current').html(selectedTemplateIndex + 1);
        $('#template-preview').attr('src', '/uploads/templates/' + encodeURIComponent(template.name) + '/' + encodeURIComponent(template.preview_image));
        $('#review-template-id').val(template.id);
    }

    function setTemplateInModal(template) {
        $('#template-preview-modal-title').html(template.name);
        $('#preview-iframe').attr('src', '/crc/1149e6d531608300e900b16d9ee24794?preview=true&preview_id=' + template.id);
    }

    $(document).ready(function() {
        initializeTemplatePreview();

        $('#short-io-error').hide();
        if ($('#enable_short_io').is(':checked')) {
            $('#short-io-container').show();
            $('#input-group-addon-shortio').css('border-right', '0');
        } else {
            $('#short-io-container').hide();
            $('#input-group-addon-shortio').css('border-right', '1px solid #ccc');
        }

        $('#enable_short_io').click(function() {
            if ($('#enable_short_io').is(':checked')) {
                $('#short-io-container').show();
                $('#input-group-addon-shortio').css('border-right', '0');
            } else {
                $('#short-io-container').hide();
                $('#input-group-addon-shortio').css('border-right', '1px solid #ccc');
            }
        });

        if ($('#video-review').is(':checked')) {
            $('#social-media-share').show();
        } else {
            $('#social-media-share').hide();
        }

        $('#video-review').click(function() {
            if ($('#video-review').is(':checked')) {
                $('#social-media-share').show();
            } else {
                $('#social-media-share').hide();
            }
        });

        $('#text-review').click(function() {
            if ($('#video-review').is(':checked')) {
                $('#social-media-share').show();
            } else {
                $('#social-media-share').hide();
            }
        });

        $('textarea[name="description"]').summernote();

        $('textarea[name="reward"]').summernote({
            toolbar: [
                ['font', ['bold', 'underline', 'clear']],
                ['fontname', ['fontname']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['insert', ['link']],
                ['view', ['codeview']],
            ]
        });

        $('textarea[name="footer_text"]').summernote({
            toolbar: [
                ['font', ['bold', 'underline', 'clear']],
                ['fontname', ['fontname']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['insert', ['link']],
                ['view', ['codeview']],
            ]
        });

        $('input[data-role="tagsinput"]').tagsinput({
            confirmKeys: [13, 32, 44]
        });

        $('.colorpicker-component').colorpicker({
            format: 'hex'
        });

        function formatFont(fnt) {
            var $f = $(
                '<span style="font-family:' + fnt.id + '" > ' + fnt.text + '</span>'
            );
            return $f;
        };

        $(".font-selection").select2({
            formatSelection: formatFont,
            formatResult: formatFont
        });

        $('input[name^="enable"]').each(function() {
            var i = $(this).attr('name');
            i = i.replace('enable_', '');

            if ($(this).val() == 0) {
                $('input[name="' + i + '"]').attr('disabled', true);
                if (i == 'short_io') {
                    $('input[name="short_io_api_key"]').attr('disabled', true);
                }
            } else {
                $('input[name="' + i + '"]').attr('disabled', false);
                if (i == 'short_io') {
                    $('input[name="short_io_api_key"]').attr('disabled', false);
                }
            }
        });

        $('input[name^="enable"]').change(function() {
            var i = $(this).attr('name');
            i = i.replace('enable_', '');
            if ($(this).is(":checked")) {
                $(this).val("1");
                $('input[name="' + i + '"]').attr('disabled', false);
                if (i == 'review_directories_custom') {
                    $(this).closest('.input-group').find('.fileupload').removeClass('hidden');
                }
                if (i == 'short_io') {
                    $('input[name="short_io_api_key"]').attr('disabled', false);
                    $('#set_short_io').removeClass('hidden');
                }
            } else {
                $(this).val("0");
                $('input[name="' + i + '"]').attr('disabled', true);
                if (i == 'review_directories_custom') {
                    $(this).closest('.input-group').find('.fileupload').addClass('hidden');
                }
                if (i == 'short_io') {
                    $('input[name="short_io_api_key"]').attr('disabled', true);
                    $('#set_short_io').addClass('hidden');
                }
            }
        });


        $('input[name="min_rating"]').slider({
            precision: 0.1,
            step: 1,
            min: 0,
            max: 5,
            value: <?= round(@$row['min_rating'], 1) ?>
        });

        $('input[name="min_rating"]').on("slide", function(e) {
            $(this).closest('.form-group').find('.help-block').text(e.value);
        });

        $('input[name="min_rating"]').on("change", function() {
            $(this).closest('.form-group').find('.help-block').text($(this).val());

        });

        $('input[name="enable_google_sheets"]').change(function() {
            $('#set_google_sheet_id').addClass('disabled');
            if (this.checked) {
                $.ajax({
                    type: 'post',
                    url: "/action.php",
                    data: {
                        'action': 'capture_reviews_enable_google_sheets',
                        'id': '<?= isset($_REQUEST['id']) && (int)$_REQUEST['id'] != 0 ? $_REQUEST['id'] : '' ?>'
                    },
                    dataType: 'json',
                    success: function(response) {
                        var nW = window.open(response.result.login_url, "", "toolbar=no,status=no,menubar=no,location=center,scrollbars=no,resizable=no,height=500,width=657");
                        if (window.focus) {
                            nW.focus();
                        }

                        var nWClosed = setInterval(function() {
                            if (nW.closed) {
                                clearInterval(nWClosed);
                                $.post("/action.php", {
                                        'action': 'capture_reviews_get',
                                        'id': '<?= isset($_REQUEST['id']) && (int)$_REQUEST['id'] != 0 ? $_REQUEST['id'] : '' ?>',
                                        'user_id': '<?= $_SESSION['user_id'] ?>'
                                    },
                                    function(data) {
                                        data = $.parseJSON(data);
                                        if (data.result.google_access_token.trim() != '') {
                                            $('#set_google_sheet_id').removeClass('disabled');
                                            $('input[name="google_spread_sheet_id"]').attr('disabled', false);
                                        } else {
                                            $('input[name="enable_google_sheets"]').prop('checked', false);
                                        }
                                    });
                            }
                        }, 500);
                    },
                    error: function() {},
                    complete: function() {}
                })
            } else {
                $('input[name="enable_google_sheets"]').closest('.form-group').find('input[type="text"]').val('');
                $('input[name="enable_google_sheets"]').closest('.form-group').find('select').remove();
            }
        });


        $('#set_google_sheet_id').on("click", function() {
            if ($(this).hasClass('set')) {
                $.ajax({
                    type: 'post',
                    url: "/action.php",
                    data: {
                        'action': 'capture_reviews_set_google_sheets',
                        'google_spread_sheet_id': $('input[name="google_spread_sheet_id"]').val(),
                        'google_sheet_id': $('select[name="google_sheet_id"] option:selected').val(),
                        'id': '<?= isset($_REQUEST['id']) && (int)$_REQUEST['id'] != 0 ? $_REQUEST['id'] : '' ?>'
                    },
                    dataType: 'json',
                    success: function(response) {
                        $('select[name="google_sheet_id"]').remove();
                        if ((response.result) && (sheets = response.result.sheets)) {
                            var sheets_div = '<select class="form-control" name="google_sheet_id">';

                            $.each(sheets, function(k, v) {
                                sheets_div += '<option value="' + v.properties.title + '" ' + (v.properties.title == response.result.google_sheet_id ? 'selected' : '') + '>' + v.properties.title + '</option>';
                            });
                            sheets_div += '</select>';

                            $('input[name="google_spread_sheet_id"]').after($(sheets_div));
                            $('#set_google_sheet_id').removeClass('set').addClass('reset').html('<i class="fa fa-refresh" aria-hidden="true"></i>');
                        }
                    }
                });
            } else {
                $('input[name="enable_google_sheets"]').closest('.form-group').find('input[type="text"]').val('');
                $('input[name="enable_google_sheets"]').closest('.form-group').find('select').remove();
                $(this).removeClass('reset').addClass('set').html('<span class="ace-icon fa fa-check-square-o bigger-120"></span> Set');
            }
        });

        $('#set_short_io').on("click", function() {
            if ($('#custom_link').val().trim() == '') {
                console.log("Generating handle");
                setDefaultShortioUrl(false);
            } else {
                console.log("Setting a custom handle");
                setDefaultShortioUrl(true);
            }
        });

        updateShortUrlInput();
    });

    function setCustomShortioUrl() {
        $.ajax({
            type: 'post',
            url: "/action.php",
            data: {
                'action': 'capture_reviews_update_short_io',
                'short_io_api_key': "<?= $row['short_io_api_key'] ?>",
                'short_io_domain': $('select[name="short_io_domain"] option:selected').val(),
                'short_io_custom_handle': $('#custom_link').val(),
                'original_url': '<?= Tools::siteURL() . '/crc/' . Tools::encrypt_link(isset($row['id']) ? $row['id'] : 0) ?>',
                'id': '<?= isset($_REQUEST['id']) && (int)$_REQUEST['id'] != 0 ? $_REQUEST['id'] : '' ?>'
            },
            dataType: 'json',
            success: function(response) {
                console.log(response);
                if (response.link_result) {
                    $('#short_io_message').addClass("hidden");
                    $('#short_io_domain_link').removeClass("hidden");
                    $('#short_io_domain_link').attr("href", response.link_result);
                    $('#short_io_domain_link').html(response.link_result);
                    updateShortUrlInput();
                } else if (response.error) {
                    $('#short-io-error').show();
                    $('#short-io-error').html(response.error);
                }
            }
        });
    }

    function setDefaultShortioUrl(shouldCallSetCustom) {
        $('#short-io-error').hide();
        $.ajax({
            type: 'post',
            url: "/action.php",
            data: {
                'action': 'capture_reviews_set_short_io',
                'short_io_api_key': "<?= $row['short_io_api_key'] ?>",
                'short_io_domain': $('select[name="short_io_domain"] option:selected').val(),
                'original_url': '<?= Tools::siteURL() . '/crc/' . Tools::encrypt_link(isset($row['id']) ? $row['id'] : 0) ?>',
                'id': '<?= isset($_REQUEST['id']) && (int)$_REQUEST['id'] != 0 ? $_REQUEST['id'] : '' ?>'
            },
            dataType: 'json',
            success: function(response) {
                console.log(response);
                if (response.link_result) {
                    $('#short_io_message').addClass("hidden");
                    $('#short_io_domain_link').removeClass("hidden");
                    $('#short_io_domain_link').attr("href", response.link_result);
                    $('#short_io_domain_link').html(response.link_result);
                    if (shouldCallSetCustom) {
                        setCustomShortioUrl();
                    } else {
                        updateShortUrlInput();
                    }
                }
            }
        });
    }

    function updateShortUrlInput() {
        let handle = $('#short_io_domain_link').attr("href");

        if((typeof handle !== "undefined") && (handle != '')) {
            handle = handle.split('/');
            handle = handle[handle.length - 1];
            $('#custom_link').val(handle);
        }
    }
</script>
</body>

</html>