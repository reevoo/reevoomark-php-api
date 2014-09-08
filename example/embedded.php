<? include("../../reevoomark-php-api/lib/reevoo_mark.php"); ?>

<script id="reevoomark-loader">
  (function() {
    var trkref = 'DXN';
    var myscript = document.createElement('script');
    myscript.type = 'text/javascript';
    myscript.src=('//mark.reevoo.com/reevoomark/'+trkref+'.js?async=true');
    var s = document.getElementById('reevoomark-loader');
    s.parentNode.insertBefore(myscript, s);
  })();
</script>

<link rel="stylesheet" href="http://mark.reevoo.com/stylesheets/reevoomark/embedded_reviews.css" type="text/css" />

<? $reevoo_mark = new ReevooMark("/Users/alexmalkov/reevoo/reevoomark-php-api/reevoo_cache", "http://mark.reevoo.com/reevoomark/embeddable_reviews.html", "DXN", "18877367") ?>

<?php $reevoo_mark->render(); ?>
