# reevoomark-php-api

##Description

The reevoomark-php-api is a PHP tag library for ReevooMark and Reevoo Essentials customers who want to quickly and easily integrate Reevoo content in to their sites server-side.

##Other Languages
Tag libraries are also available for [.NET](https://github.com/reevoo/reevoomark-dotnet-api) and [Java](https://github.com/reevoo/reevoomark-java-api).

##Features

* Server-side inclusion of Reevoo content.
* Included CSS for display of Reevoo content.
* Server-side caching of content that respects the cache control rules set by Reevoo.

##Support
For ReevooMark and Reevoo Essentials customers, support can be obtained by emailing <operations@reevoo.com>.

There is also a [bug tracker](https://github.com/reevoo/reevoomark-php-api/issues) available.

##Installation

Add Pearhub as a PEAR channel if you don't already have it:

```
pear channel-discover pearhub.org
```

Install the ReevooMark API:

```
pear install pearhub/reevoomark_php_api
```

##Implementation

Include the relevant CSS. For product reviews use:

``` html
<link rel="stylesheet" href="http://mark.reevoo.com/stylesheets/reevoomark/embedded_reviews.css" type="text/css" />
```

Include your customer specific Reevoo JavaScript:

``` html
<script src="http://mark.reevoo.com/reevoomark/<TRKREF>.js" type="text/javascript"></script>
```

Note: The URL should not include the angled brackets, e.g. `/reevoomark/EXAMPLE.js`

Include the PHP library (make sure you use the correct path to the reevoo_mark.php file):

``` php
<? include("reevoo_mark.php"); ?>
```

Render embedded review content. Make sure you replace `<reevoo_cache>` with the path of a directory that can be used to cache review content:

``` php
<? $reevoo_mark = new ReevooMark("<reevoo_cache>", "http://mark.reevoo.com/reevoomark/embeddable_reviews.html", "<TRKREF>", "<SKU>") ?>
<?php $reevoo_mark->render(); ?>
```

It is also possible to specify locale and the number of reviews you'd like in the URI:

```html
http://mark.reevoo.com/reevoomark/fr-FR/10/embeddable_reviews.html
```

By default Reevoo will display helpful pages to the user when there are no reviews available. If you'd like to handle this yourself, you can check the review count:

``` php
<? if( $reevoo_mark->reviewCount() > 0 ){ ?>
  <?php $reevoo_mark->render(); ?>
<? }else{ ?>
  <h1>No reviews</h1>
<? } ?>
```

Note: If you are using [Smarty code](http://www.smarty.net/docs/en/what.is.smarty.tpl), click [here](https://github.com/reevoo/reevoomark-php-api/tree/smarty_php#implementation).

Click [here](https://github.com/reevoo/php-traffic-example) for a concrete example of a traffic implementation in PHP.

## Tracking

If you display the reviews in a tabbed display, or otherwise require visitors to your site to click an element before seeing the embedded reviews, add the following onclick attribute to track the clickthroughs:

``` html
  onclick="ReevooMark.track_click_through(‘<SKU>’)”
```

## Overall rating

The overall rating section at the top of inline reviews contains an overall score, a summary and the score breakdowns.

##License

This software is released under the MIT license.  Only certified ReevooMark partners
are licensed to display Reevoo content on their sites.  Contact <sales@reevoo.com> for
more information.

(The MIT License)

Copyright (c) 2008 - 2010:

* [Reevoo](http://www.reevoo.com)

Permission is hereby granted, free of charge, to any person obtaining
a copy of this software and associated documentation files (the
'Software'), to deal in the Software without restriction, including
without limitation the rights to use, copy, modify, merge, publish,
distribute, sublicense, and/or sell copies of the Software, and to
permit persons to whom the Software is furnished to do so, subject to
the following conditions:

The above copyright notice and this permission notice shall be
included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED 'AS IS', WITHOUT WARRANTY OF ANY KIND,
EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT.
IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY
CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT,
TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE
SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
