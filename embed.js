(function(window) {
  var WIDGET_TYPE_SWIPER = 2;
  var WIDGET_TYPE_GRID = 3;
  // Localize jQuery variable
  var jQuery_2_2_4;

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
    jQuery_2_2_4 = window.jQuery.noConflict(true);
    jQuery = jQuery_2_2_4;
    main();
  }


  /******** Called once jQuery has loaded ******/
  function scriptLoadHandler() {
    // Restore $ and window.jQuery to their previous values and store the
    // new jQuery in our local jQuery variable
    console.log('here2');

    jQuery_2_2_4 = window.jQuery.noConflict(true);

    // Call our main function
    main();
  }

  /******** Our main function ********/
  function main() {

    jQuery_2_2_4(function($) {

      console.log($().jquery); // This prints v1.4.2
      if (!window.jQuery) {
        //if no jquery was loaded before
        window.jQuery = jQuery_2_2_4;
        window.$ = jQuery_2_2_4;
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


      function loadContent(i=0) {

        $('head').append('<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/rateYo/2.3.2/jquery.rateyo.min.css">');
        $('head').append('<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css" integrity="sha512-5A8nwdMOWrSz20fDsjczgUidUBR8liPYU+WymTZP1lmY9G6Oc7HlZv156XqnsgNUzTyMefFTcsFH/tnJE/+xBg==" crossorigin="anonymous" />');

        $('body').append('<script src="https://cdnjs.cloudflare.com/ajax/libs/rateYo/2.3.2/jquery.rateyo.min.js"></script>');
        if ($('meta[name="viewport"]').length == 0) {
          $('head').append('<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">');
        }



        $.get(window.application_url + "reviews.php?id=" + reviewDataArray[i].token + "&src=embed", function(data) {
          


          $("#" + reviewDataArray[i].container).html(data).promise().done(function() {

             reviews_div = $("#" + reviewDataArray[i].container).find('.smb-reviews-container').first();

            $(".smb-reviews-container .src-type-google .user-rating,.smb-reviews-container .src-type-facebook .user-rating,.smb-reviews-container .src-type-custom .user-rating").each(function() {
              var score = $(this).html();
              var color = $("#" + reviewDataArray[i].container + " .smb-reviews-container").attr('rating_color');
              if (score == '') {
                score = 4.0;
              }

              $(this).html("");
              //rateYo is always loading for default jQuery
              jQuery(this).rateYo({
                rating: score,
                starWidth: "15px",
                ratedFill: color,
                readOnly: true,
              })
            });

            $('.r_date').each(function(i){
              if ( $(this).text().trim()!='') {
                var dt =(new Date(Date.parse($(this).text().trim()))).toLocaleDateString(navigator.language,{year: 'numeric', month: 'long', day: 'numeric' })
                $(this).text(dt);
              }
             });


            function slideShowFunction(t) {
              slideShow = setTimeout(function(){ $("#" + reviewDataArray[i].container + " .smb_reviews_slide .nav-next").trigger('slide'); }, t);
            }

            function renderPagination(style,obj) {

              if ((style=='list')
                  || (style=='grid')
                ){

                  var reviews_per_page  =  $(obj).attr('reviews_per_page');
                  var total_reviews= $(obj).find('ul li').length;
                  var total_pages = (reviews_per_page!=0 ? Math.ceil(total_reviews/reviews_per_page) : 1) ;
                  // $('#smb_reviews<?=$id?> ul li:nth-child(n+'+reviews_per_page+')').hide();
                  var pagination = '';

                    for (var i = 0; i < total_pages; i++) {
                      pagination=pagination+'<span class="page_number'+(i==0 ? ' active' : '')+'">'+(i+1)+'</span>';
                    }

                    if (total_pages >1) {
                        pagination='<span class="page_prev"><</span>'+pagination+'<span class="page_next">></span>';
                    }

                    $(obj).append('<div class="pagination" reviews_per_page="'+reviews_per_page+'" active_page="1" total_pages="'+total_pages+'">'+pagination+'</div>');

                    $(obj).find('ul li:nth-child(n+'+0+')').hide();
                    $(obj).find('li:nth-child(n+1):nth-child(-n+'+reviews_per_page+')').show();

              }
            }

            function changeActivePage(obj) {

            var reviews_div = $(obj).closest('.smb-reviews-container');
            var target_page =$(obj).text();
            var pagination = $(obj).closest('.pagination');
            var current_page = pagination.attr('active_page');
            var total_pages = pagination.attr('total_pages');
            var reviews_per_page = pagination.attr('reviews_per_page');
            pagination.children('span.active').removeClass('active');
            if (target_page=='>') {

              target_page = parseInt(current_page)+1;
            }
            else if (target_page=='<') {

              target_page = parseInt(current_page)-1;
            }

            if (parseInt(target_page)<1) {
            target_page=1;
            }

            if (parseInt(target_page)>parseInt(total_pages)) {
            target_page=total_pages;
            }

            var range_stop=parseInt(target_page)*parseInt(reviews_per_page);
            var range_start=range_stop-parseInt(reviews_per_page)+1;
            reviews_div.find('ul li:nth-child(n+'+0+')').hide();
            reviews_div.find('ul li:nth-child(n+'+range_start+'):nth-child(-n+'+range_stop+')').show();
            pagination.attr('active_page',target_page);
            pagination.children('span.page_number:nth-child('+(parseInt(target_page)+1)+')').addClass('active');
            $('html, body').animate({
                                scrollTop: reviews_div.offset().top-200
                            }, 100);
            }


            function addReadmore() {
              if((review_text_size =   $("#" + reviewDataArray[i].container + " .smb-reviews-container").attr('review_text_size')) && (parseInt(review_text_size) >0)){
                $("#" + reviewDataArray[i].container + " .smb-reviews-container .r_review").each(function(i){
                   var text = $(this).text();
                   text =  '<span class="read-more">'+text.substr(0,review_text_size)+'...[read more]</span>'+
                   '<span style="display:none;">'+text+'</span>';
                   $(this).html(text);
                });
              }
            }



          $(document).on("click","#" + reviewDataArray[i].container + " .smb-reviews-container .r_review .read-more", function() {
            $(this).next().show(200);
            $(this).remove();
            });



            $(document).on("click","#" + reviewDataArray[i].container + " .pagination span:not(.active)", function() {
              changeActivePage($(this));
            });

            $("#" + reviewDataArray[i].container + " .smb_reviews_slide .nav-prev, #" + reviewDataArray[i].container + " .smb_reviews_slide .nav-next").on("click slide", function(e) {

              var carousel_timeout = $("#" + reviewDataArray[i].container + " .smb_reviews_slide").attr('carousel_timeout');
              clearTimeout(slideShow);
              if (e.type == 'slide') {
              slideShowFunction(carousel_timeout);
              } else {
              slideShowFunction(carousel_timeout);
              }





              var a_s = $(this).siblings('.media-slide').find('li.active'),
                prev, next;
              a_s.removeClass('active');


              if ($(this).hasClass('nav-prev')) {

                if ((prev = a_s.prev()) && (prev.length > 0)) {
                  prev.css('margin-left', '-300%').addClass('active');

                } else {
                  a_s.closest('.media-slide').find('li').last().css('margin-left', '-300%').addClass('active');

                }

              } else {
                if ((next = a_s.next()) && (next.length > 0)) {
                  next.css('margin-left', '300%').addClass('active');
                } else {
                  a_s.closest('.media-slide').find('li').first().css('margin-left', '-300%').addClass('active');
                }
              }

              $(this).siblings('.media-slide').find('li.active').animate({
                "margin-left": "0"
              }, 200, function() {
                $(this).removeAttr('style');
              });

            });


            var slideShow;

            switch (reviews_div.attr("render_type")) {
              case 'list':
                    renderPagination('list',reviews_div);
                  break;
              case 'grid':
                  renderPagination('grid',reviews_div);
                  break;
              case 'slide':
                var carousel_timeout = $("#" + reviewDataArray[i].container + " .smb_reviews_slide").attr('carousel_timeout');
                slideShowFunction(carousel_timeout);
                  break;
            }


            reviews_div.show();

            addReadmore();




          });








        });

      }

      for (var i = 0; i < reviewDataArray.length; i++) {
         loadContent(i);
      }
    });
  }
})(window); // We call our anonymous function immediately
