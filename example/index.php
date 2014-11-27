<html>
<?php
	require("../lib/reevoo_mark.php");
	$reevoo_mark = new ReevooMark("KIA,HYU,PIU,REV", "/tmp");
?>

<head>
	<?php $reevoo_mark->cssAssets() ?>
</head>

<body>
	<?php $reevoo_mark->productBadge(array("sku" => "100A")) ?>

	<?php $reevoo_mark->conversationsBadge(array("sku" => "100A")) ?>

	<?php $reevoo_mark->productSeriesBadge(array("sku" => "ix20", "trkref" => "HYU")) ?>

	<?php $reevoo_mark->conversationSeriesBadge(array("sku" => "ix20", "trkref" => "HYU")) ?>

	<?php $reevoo_mark->overallServiceRatingBadge(array("trkref" => "PIU")) ?>

	<?php $reevoo_mark->customerServiceRatingBadge(array("trkref" => "PIU")) ?>

	<?php $reevoo_mark->deliveryRatingBadge(array("trkref" => "PIU")) ?>

	<?php $reevoo_mark->productReviews(array("sku" => "100A", "numberOfReviews" => 6, "paginated" => true, "locale" => "en-GB")) ?>

	<?php if (!$reevoo_mark->productReviews(array("sku" => "10023A", "numberOfReviews" => 6, "paginated" => true, "locale" => "en-GB", "showEmptyMessage" => false))): ?>
		<h2>Sorry, no product reviews here</h2>
	<?php endif ?>

	<?php $reevoo_mark->customerExperienceReviews(array("numberOfReviews" => 6, "paginated" => true, "locale" => "en-GB")) ?>

	<?php if (!$reevoo_mark->customerExperienceReviews(array("trkref" => "REV", "numberOfReviews" => 6, "paginated" => true, "locale" => "en-GB", "showEmptyMessage" => false))): ?>
		<h2>Sorry, no customer experience reviews here</h2>
	<?php endif ?>

	<?php $reevoo_mark->conversations(array("sku" => "167823", "trkref" => "REV", "locale" => "en-GB")) ?>

	<?php if (!$reevoo_mark->conversations(array("sku" => "1678sdf", "trkref" => "REV", "locale" => "en-GB", "showEmptyMessage" => false))): ?>
		<h2>Sorry, no conversations here</h2>
	<?php endif ?>

	<?php $reevoo_mark->purchaseTrackingEvent(array("skus" => "111,222,333", "trkref" => "HYU", "value" => "250")) ?>

	<?php $reevoo_mark->propensityToBuyTrackingEvent(array("trkref" => "HYU", "action" => "Brochure")) ?>

	<?php $reevoo_mark->propensityToBuyTrackingEvent(array("trkref" => "HYU", "action" => "Locate Store", "sku" => "123")) ?>

	<?php $reevoo_mark->javascriptAssets() ?>
</body>

</html>
