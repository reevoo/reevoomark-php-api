<html>
<?php
	require("../lib/reevoo_mark.php");
	$reevooMark = new ReevooMark("KIA,HYU,PIU,REV", "/tmp");
?>

<head>
	<?php $reevooMark->cssAssets() ?>
</head>

<body>
	<?php $reevooMark->productBadge(array("sku" => "100A")) ?>

	<?php $reevooMark->conversationsBadge(array("sku" => "100A")) ?>

	<?php $reevooMark->productSeriesBadge(array("sku" => "ix20", "trkref" => "HYU")) ?>

	<?php $reevooMark->conversationSeriesBadge(array("sku" => "ix20", "trkref" => "HYU")) ?>

	<?php $reevooMark->overallServiceRatingBadge(array("trkref" => "PIU")) ?>

	<?php $reevooMark->customerServiceRatingBadge(array("trkref" => "PIU")) ?>

	<?php $reevooMark->deliveryRatingBadge(array("trkref" => "PIU")) ?>

    <?php $reevooMark->offersWidget(array("sku" => "3461209", "trkref" => "PCA")) ?>

    <?php if (!$reevooMark->offersWidget(array("sku" => "10023AAA"))): ?>
        <h2>Sorry, no product offers available</h2>
    <?php endif ?>

    <?php $reevooMark->productReviews(array("sku" => "100A", "numberOfReviews" => 6, "paginated" => true, "locale" => "en-GB")) ?>

	<?php if (!$reevooMark->productReviews(array("sku" => "10023A", "numberOfReviews" => 6, "paginated" => true, "locale" => "en-GB", "showEmptyMessage" => false))): ?>
		<h2>Sorry, no product reviews here</h2>
	<?php endif ?>

	<?php $reevooMark->customerExperienceReviews(array("numberOfReviews" => 6, "paginated" => true, "locale" => "en-GB")) ?>

	<?php if (!$reevooMark->customerExperienceReviews(array("trkref" => "REV", "numberOfReviews" => 6, "paginated" => true, "locale" => "en-GB", "showEmptyMessage" => false))): ?>
		<h2>Sorry, no customer experience reviews here</h2>
	<?php endif ?>

	<?php $reevooMark->conversations(array("sku" => "167823", "trkref" => "REV", "locale" => "en-GB")) ?>

	<?php if (!$reevooMark->conversations(array("sku" => "1678sdf", "trkref" => "REV", "locale" => "en-GB", "showEmptyMessage" => false))): ?>
		<h2>Sorry, no conversations here</h2>
	<?php endif ?>

	<?php $reevooMark->purchaseTrackingEvent(array("skus" => "111,222,333", "trkref" => "HYU", "value" => "250")) ?>

	<?php $reevooMark->propensityToBuyTrackingEvent(array("trkref" => "HYU", "action" => "Brochure")) ?>

	<?php $reevooMark->propensityToBuyTrackingEvent(array("trkref" => "HYU", "action" => "Locate Store", "sku" => "123")) ?>

	<?php $reevooMark->javascriptAssets() ?>
</body>

</html>
