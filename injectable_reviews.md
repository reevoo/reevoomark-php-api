#Injectable Reviews

##Implementation

Include the relevant CSS. For product reviews use:

``` html
<link rel="stylesheet" href="http://mark.reevoo.com/stylesheets/reevoomark/reevoo_reviews.css" type="text/css" />
```

Include your customer specific Reevoo JavaScript:

``` html
<script src="http://mark.reevoo.com/reevoomark/<TRKREF>.js" type="text/javascript"></script>
```

Include the PHP library (make sure you use the correct path to the reevoo_mark.php file):

``` php
<? include("reevoo_mark.php"); ?>
```

Inject the first two reviews on to the page. Make sure to replace `<SKU>` and `<TRKREF>` with the appropriate values. Note that even if there are no reviews the content should still be rendered for tracking purposes.

``` php
<? $reevoo_mark = new ReevooMark("reevoo_cache", "http://mark.reevoo.com/reevoomark/first_two_reviews.html", "<TRKREF>", "<SKU>") ?>

<? if( $reevoo_mark->reviewCount() == 0 ){ ?>
  <h1>No reviews</h1>
<? } ?>
<?php $reevoo_mark->render(); ?>
```
