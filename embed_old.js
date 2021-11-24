(function(window) {
  var WIDGET_TYPE_SWIPER = 2;
  var WIDGET_TYPE_GRID = 3;
  // Localize jQuery variable
  var jQuery;

  /******** Load jQuery if not present *********/

  if (window.jQuery === undefined || window.jQuery.fn.jquery !== '2.2.4') {
    var script_tag = document.createElement('script');
    script_tag.setAttribute("type", "text/javascript");
    script_tag.setAttribute("src",
      "//cdnjs.cloudflare.com/ajax/libs/jquery/2.2.4/jquery.min.js");

    if (script_tag.readyState) {
      script_tag.onreadystatechange = function() { // For old versions of IE
        if (this.readyState == 'complete' || this.readyState == 'loaded') {
          scriptLoadHandler();
        }
      };
    } else { // Other browsers
      script_tag.onload = scriptLoadHandler;
    }
    // Try to find the head, otherwise default to the documentElement
    (document.getElementsByTagName("head")[0] || document.documentElement).appendChild(script_tag);
  } else {
    // The jQuery version on the window is the one we want to use
    jQuery = window.jQuery.noConflict(true);
    main();
  }


  /******** Called once jQuery has loaded ******/
  function scriptLoadHandler() {
    // Restore $ and window.jQuery to their previous values and store the
    // new jQuery in our local jQuery variable
    jQuery = window.jQuery.noConflict(true);
    // Call our main function
    main();
  }

  /******** Our main function ********/
  function main() {
    jQuery(document).ready(function($) {
      if (!window.jQuery) {
        //if no jquery was loaded before
        window.jQuery = jQuery;
      }

      // load css and js
      //$('head').append('<link rel="stylesheet" type="text/css" href="//cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" />');

      // swiper
      function loadSwiper(deffered, defferedData) {
        $('head').append('<link rel="stylesheet" type="text/css" href="//cdnjs.cloudflare.com/ajax/libs/Swiper/3.4.2/css/swiper.min.css" />');
        // var swiperDeferred = jQuery.Deferred();
        var swiperUrl = "//cdnjs.cloudflare.com/ajax/libs/Swiper/3.4.2/js/swiper.min.js";
        if (location.href.substring(0, 4) != 'http') {
          swiperUrl = 'https:' + swiperUrl;
        }
        $.getScript(swiperUrl, function(data, textStatus, jqxhr) {
          deffered.resolve(defferedData);
        });
      }

      function loadScrollBar(callback) {
        $('head').append('<link rel="stylesheet" type="text/css" href="https://reviewsonmywebsite.com/css/rocketScroll.css" />');
        $.when(
          $.getScript('https://reviewsonmywebsite.com/js/rocketHelpers.js'),
          $.getScript('https://reviewsonmywebsite.com/js/rocketScroll.js')
        ).done(function() {
          callback();
        });
      }


      //prepare data
      var reviewDataArray;
      if (window.review_token && window.review_target) {
        reviewDataArray = [{
          'container': window.review_target,
          'token': window.review_token
        }];
      } else {
        reviewDataArray = window.reviewData;
      }

      if (!reviewDataArray) {
        if (console) {
          console.warn('Review widget: `reviewData` varible is missing, no widget will be inserted into page');
        }
      } else if (Object.prototype.toString.call(reviewDataArray) !== '[object Array]') {
        reviewDataArray = [reviewDataArray];
      }

      // load reviews
      var deferredArray = [];

      function loadContent() {

        $('head').append('<link rel="stylesheet" type="text/css" href="' + window.application_url + 'assets/css/bootstrap.min.css" />');
        // $('head').append('<link rel="stylesheet" type="text/css" href="'+window.application_url+'assets/css/core.css" />');


        $('head').append('<script src="https://cdnjs.cloudflare.com/ajax/libs/rateYo/2.3.2/jquery.rateyo.min.js"></script>');
        $('head').append('<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/rateYo/2.3.2/jquery.rateyo.min.css">');



        //$('head').append('<script src="'+window.application_url+'assets/js/jquery.min.js"></script>');
        $('head').append('<script src="' + window.application_url + 'assets/js/bootstrap.min.js"></script>');

        $.get(window.application_url + "reviews.php?id=" + window.review_token + "&src=embed", function(data) {


          jQuery("#" + window.review_target).html(data).promise().done(function() {

            //your callback logic / code here
            jQuery("#" + window.review_target + " .user-rating").each(function() {
              var score = $(this).html();
              var color = $("#rating_color").val();
              if (score == '') {
                score = 4.0;
              }

              $(this).html("");
              $(this).rateYo({
                rating: score,
                starWidth: "15px",
                ratedFill: color,
                readOnly: true,
              })
            })

          });




        });

      }


      loadContent();
    });
  }
})(window); // We call our anonymous function immediately
