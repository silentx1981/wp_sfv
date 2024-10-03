<?php

namespace wpSfv\Wp;

class AddActions
{
    public static function run()
    {
        add_action('wp_enqueue_scripts',[self::class, 'style']);
        add_action('wp_enqueue_scripts',[self::class, 'javascript']);
    }

    public static function javascript()
    {
        $dir = plugins_url().'/wp_sfv/';
        wp_enqueue_script('jquery', $dir.'node_modules/jquery/dist/jquery.min.js', [], false, true);
        wp_enqueue_script('bootstrap', $dir.'node_modules/bootstrap/dist/js/bootstrap.bundle.min.js', [], false, true);
        //wp_enqueue_script('owlcarousel', $dir.'node_modules/owl.carousel2/dist/owl.carousel.min.js', [], false, true);
    }

    public static function style()
    {
        $dir = plugins_url().'/wp_sfv/';
        wp_enqueue_style('stylebootstrap', $dir.'node_modules/bootstrap/dist/css/bootstrap.min.css');
        //wp_enqueue_style('styleowlcarousel', $dir.'node_modules/owl.carousel2/dist/assets/owl.carousel.min.css');
        //wp_enqueue_style('styleowlcarouseltheme', $dir.'node_modules/owl.carousel2/dist/assets/owl.theme.default.css');
        wp_enqueue_style('style', $dir . 'css/wpSfv.css');
    }
}