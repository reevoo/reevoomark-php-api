#Injectable Reviews & Pricing Widget

##Implementation

The first two instructions, the JavaScript and PHP library inclusion, are common to both injectable reviews and pricing widget. If you have a page that includes both injectable reviews and the pricing widget, you only need to perform these steps once.

Include your customer specific Reevoo JavaScript:

``` html
<script src="http://mark.reevoo.com/reevoomark/<TRKREF>.js" type="text/javascript"></script>
```

Include the PHP library (make sure you use the correct path to the reevoo_mark.php file):

``` php
<? include("reevoo_mark.php"); ?>
```

###Injectable Reviews

Include the product review CSS:

``` html
<link rel="stylesheet" href="http://mark.reevoo.com/stylesheets/reevoomark/reevoo_reviews.css" type="text/css" />
```

Inject the first two reviews on to the page. Make sure to replace `<SKU>` and `<TRKREF>` with the appropriate values, and to replace `<reevoo_cache>` with the path of a directory that can be used to cache review content. Note that even if there are no reviews the content should still be rendered for tracking purposes.

``` php
<? $reevoo_mark = new ReevooMark("<reevoo_cache>", "http://mark.reevoo.com/reevoomark/first_two_reviews.html", "<TRKREF>", "<SKU>") ?>

<? if( $reevoo_mark->reviewCount() == 0 ){ ?>
  <h1>No reviews</h1>
<? } ?>
<?php $reevoo_mark->render(); ?>
```

###Pricing Widget

Include the pricing widget CSS:

``` html
<link href="http://mark.reevoo.com/stylesheets/best_offers/base.css" rel="stylesheet" />
```

Render price offers. Make sure to replace `<SKU>` and `<TRKREF>` with the appropriate values, and to replace `<reevoo_cache>` with the path of a directory that can be used to cache review content:

``` php
<? $reevoo_mark = new ReevooMark("<reevoo_cache>", "http://mark.reevoo.com/widgets/offers", "<TRKREF>", "<SKU>") ?>

<? if ($reevoo_mark->offerCount() > 0) { ?>
  <? $reevoo_mark->render(); ?>
<? } ?>
```

Or to use price comparison in USD change the url to `http://mark.reevoo.com/widgets/offers?currency=USD`
