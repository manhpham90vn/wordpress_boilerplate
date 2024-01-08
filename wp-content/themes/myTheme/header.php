<!DOCTYPE html>
<html>

<head <?php language_attributes(); ?>>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
    <header class="site-header">
        <div class="container">
            <h1 class="school-logo-text float-left">
                <a href="<?php echo site_url() ?>"><strong>Fictional</strong> University</a>
            </h1>
            <a href="<?php echo site_url('/search') ?>" class="js-search-trigger site-header__search-trigger"><i
                    class="fa fa-search" aria-hidden="true"></i></a>
            <i class="site-header__menu-trigger fa fa-bars" aria-hidden="true"></i>
            <div class="site-header__menu group">
                <nav class="main-navigation">
                    <?php
                    if (is_user_logged_in()) {
                        wp_nav_menu(
                            array(
                                'theme_location' => 'headerMenuLoggedIn'
                            ));
                    } else {
                        wp_nav_menu(
                            array(
                                'theme_location' => 'headerMenu'
                            ));
                    }
                    ?>
                </nav>
                <div class="site-header__util">
                    <?php
                    if (!is_user_logged_in()) { ?>
                        <a href="<?php echo wp_login_url() ?>"
                            class="btn btn--small btn--orange float-left push-right">Login</a>
                        <a href="<?php echo wp_registration_url() ?>"
                            class="btn btn--small btn--dark-orange float-left">Sign Up</a>
                        <?php
                    } else {
                        ?>
                        <a href="<?php echo wp_logout_url() ?>"
                            class="btn btn--small btn--dark-orange float-left btn--with-photo">
                            <span class="site-header__avatar">
                                <?php echo get_avatar(get_current_user_id()) ?>
                            </span>Logout
                        </a>
                        <?php
                    }
                    ?>
                    <a href="<?php echo site_url('/search') ?>" class="search-trigger js-search-trigger"><i
                            class="fa fa-search" aria-hidden="true"></i></a>
                </div>
            </div>
        </div>
    </header>