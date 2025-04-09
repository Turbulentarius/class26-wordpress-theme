<?php
define('CACHE_TOGGLE', true);
define('BASE_PATH', rtrim(preg_replace('#[/\\\\]{1,}#', '/', __DIR__), '/') . '/');
$cache_dir_path = BASE_PATH . 'cache';
$req_path_hash = md5($_SERVER['REQUEST_URI']);
$cached_file_path = $cache_dir_path . '/'. $req_path_hash . '.html';

if (!is_dir($cache_dir_path)) {
    mkdir($cache_dir_path, 0777, true);
}

if (file_exists($cached_file_path) && CACHE_TOGGLE) {
    // Make sure output buffering is not active so readfile's chunks
    // will be flushed by PHP as they arrive
    while (ob_get_level()) {
        ob_end_clean();
    }
    header('content-type: text/html; charset=utf-8');
    header('cache-control: public, max-age=86400');
    header('from-cache: simple-cache');
    header('expires: ' . gmdate('D, d M Y H:i:s', time() + 86400) . ' GMT');
    header('content-length: ' . filesize($cached_file_path));
    readfile($cached_file_path);
    flush(); // If anything remains, make sure to flush it.
    exit(); // This should also flush remaining stuff, but it's good to be explicit in this case imo
}

// A handler for output buffering. Will only be called when the cached file does not exist.
ob_start(function (string $buffer) use ($cached_file_path) {
    file_put_contents($cached_file_path, $buffer);
    return $buffer;
});


?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php wp_title('|', true, 'right'); ?></title>
    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>

    <header>
        <h1><a href="<?php echo esc_url(home_url('/')); ?>"><?php bloginfo('name'); ?></a></h1>
        <p><?php bloginfo('description'); ?></p>
        <nav>
            <ol>
                <?php
                $pages = get_pages();
                if ('/' !== $_SERVER['REQUEST_URI']) {
                    echo '<li><a href="/">Webshop</a></li>';
                }
                foreach ($pages as $page) {
                    if (parse_url(get_permalink($page), PHP_URL_PATH) !== $_SERVER['REQUEST_URI']) {
                        echo '<li><a href="' . get_permalink($page) . '">' . esc_html($page->post_title) . '</a></li>';
                    }
                }
                ?>
            </ol>
        </nav>
    </header>

    <main>
        <?php
        if (is_page()) {
            if (!have_posts()) {
                http_response_code(404);
                echo '404 Not Found';
                exit();
            }
            the_post();
            the_title('<h1>', '</h1>');
            the_content();
        } else {
            $query = new WP_Query([
                'category_name' => 'webshop'
            ]);

            if ($query->have_posts()) {
                echo '<div id="shop_listing">';
                while ($query->have_posts()) {
                    $query->the_post();
                    echo '<div class="listing_item">';
                    the_title('<h2>', '</h2>');
                    the_content();
                    echo '</div>';
                }
                echo '</div>';
                wp_reset_postdata();
            } else {
                echo '<p>No content found</p>';
            }
        }
        ?>

    </main>

    <?php get_footer(); ?>
</body>

</html>
<?php

ob_end_flush();

?>