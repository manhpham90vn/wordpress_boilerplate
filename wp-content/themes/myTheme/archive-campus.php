<?php
get_header();
pageBanner(
    array(
        "title" => "All Campuses",
        "sub_title" => ""
    ));
?>
<div class="container container--narrow page-section">
    <div class="acf-map">
        <ul class="link-list min-list">
            <?php
            while (have_posts()) {
                the_post();
                $map = get_field('map_location');
                ?>
                <div class="marker" data-lat="<?php echo $map["lat"] ?>" data-lng="<?php echo $map["lng"] ?>">
                    <h3><a href="<?php the_permalink(); ?>">
                            <?php the_title() ?>
                        </a></h3>
                    <?php echo $map["address"]; ?>
                </div>
            <?php } ?>
        </ul>
    </div>
</div>

<?php get_footer(); ?>