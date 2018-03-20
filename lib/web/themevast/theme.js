/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
/*jshint browser:true jquery:true*/
define([
    'jquery',
    'domReady!'
], function ($) {
    'use strict';

    /* Form with auto submit feature */
    $(window).scroll(function () {
		if ($(this).scrollTop() > 100) {
			$('#back-top').fadeIn();
		} else {
			$('#back-top').fadeOut();
		}
		
		if ($(this).scrollTop() > 200) {
		   $('.header').addClass("header-sticky");
		  } else {
		   $('.header').removeClass("header-sticky");
		  }
	});
	// scroll body to 0px on click
	$('#back-top').click(function () {
		$('body,html').animate({
			scrollTop: 0
		}, 800);
		return false;
	});
	$(".header-toplinks .fa-user").click(function(){
        $(".links").toggle();
    });
	$(".main-menu-bar h3").click(function(){
        $(".nav-sections").toggle();
    });
    $(".quick-access .fa-cog").click(function(){
        $(".access-content").toggle();
    });
    $(".verticalmenu h3").click(function(){
        jQuery(".menu-item").toggle();
    });
	if ($(this).scrollTop() > 200) {
		$('.header-container').addClass("header-sticky");
		} else {
		$('.header-container').removeClass("header-sticky");
	}
});
