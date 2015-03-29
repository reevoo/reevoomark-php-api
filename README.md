# reevoomark-php-api

[![Build Status](https://travis-ci.org/reevoo/reevoomark-php-api.png?branch=master)](https://travis-ci.org/reevoo/reevoomark-php-api)

##Description

The reevoomark-php-api is a PHP tag library for Reevoo Ratings & Reviews customers who want to quickly and easily integrate Reevoo content in to their sites server-side.

##Other Languages
Tag libraries are also available for [.NET](https://github.com/reevoo/reevoomark-dotnet-api) and [Java](https://github.com/reevoo/reevoomark-java-api).

##Features

* Server-side inclusion of Reevoo content.
* Included CSS for display of Reevoo content.
* Server-side caching of content that respects the cache control rules set by Reevoo.

##Support
For Reevoo Ratings & Reviews customers, support can be obtained by emailing <operations@reevoo.com>.

There is also a [bug tracker](https://github.com/reevoo/reevoomark-php-api/issues) available.

##Installation

Get Composer from [here](https://getcomposer.org/download/) and install it.

Add the reevoo tag lib to your composer.json like so:

```
{
  "require": {
    "reevoo/reevoomark-php-api": "2.*"
  }
}
```

and then do:

```
$ php composer.phar install
```

##Implementation

Require the PHP library (make sure you use the correct path to the reevoo_mark.php file) and create new instance
of ReevooMark class with your TRKREF and path to your cache directory (or false if you do not want to use cache)
as a contructor attributes:

``` php
<?php
  require("reevoo_mark.php");
  $reevooMark = new ReevooMark("REV", "/tmp");
?>
```

ReevooMark supports multiple retailers content on the same page, it such case you can specify multiple TRKREFs
in the constructor:

``` php
<?php
  require("reevoo_mark.php");
  $reevooMark = new ReevooMark("REV1,REV2,REV3", "/tmp");
?>
```

To include the relevant CSS call the method ```cssAssets()``` inside ```<head>``` section of your page:

``` php
<head>
  ...
  <?php $reevooMark->cssAssets() ?>
</head>
```

To include the relevant JavaScript call the method ```javascriptAssets()``` before the closing ```</body>``` tag your page:

``` php
<body>
  ...
  <?php $reevooMark->javascriptAssets() ?>
</body>
```


### Standard Badges

#### Product Badge

To render "product badges" you can use any of the examples below.
The ```sku``` is compulsory but ```trkref``` and ```variantName``` are optional.
Specify ```trkref``` only if you are using multiple TRKREFs in ```ReevooMark``` constructor.


```php
<?php $reevooMark->productBadge(array("sku" => "ABC")) ?>
<?php $reevooMark->productBadge(array("sku" => "ABC", "variantName" => "undecorated")) ?>
<?php $reevooMark->productBadge(array("sku" => "ABC", "trkref" => "REV", "variantName" => "stars_only")) ?>
```

#### Conversations Badge

To render "conversations badges" you can use any of the examples below.
The ```sku``` is compulsory but ```trkref``` and ```variantName``` are optional.
Specify ```trkref``` only if you are using multiple TRKREFs in ```ReevooMark``` constructor.

```php
<?php $reevooMark->conversationsBadge(array("sku" => "ABC")) ?>
<?php $reevooMark->conversationsBadge(array("sku" => "ABC", "variantName" => "undecorated")) ?>
<?php $reevooMark->conversationsBadge(array("sku" => "ABC", "trkref" => "REV", "variantName" => "stars_only")) ?>
```

### Series Badges

#### Product Badges

To render "product series badges" you can use any of the examples below.
The ```sku``` is compulsory and should be set to the series id. The ```trkref``` and ```variantName``` are optional.
Specify ```trkref``` only if you are using multiple TRKREFs in ```ReevooMark``` constructor.

```php
<?php $reevooMark->productSeriesBadge(array("sku" => "ABC")) ?>
<?php $reevooMark->productSeriesBadge(array("sku" => "ABC", "variantName" => "undecorated")) ?>
<?php $reevooMark->productSeriesBadge(array("sku" => "ABC", "trkref" => "REV", "variantName" => "stars_only")) ?>
```

#### Conversations Badges

To render "conversation series badges" you can use any of the examples below.
The ```sku``` is compulsory and should be set to the series id. The ```trkref``` and ```variantName``` are optional.
Specify ```trkref``` only if you are using multiple TRKREFs in ```ReevooMark``` constructor.

```php
<?php $reevooMark->conversationSeriesBadge(array("sku" => "ABC")) ?>
<?php $reevooMark->conversationSeriesBadge(array("sku" => "ABC", "variantName" => "undecorated")) ?>
<?php $reevooMark->conversationSeriesBadge(array("sku" => "ABC", "trkref" => "REV", "variantName" => "stars_only")) ?>
```

### Overall Service Rating Badges

To render "Overall Service Rating badges" you can use any of the examples below.
The ```trkref``` and ```variantName``` are optional.
Specify ```trkref``` only if you are using multiple TRKREFs in ```ReevooMark``` constructor.


```php
<?php $reevooMark->overallServiceRatingBadge() ?>
<?php $reevooMark->overallServiceRatingBadge(array("variantName" => "undecorated")) ?>
<?php $reevooMark->overallServiceRatingBadge(array("trkref" => "PIU", "variantName" => "stars_only")) ?>
```

### Customer Service Rating Badges

To render "Customer Service Rating badges" you can use any of the examples below.
The ```trkref``` and ```variantName``` are optional.
Specify ```trkref``` only if you are using multiple TRKREFs in ```ReevooMark``` constructor.

```php
<?php $reevooMark->customerServiceRatingBadge() ?>
<?php $reevooMark->customerServiceRatingBadge(array("variantName" => "undecorated")) ?>
<?php $reevooMark->customerServiceRatingBadge(array("trkref" => "PIU", "variantName" => "stars_only")) ?>
```

### Delivery Rating Badges

To render "Delivery Rating badges" you can use any of the examples below.
The ```trkref``` and ```variantName``` are optional.
Specify ```trkref``` only if you are using multiple TRKREFs in ```ReevooMark``` constructor.

```php
<?php $reevooMark->deliveryRatingBadge() ?>
<?php $reevooMark->deliveryRatingBadge(array("variantName" => "undecorated")) ?>
<?php $reevooMark->deliveryRatingBadge(array("trkref" => "PIU", "variantName" => "stars_only")) ?>
```

### Embedded Product Review Content

To render "embedded review content" you can use any of the examples below.
The ```sku``` attribute is compulsory but ```trkref```, ```locale```, ```numberOfReviews```  and ```paginated``` are optional.
Any combination of the optional attributes is possible.
Specify ```trkref``` only if you are using multiple TRKREFs in ```ReevooMark``` constructor.

```php
<?php $reevooMark->productReviews(array("sku" => "100A")) ?>
<?php $reevooMark->productReviews(array("sku" => "100A", "locale" => "en-GB", "numberOfReviews" => 5)) ?>
<?php $reevooMark->productReviews(array("sku" => "100A", "paginated" => true, "numberOfReviews" => 10)) ?>
<?php $reevooMark->productReviews(array("sku" => "100A", "trkref" => "REV", "paginated" => true, "locale" => "cs-CZ")) ?>
```

If you set the ```paginated``` attribute to true, the embedded reviews will show pagination links.

If no reviews are available we will display default message in language specified by your ```locale```.
If you would like to specify your own message you can pass attribute ```showEmptyMessage``` with value false
and use return value of the ```productReviews()``` method that is false in this case.

```php
<?php if (!$reevooMark->productReviews(array("sku" => "100A", "showEmptyMessage" => false))): ?>
  <h2>Sorry, no product reviews here</h2>
<?php endif ?>
```

### Price Offers Widget

To render "price offers" you can use the example below.
Please provide the ```sku``` attribute. The ```trkref``` attribute is only compulsory if you are using multiple TRKREFs in ```ReevooMark``` constructor.

```php
<?php $reevooMark->offersWidget(array("sku" => "3461209", "trkref" => "PCA")) ?>
```
If there are no offers to display you can display your own custom message as in the example below:
```php
<?php if (!$reevooMark->offersWidget(array("sku" => "10023AAA", "trkref" => "PCA"))): ?>
  <h2>Sorry, no price  offers available for this product</h2>
<?php endif ?>
```
### Embedded Customer Experience Review Content

To render "embedded customer experience review content" you can use any of the examples below.
The ```trkref```, ```locale```, ```numberOfReviews``` and ```paginated``` a attributes are optional.
Any combination of the optional attributes is possible.
Specify ```trkref``` only if you are using multiple TRKREFs in ```ReevooMark``` constructor.

```php
<?php $reevooMark->customerExperienceReviews() ?>
<?php $reevooMark->customerExperienceReviews(array("numberOfReviews" => 5)) ?>
<?php $reevooMark->customerExperienceReviews(array("paginated" => true, "numberOfReviews" => 10)) ?>
<?php $reevooMark->customerExperienceReviews(array("trkref" => "REV", "paginated" => true, "locale" => "cs-CZ")) ?>
```

If you set the ```paginated``` attribute to true, the embedded reviews will show pagination links.

If no reviews are available we will display default message in language specified by your ```locale```.
If you would like to specify your own message you can pass attribute ```showEmptyMessage``` with value false
and use return value of the ```customerExperienceReviews()``` method that is false in this case.

```php
<?php if (!$reevooMark->customerExperienceReviews(array("showEmptyMessage" => false))): ?>
  <h2>Sorry, no customer experience reviews here</h2>
<?php endif ?>
```

### Embedded Conversation Content

To render "embedded conversations content" you can use any of the examples below.
The ```sku``` attribute is compulsory but ```trkref``` and ```locale``` are optional.
Any combination of the optional attributes is possible.
Specify ```trkref``` only if you are using multiple TRKREFs in ```ReevooMark``` constructor.

```php
<?php $reevooMark->conversations(array("sku" => "100A")) ?>
<?php $reevooMark->conversations(array("sku" => "100A", "locale" => "en-GB")) ?>
<?php $reevooMark->conversations(array("sku" => "100A", "trkref" => "REV")) ?>
```

If no conversations are available we will display default message in language specified by your ```locale```.
If you would like to specify your own message you can pass attribute ```showEmptyMessage``` with value false
and use return value of the ```conversations()``` method that is false in this case.

```php
<?php if (!$reevooMark->conversations(array("sku" => "100A", "showEmptyMessage" => false))): ?>
  <h2>Sorry, no conversations here</h2>
<?php endif ?>
```


## Tracking

If you display the reviews in a tabbed display, or otherwise require visitors to your site to click an element before
seeing the embedded reviews, add the following onclick attribute to track the clickthroughs:

If your trkref value is for example "REV" you would add:

```
onclick="ReevooMark_REV.track_click_through(‘<SKU>’)”
```

If your trkref value is for example "PIU" you would add:

```
onclick="ReevooMark_PIU.track_click_through(‘<SKU>’)”
```

See how in examples above you need to put your trkref value as a suffix to the ```ReevooMark_``` part. Also remember to replace ```<SKU>``` by the sku of the actual product.


### Purchase Tracking

If your site includes online shopping functionality you can use ```purchaseTrackingEvent()``` method on your "Order Confirmation Page".

``` php
<?php $reevooMark->purchaseTrackingEvent(array("skus" => "111,222,333", "value" => "250")) ?>
<?php $reevooMark->purchaseTrackingEvent(array("trkref" => "HYU", "skus" => "111,222,333", "value" => "250")) ?>
```

* ```skus``` attribute value is with a comma separated list of all the skus that have been purchased as part of the order.
* ```value``` attribute value is total price of the order, you don't need to include currency symbol.
* ```trkref``` attribute value you need to specify only if you are using multiple TRKREFs in ```ReevooMark``` constructor.

All this tracking information will be available to you on your Reevoo Analytics account.


### Propensity to Buy Tracking

This type of tracking is used as a substitute of purchase tracking for retailers that do not offer online purchase in their stores and therefore do not have an order confirmation page.

These retailers can use ```propensityToBuyTrackingEvent()``` method which can be added to any page they wish on the site.

``` php
<?php $reevooMark->propensityToBuyTrackingEvent(array("action" => "Brochure")) ?>
<?php $reevooMark->propensityToBuyTrackingEvent(array("trkref" => "REV", "action" => "Locate Store", "sku" => "123")) ?>
```

* ```action``` attribute value is the type of event that you want to track, can be anything you want like "user visited the buy now page" or "user requested brochure" or "user requested a test drive", etc...
* ```sku``` attribute is optional, you only have to include it if you want to link the tracking event to a specific product SKU
* ```trkref``` attribute value you need to specify only if you are using multiple TRKREFs in ```ReevooMark``` constructor.

All this tracking information will be available to you on your Google Analytics account.


## More examples

Click [here](https://github.com/reevoo/reevoomark-php-api/tree/master/example/index.php) for a full page example of implementation in PHP.


##License

This software is released under the MIT license.  Only certified Reevoo partners
are licensed to display Reevoo content on their sites.  Contact <sales@reevoo.com> for
more information.

(The MIT License)

Copyright (c) 2008 - 2014:

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
