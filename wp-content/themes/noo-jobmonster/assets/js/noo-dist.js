!function(u){"use strict";u.fn.nooLoadmore=function(d,n){var e={contentSelector:null,contentWrapper:null,nextSelector:"div.navigation a:first",navSelector:"div.navigation",itemSelector:"div.post",dataType:"html",finishedMsg:"<em>Congratulations, you've reached the end of the internet.</em>",loading:{speed:"fast",start:void 0},state:{isDuringAjax:!1,isInvalidPage:!1,isDestroyed:!1,isDone:!1,isPaused:!1,isBeyondMaxPage:!1,currPage:1}};d=u.extend(e,d);return this.each(function(){var e=this,o=u(this),a=o.find(".loadmore-wrap"),s=o.find(".loadmore-action"),l=s.find(".btn-loadmore"),c=s.find(".loadmore-loading");d.contentWrapper=d.contentWrapper||a;if(u(d.nextSelector).length){d.callback=function(e,o){n&&n.call(u(d.contentSelector)[0],e,d,o)},d.loading.start=d.loading.start||function(){l.hide(),u(d.navSelector).hide(),c.show(d.loading.speed,u.proxy(function(){t(d)},e))};var t=function(o){var e=u(o.nextSelector).attr("href");e=function(e){if(e.match(/^(.*?)\b2\b(.*?$)/))e=e.match(/^(.*?)\b2\b(.*?$)/).slice(1);else if(e.match(/^(.*?)2(.*?$)/)){if(e.match(/^(.*?page=)2(\/.*|$)/))return e=e.match(/^(.*?page=)2(\/.*|$)/).slice(1);e=e.match(/^(.*?)2(.*?$)/).slice(1)}else{if(e.match(/^(.*?page=)1(\/.*|$)/))return e=e.match(/^(.*?page=)1(\/.*|$)/).slice(1);d.state.isInvalidPage=!0}return e}(e);var a,t,n,i,r;o.callback;o.state.currPage++,void 0!==o.maxPage&&o.state.currPage>o.maxPage?o.state.isBeyondMaxPage=!0:(a=e.join(o.state.currPage),(n=u("<div/>")).load(a+" "+o.itemSelector,void 0,function(e){if(0===(i=n.children()).length)return l.hide(),void s.append('<div style="margin-top:5px;">'+o.finishedMsg+"</div>").animate({opacity:1},2e3,function(){s.fadeOut(o.loading.speed)});for(t=document.createDocumentFragment();n[0].firstChild;)t.appendChild(n[0].firstChild);u(o.contentWrapper)[0].appendChild(t),r=i.get(),c.hide(),l.show(o.loading.speed),o.callback(r)}))};l.on("click",function(e){e.stopPropagation(),e.preventDefault(),d.loading.start.call(u(d.contentWrapper)[0],d)})}})};var e=function(){if(u(".navbar").length){var n=u(window),i=u("body"),r=u(".navbar").offset().top,s=0,l=u(".navbar"),c=u(".navbar").outerHeight(),d=0;i.hasClass("admin-bar")&&(d=u("#wpadminbar").outerHeight());var e=function(){if(992<(a=window,t="inner","innerWidth"in window||(t="client",a=document.documentElement||document.body),{width:a[t+"Width"],height:a[t+"Height"]}).width&&l.hasClass("fixed-top")){var e="navbar-fixed-top";l.hasClass("shrinkable")&&!i.hasClass("one-page-layout")&&(e+=" navbar-shrink");var o=r+c;if(n.scrollTop()+d>o){if(l.hasClass("navbar-fixed-top"))return;if(!l.hasClass("navbar-fixed-top"))return s=c,u(".navbar-wrapper").css({"min-height":s+"px"}),l.closest(".noo-header").css({position:"relative"}),void l.addClass(e).css("top",0-s).animate({top:d},300)}else{if(!l.hasClass("navbar-fixed-top"))return;l.removeClass(e),l.css({top:""}),u(".navbar-wrapper").css({"min-height":"none"}),l.closest(".noo-header").css({position:""})}}var a,t};n.bind("scroll",e).resize(e),i.hasClass("one-page-layout")&&(u('.navbar-scrollspy > .nav > li > a[href^="#"]').click(function(e){e.preventDefault();var o=u(this).attr("href").replace(/.*(?=#[^\s]+$)/,"");if(o&&u(o).length){var a=Math.max(0,u(o).offset().top);a=Math.max(0,a-(d+u(".navbar").outerHeight())+5),u("html, body").animate({scrollTop:a},{duration:800,easing:"easeInOutCubic",complete:window.reflow})}}),i.scrollspy({target:".navbar-scrollspy",offset:d+u(".navbar").outerHeight()}),u(window).resize(function(){i.scrollspy("refresh")}))}u(".noo-slider-revolution-container .noo-slider-scroll-bottom").click(function(e){e.preventDefault();var o=u(".noo-slider-revolution-container").outerHeight();u("html, body").animate({scrollTop:o},900,"easeInOutExpo")}),u(".masonry").each(function(){if(!u().isotope)return!1;var e=u(this),t=u(this).find(".masonry-container"),n=u(".company-letters a");t.isotope({itemSelector:".masonry-item",transitionDuration:"0.8s",masonry:{gutter:30}}),imagesLoaded(e,function(){t.isotope("layout")}),n.click(function(e){e.stopPropagation(),e.preventDefault();var o=jQuery(this);n.removeClass("selected"),o.addClass("selected");var a=o.attr("data-filter");t.isotope({itemSelector:".masonry-item",transitionDuration:"0.5s",masonry:{gutter:30},filter:a})})}),u("a[data-vc-tabs]").on("show.vc.tab shown.bs.tab",function(e){var o=u(u(e.target).attr("href"));o.find(".masonry-container").length&&o.find(".masonry-container").each(function(){u().isotope&&u(this).isotope({itemSelector:".masonry-item",transitionDuration:"0.8s",masonry:{gutter:30}})})}),u(window).scroll(function(){500<u(this).scrollTop()?u(".go-to-top").addClass("on"):u(".go-to-top").removeClass("on")}),u("body").on("click",".go-to-top",function(){return u("html, body").animate({scrollTop:0},800),!1}),u("body").on("click",".search-button",function(){return u(".searchbar").hasClass("hide")&&(u(".searchbar").removeClass("hide").addClass("show"),u(".searchbar #s").focus()),!1}),u("body").on("mousedown",u.proxy(function(e){var o=u(e.target);o.is(".searchbar")||0!==o.parents(".searchbar").length||u(".searchbar").removeClass("show").addClass("hide")},this))};u(document).ready(function(){u(".mc-subscribe-form").submit(function(e){e.preventDefault();var t=u(this),o=t.serializeArray();t.find("label.noo-message").remove(),u.ajax({type:"POST",url:nooL10n.ajax_url,data:o,success:function(e){var o=u.parseJSON(e),a="";o.success?""!==o.data&&(a='<label class="noo-message error" role="alert">'+o.data+"</label>",t.addClass("submited"),t.html(a)):""!==o.data&&(t.removeClass("submited"),u('<label class="noo-message" role="alert">'+o.data+"</label>").prependTo(t))},error:function(e){}})}),u('[data-toggle="tooltip"]').tooltip(),u(".noo-user-navbar-collapse").on("show.bs.collapse",function(){u(".noo-navbar-collapse").hasClass("in")&&u(".noo-navbar-collapse").collapse("hide")}),u(".noo-navbar-collapse").on("show.bs.collapse",function(){u(".noo-user-navbar-collapse").hasClass("in")&&u(".noo-user-navbar-collapse").collapse("hide")}),e()}),u(document).bind("noo-layout-changed",function(){e()}),u(window).ready(function(){0<u(".noo-megamenu").length&&u(".noo-megamenu").find("ul.noo-nav").addClass("sf-menu"),0<u(".company-info-content").length&&u(".company-info-content").readmore({speed:75,lessLink:'<a class="btn-readmore" href="#">'+noo_readmore.lessLink+"</a>",moreLink:'<a class="btn-readmore" href="#">'+noo_readmore.moreLink+"</a>"})}),u("body").on("click",".btn-quick-view-popup",function(){var a=u(this);return a.addClass("loading"),u.ajax({type:"POST",dataType:"json",url:nooMemberL10n.ajax_url,data:{action:"noo_quick_view_job",security:a.data("security"),job_id:a.data("id")},success:function(e){if(1==e.success){a.removeClass("loading");var o=u(e.html);u("body").append(o),o.modal("show")}},complete:function(){},error:function(){}}),!1}),u(".btn-map").click(function(){return function(){var e=u("#modalLocationPicker");e.modal("show");var o=u("#noo-map-lat").val(),a=u("#noo-map-lon").val();u("#noo-location-name").on("input",function(){u("#noo-map-address").val(u(this).val())});var t={location:{latitude:o,longitude:a},radius:0,inputBinding:{latitudeInput:u("#noo-map-lat"),longitudeInput:u("#noo-map-lon"),locationNameInput:u("#noo-map-address")},markerIcon:nooLocationPicker.marker_icon};nooLocationPicker.enable_auto_complete&&(t.enableAutocomplete=!0,t.enableAutocompleteBlur=!0),nooLocationPicker.componentRestrictions&&(t.autocompleteOptions={types:nooLocationPicker.types,componentRestrictions:{country:nooLocationPicker.componentRestrictions}}),console.log(t),u("#noo-map-picker").locationpicker(t),e.on("shown.bs.modal",function(){u("#noo-map-picker").locationpicker("autosize")})}(),!1}),u("select.form-control ").each(function(){var e=u(this),o=u(this).parents(".form-group"),a=void 0!==o.data("placeholder")?o.data("placeholder"):e.data("placeholder"),i=void 0!==a?a:Noo_BMS.nonSelectedText;e.multiselect({templates:{filter:'<li class="multiselect-filter"><div class="input-group"><input class="form-control multiselect-search" type="text"></div></li>',filterClearBtn:'<span class="multiselect-clear-filter"><i class="fa fa-remove"></i></span>'},enableFiltering:!0,enableCaseInsensitiveFiltering:!0,numberDisplayed:2,nonSelectedText:i,filterPlaceholder:"Search",buttonText:function(o,e){if(0==o.length)return i;var a=[],t=0,n=this.numberDisplayed;return o.each(function(){var e=void 0!==u(this).attr("label")?u(this).attr("label"):u(this).html();if(!(t<n))return a.push("...("+o.length+")"),!1;a.push(e),t++}),a.join(", ")}})})}(jQuery);