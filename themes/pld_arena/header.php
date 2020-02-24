<?php
/**
 * The header for our theme
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package plda
 */

?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="http://gmpg.org/xfn/11">
	<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
	<link rel="stylesheet" href="https://use.typekit.net/qjj5oti.css">
	<?php wp_head(); ?>
    <!-- Google Tag Manager -->
    <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
    new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
    j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
    'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
    })(window,document,'script','dataLayer','GTM-54TWP2S');</script>
    <!-- End Google Tag Manager -->
</head>

<body <?php body_class(); ?>>

    <!-- Google Tag Manager (noscript) -->
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-54TWP2S"
    height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
    <!-- End Google Tag Manager (noscript) -->

<?php wp_body_open(); ?>
	<div id="page" class="site">
		<!-- This is the off-canvas -->
		<div id="sidebar-nav" uk-offcanvas="overlay: true">
		     <div class="uk-offcanvas-bar uk-width-expand uk-width-1-2@s uk-width-1-3@m uk-width-1-4@l">

		        	<?php
		        	if ( has_nav_menu( 'primary' ) ) :
		        		wp_nav_menu(
		        			array(
		        				'theme_location'  => 'primary',
		        				'container'       => false,
		        				'menu_id'         => 'mobile-menu',
		        				'menu_class'      => 'uk-nav-default uk-nav-parent-icon',
		        				'items_wrap'      => '<ul id="%1$s" class="%2$s" uk-nav="multiple: false">%3$s</ul>',
								'walker'          => new Plda\Core\WalkerNavMobilePlda(),
		        			)
		        		);
		        	endif;
		        	?>
		    </div>
		</div>

		<header id="masthead" class="site-header" role="banner" uk-sticky>

			<?php
			if ( is_customize_preview() ) {
				echo '<div id="awps-header-control"></div>';
			}
			?>

			<div class="container container-fluid">

				<div class="row">
					<div class="header__burger">
						<button uk-toggle="target: #sidebar-nav" class="navbar-burger" type="button" data-toggle="collapse" data-target="#main-navigation" aria-expanded="false" aria-label="Toggle navigation">
						  <?php inlineSvg('icon-burger'); ?>
						</button>
					</div>
					<div class="header__logo">

						<div class="site-branding">
							<?php
							if ( is_front_page() ) :
								$html = sprintf(
									'<h1 class="site-title" title="%1$s">%2$s</h1>',
									esc_attr( get_bloginfo( 'name', 'display' ) ),
									inlineSvg('logo-parisladefense-arena-horizontal', true)
								);

							else :

								$html = sprintf(
									'<a href="%1$s" class="navbar__logo-link" rel="home" title="%2$s">%3$s</a>',
									esc_url( home_url( '/' ) ),
									esc_attr( get_bloginfo( 'name', 'display' ) ),
									inlineSvg('logo-parisladefense-arena-horizontal', true)
								);
							endif;

							echo $html;
							 ?>
						</div><!-- .site-branding -->

					</div><!-- .header__logo-->

					<div class="header__mainnav">

						<nav class="uk-navbar-container" uk-navbar="dropbar: true;duration:200">
							<?php
							if ( has_nav_menu( 'primary' ) ) :
								wp_nav_menu(
									array(
										'theme_location'  => 'primary',
										'container'       => 'div',
								        'container_class' => 'uk-navbar-center',
										'menu_id'         => 'primary-menu',
										'menu_class'      => 'uk-navbar-nav',
										'walker'          => new Plda\Core\WalkerNavPlda(),
									)
								);
							endif;
							?>
						</nav>

					</div><!-- .header__mainnav-->
					<div class="header__user">
						<span class="user__profile"><i class="plda fa-user-circle"></i><i class="plda fa-user-connected"></i></span>
					</div>
					<div class="header__topnav">
						<div class="bg-grad bg-nav-blue"></div>
						<div class="bg-grad bg-nav-blue"></div>
						<div class="bg-grad bg-grad-navbar"></div>
						<div class="header__rs">
							<nav id="rs-navigation" class="rs-navigation">
								<?php
								if ( has_nav_menu( 'rs-menu' ) ) :
									wp_nav_menu(
										array(
											'theme_location' => 'rs-menu',
											'menu_id'        => 'rs-menu',
										)
									);
								endif;
								?>
							</nav>
						</div>
						<div class="header__extra">
							<span class="action__search"><button class="btn-link" uk-toggle="target: #search-window" type="button"><i class="plda fa-search"></i></button></span>
							<a href="https://billetterie.parisladefense-arena.com/fr/cart" class="user__cart">
                                <i class="fas fa-shopping-cart"></i>
                                <span class="user__cart-count"></span>
                            </a>
							<a href="https://billetterie.parisladefense-arena.com/fr/user/" class="user__profile"><i class="plda fa-user-circle"></i><i class="plda fa-user-connected"></i></a>
						</div>
					</div><!-- .header__topnav -->

				</div><!-- .row -->
			</div><!-- .container-fluid -->

		</header><!-- #masthead -->

		<?php do_action( 'content_after_header' ); ?>

		<div id="content" class="site-content">
