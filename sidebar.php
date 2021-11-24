<!-- start: sidebar -->
<?php
$currentPage = $_SERVER['PHP_SELF'];
?>

<aside id="sidebar-left" class="sidebar-left">

  <div class="sidebar-header">
    <div class="sidebar-title" style="font-weight:bold;">
      Build Trust
    </div>
    <div class="sidebar-toggle hidden-xs" data-toggle-class="sidebar-left-collapsed" data-target="html" data-fire-event="sidebar-left-toggle">
      <i class="fa fa-bars" aria-label="Toggle sidebar"></i>
    </div>
  </div>

  <div class="nano">
    <div class="nano-content">
      <nav id="menu" class="nav-main" role="navigation">
        <ul class="nav nav-main">
             <li class="<?php if (strpos($directoryURI, 'campaign') != false) {
                        echo "nav-active";
                      } ?>">
            <a href="/dashboard/" class="waves-effect"><i class="fa fa-tachometer"></i><span> Dashboard </span></a>
          </li>
          <li class="<?php if (strpos($directoryURI, 'campaign') != false) {
                        echo "nav-active";
                      } ?>">
            <a href="/campaign/" class="waves-effect"><i class="fa fa-star-half-o"></i><span> Display Reviews </span></a>
          </li>
          <li class="<?php if (strpos($directoryURI, 'custom_reviews') != false) {
                        echo "nav-active";
                      } ?>">
            <a href="/custom_reviews/" class="waves-effect"><i class="fa fa-list-ul"></i><span> Your Reviews </span></a>
          </li>
          <?php if ($_SESSION['type'] == "1" or true) : ?>
            <li class="<?php if (strpos($directoryURI, 'capture_reviews') != false) {
                          echo "nav-active";
                        } ?>">
              <a href="/capture_reviews/" class="waves-effect"><i class="fa fa-puzzle-piece"></i><span> Capture Reviews </span></a>
            </li>

          <?php endif; ?>

          <li class="nav-parent <?php if ((strpos($directoryURI, 'settings') != false)
                                  || (strpos($directoryURI, 'google_settings') != false)
                                  || (strpos($directoryURI, 'paypal_settings') != false)
                                  || (strpos($directoryURI, 'general_settings') != false)
                                  || (strpos($directoryURI, 'yelp_settings') != false)
                                ) {
                                  echo " nav-expanded nav-active";
                                } ?>">
            <a>
              <i class="fa fa-gear" aria-hidden="true"></i>
              <span>Settings</span>
            </a>
            <ul class="nav nav-children">
              <li class="<?php if (strpos($directoryURI, '/settings/') != false) {
                            echo "nav-active";
                          } ?>">
                <a href="/settings/"><span>Facebook </span></a>
              </li>
              <li class="<?php if (strpos($directoryURI, 'google_settings') != false) {
                            echo "nav-active";
                          } ?>">
                <a href="/google_settings/"><span>Google </span></a>
              </li>
              <li class="<?php if (strpos($directoryURI, 'yelp_settings') != false) {
                            echo "nav-active";
                          } ?>">
                <a href="/yelp_settings/"><span>Yelp </span></a>
              </li>
              <li class="<?php if (strpos($directoryURI, 'short_io_settings') != false) {
                            echo "nav-active";
                          } ?>">
                <a href="/short_io_settings/">Short.io</a>
              </li>
              <?php if ($_SESSION['type'] == "1") : ?>
                <li class="<?= strpos($directoryURI, 'paypal_settings') ? "nav-active" : "" ?>">
                  <a href="/paypal_settings/"></i><span>Paypal</span></a>
                </li>
              <?php endif ?>
              <?php if ($_SESSION['type'] == "1" || $_SESSION['type'] == "3") : ?>
                <li class="<?= strpos($directoryURI, 'review_template_settings') ? "nav-active" : "" ?>">
                  <a href="/review_template_settings/"></i><span>Review templates</span></a>
                </li>
              <?php endif ?>
              <?php if ($_SESSION['type'] == "1") : ?>
                <li class="<?= strpos($directoryURI, 'general_settings') ? "nav-active" : "" ?>">
                  <a href="/general_settings/"><span> General</span></a>
                </li>
              <?php endif ?>
            </ul>
          </li>

          <?php if ($_SESSION['type'] == "1") : ?>
            <li class="<?php if (strpos($directoryURI, 'members') != false) {
                          echo "nav-active";
                        } ?>">
              <a href="/members/" class="waves-effect"><i class="fa fa-users"></i><span> Clients </span></a>
            </li>
            <li class="<?php if (strpos($directoryURI, 'plans') != false) {
                          echo "nav-active";
                        } ?>">
              <a href="/plans/" class="waves-effect"><i class="fa fa-money"></i><span> Pricing Plans </span></a>
            </li>
            <li class="<?php if (strpos($directoryURI, 'payments') != false) {
                          echo "nav-active";
                        } ?>">
              <a href="/payments/" class="waves-effect"><i class="fa fa-usd"></i><span> Payments </span></a>
            </li>
          <?php endif ?>

          <li class="<?php if (strpos($directoryURI, 'profile') != false) {
                        echo "nav-active";
                      } ?>">
            <a href="/profile/" class="waves-effect"><i class="fa fa-user"></i><span> Profile </span></a>
          </li>

          <li class="<?php if (strpos($directoryURI, 'tutorial') != false) {
                        echo "nav-active";
                      } ?>">
            <a href="/tutorial/" target="_blank" class="waves-effect"><i class="fa fa-file-code-o"></i><span> Tutorial </span></a>
          </li>

          <li class="<?php if (strpos($directoryURI, 'feature-requests') != false) {
                        echo "nav-active";
                      } ?>">
            <a href="/feature-requests/" class="waves-effect"><i class="fa fa-lightbulb-o"></i><span> Feature Requests </span></a>
          </li>
          <li class="<?php if (strpos($directoryURI, 'get-reviews') != false) {
                        echo "nav-active";
                      } ?>">
            <a href="/get-reviews/" class="waves-effect"><i class="fa fa-pencil-square"></i><span> Get Reviews</span></a>
          </li>
          <li class="">
            <a href="/privacy-policy/" class="waves-effect"><i class="fa fa-file-text-o"></i><span>Privacy &amp; Terms</span></a>
          </li>
          <li class="visible-xs">
            <a role="menuitem" tabindex="-1" href="/logout/"><i class="fa fa-power-off"></i> Logout</a>
          </li>
        </ul>
      </nav>
    </div>
  </div>
</aside>
<!-- end: sidebar -->