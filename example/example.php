<link href="http://mark.local/stylesheets/best_offers/base.css" rel="stylesheet" />
<? include("reevoomark-php-api/reevoo_mark.php"); ?>

<? $reevoo_mark = new ReevooMark("reevoo_cache", "http://mark.local/widgets/offers", "PCA", "3221438") ?>
<ul>
<? foreach ( $reevoo_mark->datas()->headers() as $header => $value) { ?>
  <li><? echo "${header} = ${value}"?></li>
<? } ?>
</ul>

<pre>
&lt;? $reevoo_mark = new ReevooMark("reevoo_cache", "http://mark.local/widgets/offers", "PCA", "3221438") ?&gt;
&lt;? if ($reevoo_mark->offerCount() > 0) { ?&gt;
&lt;? $reevoo_mark->render(); ?&gt;
&lt;? } ?&gt;
</pre>


<? if ($reevoo_mark->offerCount() > 0) { ?>
<? $reevoo_mark->render(); ?>
<? } ?>
