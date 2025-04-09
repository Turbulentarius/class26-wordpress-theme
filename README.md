# Development process
I created a Wordpress theme to refresh my WP development skills, as a part of my Bachelor's at Zealand, and this roughly documents the process.



## index.php
This is the main request handler in my theme; for this specific assignment, I needed to create a shop-listing and a checkout page.

I decided to have the shop-listing on the frontpage, created as posts in the "webshop" category, and then I created "Checkout" in pages.

To determine if a "page" was requested, I use `is_page()`, and for extra safety I call `have_posts()` to make sure something actually exists:
```
if (is_page()) {
    if (!have_posts()) {
      http_response_code(404);
      echo '404 Not Found';
      exit();
    }
    the_post();
    the_title('<h1>', '</h1>');
    the_content();
}
```

### caching

I implemented a simple caching mechanism using my own output buffering handler, which should save the server CPU cycles while optimizing page load in clients.

This is just meant as a *proof of concept*, but the principle is sound in theory.  
It is worth noting that the same approach can be used to cache only specific components or "sections" of pages.

Functions used for this include: `ob_start`, `ob_end_flush`, and `ob_end_clean` to clean the current buffer.

The downside is that calling `ob_start` will break WordPress automatic flushing, and instead await an explicit call to `ob_end_flush` on the first page view, but this is a perfectly acceptable trade-off, because subsequent page requests made by other users will be delivered via PHP's `readfile` function.

### Custom theme vs built-in

Custom themes clearly provide much better control than using built-in or downloaded themes, and for developers, they are often faster than clicking around the WordPress administration and using the editor. Custom themes may even be more secure than relying on themes downloaded from the internet.

Furthermore, the standard WordPress editor and themes suffer from bugs that make even simple designs impractical to implement.