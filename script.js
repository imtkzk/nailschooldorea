$(function(){

	let headerHeight = $('header').outerHeight();
	let footHeight = $('footer').innerHeight();
	let windowWidth = $(window).width();
	
	
	
	//ビューポート設定
	$(window).on("resize orientationchange", function (e) {
		var wsw = window.screen.width;
		if (wsw <= 768) {
			//デバイス横幅768以下
			$("meta[name='viewport']").attr("content", "width=device-width,initial-scale=1");
		} else {
			//それ以外
			$("meta[name='viewport']").attr("content", "width=1280");
		}
	}).trigger("resize");


	
	//スマホ下部ボタン
	$(document).ready(function(){
	    if(window.matchMedia('(max-width:768px)').matches) {

			$("#fixed-btn").hide();
			$(window).on("scroll", function() {
				var scrollHeight = $(document).innerHeight(); 
				var scrollPosition = $(window).innerHeight() + $(window).scrollTop(); 
				if ($(this).scrollTop() > 100 && scrollHeight - scrollPosition > footHeight  ) {
					$("#fixed-btn").fadeIn("fast");
				} else {
					$("#fixed-btn").fadeOut("fast");
				}
			});
		}
	});



	//画像差し替え
    var $elem = $('img');
    var sp = '_sp.';
    var pc = '_pc.';
        
    $elem.each(function() {
      var $this = $(this);
    if(window.matchMedia('(max-width:768px)').matches) {
        $this.attr('src', $this.attr('src').replace(pc, sp));
      } else {
       $this.attr('src', $this.attr('src').replace(sp, pc));
        
    }
    function switchImgFunction(){
        $this.attr('src', $this.attr('src').replace(pc, sp));
        if(window.matchMedia("(min-width:769px)").matches){
        $this.attr('src', $this.attr('src').replace(sp, pc));
        }
    }
    window.matchMedia("(max-width:768px)").addListener(switchImgFunction);

    });




	//スマホメニュー
	$(".menu-trigger").click(function() {
		$(this).toggleClass("action");
		$(".head_navi").toggleClass("action");
		$(".menu-trigger span").toggleClass("action");
		$(".menu-wraper").toggleClass("action");
	});

	$(".menu-wraper, .menu").click(function() {
		$(this).removeClass("action");
		$(".head_navi").removeClass("action");
		$(".menu-trigger span").removeClass("action");
		$(".menu-trigger").removeClass("action");
	});




	// ページ内スムーズスクロール部分の記述
	$(function(){
	
	  var urlHash = location.hash;
	  if(urlHash) {
	      $('body,html').stop().scrollTop(0);
	      setTimeout(function(){
	          var target = $(urlHash);
	          var speed = 500;
	          var position = target.offset().top  ;
	          //ページロードの処理を待ってから100ミリ秒後にスクロールさせる
	          //$('body,html').stop().animate({scrollTop:position}, speed, "swing");
	          $('body,html').scrollTop(position);
	          
	      }, 100);
	  }
	  
	  $('a[href^="#"]').click(function() {
	      var href= $(this).attr("href");
	      var target = $(href == "#" || href == "" ? 'html' : href);
	      var speed = 500;
	      var position = target.offset().top ;
	      $('body,html').stop().animate({scrollTop:position}, speed, "swing");   
	  });
	});

	//チェックボックスをラジオボタンのように扱う
	$(function($){
	    $('input:checkbox').click(function() {
	        $(this).closest('.element_wrap').find('input:checkbox').not(this).prop('checked', false);
	    });
	});



});