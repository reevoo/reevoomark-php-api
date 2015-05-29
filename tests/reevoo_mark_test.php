<?php

require_once('simpletest/autorun.php');
require_once(dirname(__FILE__).'/../lib/reevoo_mark.php');
require_once(dirname(__FILE__).'/../lib/reevoo_mark_utils.php');


Mock::generatePartial('ReevooMarkHttpClient', 'MockedReevooMarkHttpClient', array('getData'));

function encoded_current_url() {
  return urlencode('http://' . ReevooMarkUtils::presenceKey($_SERVER, "HTTP_HOST", '') . ReevooMarkUtils::presenceKey($_SERVER, "SCRIPT_NAME", ''));
}

class ReevooMarkTest extends UnitTestCase {

  function test_css_assets_prints_the_correct_css_link() {
    ob_start();
    $rvm = new ReevooMark('REV', false, 'http://my_url');
    $rvm->cssAssets();
    $out1 = ob_get_contents();
    ob_end_clean();
    $this->assertEqual($out1,'<link rel="stylesheet" href="//mark.reevoo.com/stylesheets/reevoomark/embedded_reviews.css" type="text/css" />');
  }

  function test_product_badge() {
    ob_start();
    $rvm = new ReevooMark('REV', false, 'http://my_url');
    $rvm->productBadge(array("trkref" => "REV", "variant" => "stars", "sku" => "123"));
    $out1 = ob_get_contents();
    ob_end_clean();
    $this->assertEqual($out1,'<a class="reevoomark stars_variant" href="http://my_url/partner/REV/123"></a>');
  }

  function test_undecorated_product_badge() {
    ob_start();
    $rvm = new ReevooMark('REV', false, 'http://my_url');
    $rvm->productBadge(array("trkref" => "REV", "variant" => "undecorated", "sku" => "123"));
    $out1 = ob_get_contents();
    ob_end_clean();
    $this->assertEqual($out1,'<a class="reevoomark undecorated" href="http://my_url/partner/REV/123"></a>');
  }

  function test_conversations_badge() {
    ob_start();
    $rvm = new ReevooMark('REV', false, 'http://my_url');
    $rvm->conversationsBadge(array("trkref" => "REV", "variant" => "stars", "sku" => "123"));
    $out1 = ob_get_contents();
    ob_end_clean();
    $this->assertEqual($out1,'<a class="reevoomark reevoo-conversations stars_variant" href="http://my_url/partner/REV/123"></a>');
  }

  function test_product_series_badge() {
    ob_start();
    $rvm = new ReevooMark('REV', false, 'http://my_url');
    $rvm->productSeriesBadge(array("trkref" => "REV", "variant" => "stars", "sku" => "123"));
    $out1 = ob_get_contents();
    ob_end_clean();
    $this->assertEqual($out1,'<a class="reevoomark stars_variant" href="http://my_url/partner/REV/series:123"></a>');
  }

  function test_conversation_series_badge() {
    ob_start();
    $rvm = new ReevooMark('REV', false, 'http://my_url');
    $rvm->conversationSeriesBadge(array("trkref" => "REV", "variant" => "stars", "sku" => "123"));
    $out1 = ob_get_contents();
    ob_end_clean();
    $this->assertEqual($out1,'<a class="reevoomark reevoo-conversations stars_variant" href="http://my_url/partner/REV/series:123"></a>');
  }

  function test_overall_service_rating_badge() {
    ob_start();
    $rvm = new ReevooMark('REV', false, 'http://my_url');
    $rvm->overallServiceRatingBadge(array("trkref" => "REV", "variant" => "stars"));
    $out1 = ob_get_contents();
    ob_end_clean();
    $this->assertEqual($out1,'<a class="reevoo_reputation stars_variant" href="http://my_url/retailer/REV"></a>');
  }

  function test_customer_service_rating_badge() {
    ob_start();
    $rvm = new ReevooMark('REV', false, 'http://my_url');
    $rvm->customerServiceRatingBadge(array("trkref" => "REV", "variant" => "stars"));
    $out1 = ob_get_contents();
    ob_end_clean();
    $this->assertEqual($out1,'<a class="reevoo_reputation customer_service stars_variant" href="http://my_url/retailer/REV"></a>');
  }

  function test_delivery_rating_badge() {
    ob_start();
    $rvm = new ReevooMark('REV', false, 'http://my_url');
    $rvm->deliveryRatingBadge(array("trkref" => "REV", "variant" => "stars"));
    $out1 = ob_get_contents();
    ob_end_clean();
    $this->assertEqual($out1,'<a class="reevoo_reputation delivery stars_variant" href="http://my_url/retailer/REV"></a>');
  }

  function test_product_reviews() {
    ob_start();
    $rvm = $this->prepare_embedded_content_request("HTTP/1.1 200 OK\nX-Reevoo-ReviewCount: 5\n\nsome data");
    $rvm->http_client->expectOnce("getData", array("/reevoomark/embeddable_reviews?trkref=REV&sku=123"));
    $this->assertTrue($rvm->productReviews(array("trkref" => "REV", "sku" => "123")));
    $out1 = ob_get_contents();
    ob_end_clean();
    $this->assertEqual($out1,'some data');
  }

  function test_product_reviews_with_no_empty_message() {
    ob_start();
    $rvm = $this->prepare_embedded_content_request("HTTP/1.1 200 OK\nX-Reevoo-ReviewCount: 0\n\nsome data");
    $rvm->http_client->expectOnce("getData", array("/reevoomark/embeddable_reviews?trkref=REV&sku=123"));
    $this->assertFalse($rvm->productReviews(array("trkref" => "REV", "sku" => "123", "showEmptyMessage" => false)));
    $out1 = ob_get_contents();
    ob_end_clean();
    $this->assertEqual($out1, '');
  }

  function test_product_reviews_with_pagination() {
    $rvm = $this->prepare_embedded_content_request("HTTP/1.1 200 OK\n\nsome data");
    $rvm->http_client->expectOnce("getData", array("/reevoomark/embeddable_reviews?trkref=REV&sku=123&per_page=default&page=1"));
    $this->assertFalse($rvm->productReviews(array("trkref" => "REV", "sku" => "123", "paginated" => true)));
  }

  function test_product_reviews_with_pagination_and_custom_number_of_reviews() {
    $rvm = $this->prepare_embedded_content_request("HTTP/1.1 200 OK\n\nsome data");
    $rvm->http_client->expectOnce("getData", array("/reevoomark/embeddable_reviews?trkref=REV&sku=123&per_page=6&page=1"));
    $this->assertFalse($rvm->productReviews(array("trkref" => "REV", "sku" => "123", "paginated" => true, "numberOfReviews" => 6)));
  }

  function test_product_reviews_without_pagination_and_custom_number_of_reviews() {
    $rvm = $this->prepare_embedded_content_request("HTTP/1.1 200 OK\n\nsome data");
    $rvm->http_client->expectOnce("getData", array("/reevoomark/embeddable_reviews?trkref=REV&sku=123&reviews=6"));
    $this->assertFalse($rvm->productReviews(array("trkref" => "REV", "sku" => "123", "numberOfReviews" => 6)));
  }

  function test_product_reviews_with_locale() {
    $rvm = $this->prepare_embedded_content_request("HTTP/1.1 200 OK\n\nsome data");
    $rvm->http_client->expectOnce("getData", array("/reevoomark/embeddable_reviews?trkref=REV&sku=123&per_page=default&page=1&locale=en-GB"));
    $this->assertFalse($rvm->productReviews(array("trkref" => "REV", "sku" => "123", "paginated" => true, "locale" => "en-GB")));
  }

  function test_customer_experience_reviews() {
    ob_start();
    $rvm = $this->prepare_embedded_content_request("HTTP/1.1 200 OK\nX-Reevoo-ReviewCount: 5\n\nsome data");
    $rvm->http_client->expectOnce("getData", array("/reevoomark/embeddable_customer_experience_reviews?trkref=REV"));
    $this->assertTrue($rvm->customerExperienceReviews(array("trkref" => "REV")));
    $out1 = ob_get_contents();
    ob_end_clean();
    $this->assertEqual($out1,'some data');
  }

  function test_customer_experience_reviews_with_no_empty_message() {
    ob_start();
    $rvm = $this->prepare_embedded_content_request("HTTP/1.1 200 OK\nX-Reevoo-ReviewCount: 0\n\nsome data");
    $rvm->http_client->expectOnce("getData", array("/reevoomark/embeddable_customer_experience_reviews?trkref=REV"));
    $this->assertFalse($rvm->customerExperienceReviews(array("trkref" => "REV", "showEmptyMessage" => false)));
    $out1 = ob_get_contents();
    ob_end_clean();
    $this->assertEqual($out1, '');
  }

  function test_customer_experience_reviews_with_pagination() {
    $rvm = $this->prepare_embedded_content_request("HTTP/1.1 200 OK\n\nsome data");
    $rvm->http_client->expectOnce("getData", array("/reevoomark/embeddable_customer_experience_reviews?trkref=REV&per_page=default&page=1"));
    $this->assertFalse($rvm->customerExperienceReviews(array("trkref" => "REV", "sku" => "123", "paginated" => true)));
  }

  function test_customer_experience_reviews_with_pagination_and_custom_review_number() {
    $rvm = $this->prepare_embedded_content_request("HTTP/1.1 200 OK\n\nsome data");
    $rvm->http_client->expectOnce("getData", array("/reevoomark/embeddable_customer_experience_reviews?trkref=REV&per_page=6&page=1"));
    $this->assertFalse($rvm->customerExperienceReviews(array("trkref" => "REV", "sku" => "123", "paginated" => true, "numberOfReviews" => 6)));
  }

  function test_customer_experience_reviews_withouth_pagination_and_custom_review_number() {
    $rvm = new ReevooMark('REV', false, 'http://reevoo');
    $http_client =  new MockedReevooMarkHttpClient("base_uri","cache_path");
    $http_client->setReturnReference('getData', new ReevooMarkDocument("HTTP/1.1 200 OK\n\nsome data",123));
    $rvm->http_client = $http_client;
    $http_client->expectOnce("getData", array("/reevoomark/embeddable_customer_experience_reviews?trkref=REV&reviews=6"));
    $this->assertFalse($rvm->customerExperienceReviews(array("trkref" => "REV", "sku" => "123", "numberOfReviews" => 6)));
  }

  function test_customer_experience_reviews_with_locale() {
    $rvm = $this->prepare_embedded_content_request("HTTP/1.1 200 OK\n\nsome data");
    $rvm->http_client->expectOnce("getData", array("/reevoomark/embeddable_customer_experience_reviews?trkref=REV&reviews=6&locale=en-GB"));
    $this->assertFalse($rvm->customerExperienceReviews(array("trkref" => "REV", "sku" => "123", "numberOfReviews" => 6, "locale" => "en-GB")));
  }

  function test_conversations() {
    ob_start();
    $rvm = $this->prepare_embedded_content_request("HTTP/1.1 200 OK\nX-Reevoo-ConversationCount: 5\n\nsome data");
    $rvm->http_client->expectOnce("getData", array("/reevoomark/embeddable_conversations?trkref=REV&sku=123"));
    $this->assertTrue($rvm->conversations(array("trkref" => "REV", "sku" => "123")));
    $out1 = ob_get_contents();
    ob_end_clean();
    $this->assertEqual($out1,'some data');
  }

  function test_conversations_with_no_empty_message() {
    ob_start();
    $rvm = $this->prepare_embedded_content_request("HTTP/1.1 200 OK\nX-Reevoo-ConversationCount: 0\n\nsome data");
    $rvm->http_client->expectOnce("getData", array("/reevoomark/embeddable_conversations?trkref=REV&sku=123"));
    $this->assertFalse($rvm->conversations(array("trkref" => "REV", "sku" => "123", "showEmptyMessage" => false)));
    $out1 = ob_get_contents();
    ob_end_clean();
    $this->assertEqual($out1,'');
  }

  function test_offers_widget() {
    ob_start();
    $rvm = $this->prepare_embedded_content_request("HTTP/1.1 200 OK\nX-Reevoo-OfferCount: 3\n\nsome data");
    $rvm->http_client->expectOnce("getData", array("/widgets/offers?trkref=REV&sku=123"));
    $this->assertTrue($rvm->offersWidget(array("trkref" => "REV", "sku" => "123")));
    $out1 = ob_get_contents();
    ob_end_clean();
    $this->assertEqual($out1,'some data');
  }

  function test_purchase_tracking_event() {
    ob_start();
    $rvm = new ReevooMark('REV', false, 'http://my_url');
    $rvm->purchaseTrackingEvent(array("trkref" => "REV", "skus" => "111,222,3333", "value" => "250"));
    $out1 = ob_get_contents();
    ob_end_clean();
    $this->assertPattern('/retailer\.track_purchase\(\"111,222,3333\"\.split\(\/\[ ,\]\+\/\), \"250\"\);/',$out1);
  }

  function test_propensity_to_buy_tracking_event() {
    ob_start();
    $rvm = new ReevooMark('REV', false, 'http://reevoo');
    $rvm->propensityToBuyTrackingEvent(array("trkref" => "REV", "action" => "download_brochure", "sku" => "123"));
    $out1 = ob_get_contents();
    ob_end_clean();
    $this->assertPattern('/retailer\.Tracking\.ga_track_event\(\"Propensity to buy\", \"download_brochure\", \"123\"\);/',$out1);
  }

  private function prepare_embedded_content_request($document_data) {
    $rvm = new ReevooMark('REV', false, 'http://reevoo');
    $http_client =  new MockedReevooMarkHttpClient("base_uri","cache_path");
    $http_client->setReturnReference('getData', new ReevooMarkDocument($document_data,123));
    $rvm->http_client = $http_client;
    return $rvm;
  }


}
