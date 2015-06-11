<?php
require_once("reevoo_mark_api.php");

class ReevooMark extends ReevooMarkApi {

  function cssAssets() {
    echo parent::cssAssets();
  }

  function productBadge($options = array()) {
    echo parent::productBadge($options);
  }

  function conversationsBadge($options = array()) {
    echo parent::conversationsBadge($options);
  }

  function productSeriesBadge($options = array()) {
    echo parent::productSeriesBadge($options);
  }

  function conversationSeriesBadge($options = array()) {
    echo parent::conversationSeriesBadge($options);
  }

  function overallServiceRatingBadge($options = array()) {
    echo parent::overallServiceRatingBadge($options);
  }

  function customerServiceRatingBadge($options = array()) {
    echo parent::customerServiceRatingBadge($options);
  }

  function deliveryRatingBadge($options = array()) {
    echo parent::deliveryRatingBadge($options);
  }

  function productReviews($options = array()) {
    $productReviews = parent::productReviews($options);
    echo $productReviews['content'];
    return $productReviews['notEmpty'];
  }

  function offersWidget($options = array()) {
    $offersWidget = parent::offersWidget($options);
    echo $offersWidget['content'];
    return $offersWidget['notEmpty'];
  }

  function customerExperienceReviews($options = array()) {
    $customerExperienceReviews = parent::customerExperienceReviews($options);
    echo $customerExperienceReviews['content'];
    return $customerExperienceReviews['notEmpty'];
  }

  function conversations($options = array()) {
    $conversations = parent::conversations($options);
    echo $conversations['content'];
    return $conversations['notEmpty'];
  }

  function purchaseTrackingEvent($options = array()) {
    echo parent::purchaseTrackingEvent($options);
  }

  function propensityToBuyTrackingEvent($options = array()) {
    echo parent::propensityToBuyTrackingEvent($options);
  }

  function javascriptAssets() {
    echo parent::javascriptAssets();
  }
}
