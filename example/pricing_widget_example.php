<link href="http://mark.local/stylesheets/best_offers/base.css" rel="stylesheet" />
<script src="//mark.reevoo.com/reevoomark/WDC.js?async=true"></script>

<? include("../../reevoomark-php-api/lib/reevoo_mark.php"); ?>

<? $reevoo_mark = new ReevooMark("/Users/alexmalkov/reevoo/reevoomark-php-api/reevoo_cache", "mark.reevoo.com/widgets/offers", "WDC", "129609") ?>

<? if ($reevoo_mark->offerCount() > 0) { ?>
<? $reevoo_mark->render(); ?>
<? } ?>
