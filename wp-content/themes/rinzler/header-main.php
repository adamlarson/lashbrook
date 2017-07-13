<header>
    <div class="wrapper"> 
        <a href="/" class="logo">
            <img class="blue" src="<?php the_field('main_logo','options'); ?>">
            <img class="gold" src="<?php the_field('alt_logo','options'); ?>">
        </a>

        <nav>
            <div class="overlay-close"></div>
            <ul>
                <?php
                    $defaults = array(
                        'theme_location'  => 'main_nav',
                        'menu'            => 'Main Nav',
                        'items_wrap'      => '%3$s',
                        'depth'           => 3,
                        'walker'          => new Lashbrook_Nav_Menu()
                    );
                    wp_nav_menu( $defaults );
                ?>
            </ul>
            <!-- responsive search form -->
            <div class="header-search-responsive">
                <form action="/store/catalogsearch/advanced/result/" method="get">
                    <div class="field search">
                        <div class="control">
                            <input name="sku" value="" placeholder="Search entire store here..." class="input-text" maxlength="128" role="combobox" aria-haspopup="false" aria-autocomplete="both" autocomplete="off" type="text">
                        </div>
                    </div>
                </form>
            </div>
        </nav>

        <div class="social-account">
            <i class="fa fa-bars mobileToggle"></i>
            <a href="/store/customer/account/"><i class="fa fa-user"></i></a>
            <a href="/store/checkout/cart/"><i class="fa fa-shopping-bag"></i></a>
            <!-- magento search form -->
            <div class="header-search">
                <form id="search_mini_form" action="/store/catalogsearch/advanced/result/" method="get">
                <!--form id="search_mini_form" action="/store/catalogsearch/advanced/result/" method="get"-->
                    <button type="submit" title="Search"><i class="fa fa-search"></i></button>
                    <div class="field search">
                        <div class="control">
                            <input id="search" name="sku" value="" placeholder="Search entire store here..." class="input-text" maxlength="128" role="combobox" aria-haspopup="false" aria-autocomplete="both" autocomplete="off" type="text">
                            <!--input id="search" name="sku" value="" placeholder="Search entire store here..." class="input-text" maxlength="128" role="combobox" aria-haspopup="false" aria-autocomplete="both" autocomplete="off" type="text"-->
                            <div id="search_autocomplete" class="search-autocomplete"></div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div id="magic_sub_menu">&ensp;</div>
</header>
