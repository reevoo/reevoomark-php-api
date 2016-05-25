<?php

require_once('simpletest/autorun.php');
require_once(dirname(__FILE__).'/../lib/reevoo_mark_api.php');


Mock::generatePartial('ReevooMarkHttpClient', 'MockedReevooMarkHttpClient', array('getData'));

class ReevooMarkApiTest extends UnitTestCase {

  function test_css_assets_prints_the_correct_css_link() {
    $rvm = new ReevooMarkApi('REV', false, 'http://my_url');
    $this->assertEqual(
      $rvm->cssAssets(),
      '<link rel="stylesheet" href="//mark.reevoo.com/stylesheets/reevoomark/embedded_reviews.css" type="text/css" />'
    );
  }

  function test_product_badge() {
    $rvm = new ReevooMarkApi('REV', false, 'http://my_url');
    $this->assertEqual(
      $rvm->productBadge(array("trkref" => "REV", "variant" => "stars", "sku" => "123")),
      '<a class="reevoomark stars_variant" href="http://my_url/partner/REV/123"></a>'
    );
  }

  function test_product_badge_with_registration() {
    $rvm = new ReevooMarkApi('REV', false, 'http://my_url');
    $this->assertEqual(
      $rvm->productBadge(array("trkref" => "REV", "variant" => "stars", "registration" => "AB12CDE")),
      '<a class="reevoomark stars_variant" href="http://my_url/partner/REV/AB12CDE?identifier=registration"></a>'
    );
  }

  function test_undecorated_product_badge() {
    $rvm = new ReevooMarkApi('REV', false, 'http://my_url');
    $this->assertEqual(
      $rvm->productBadge(array("trkref" => "REV", "variant" => "undecorated", "sku" => "123")),
      '<a class="reevoomark undecorated" href="http://my_url/partner/REV/123"></a>'
    );
  }

  function test_conversations_badge() {
    $rvm = new ReevooMarkApi('REV', false, 'http://my_url');
    $this->assertEqual(
      $rvm->conversationsBadge(array("trkref" => "REV", "variant" => "stars", "sku" => "123")),
      '<a class="reevoomark reevoo-conversations stars_variant" href="http://my_url/partner/REV/123"></a>'
    );
  }

  function test_conversations_badge_with_registration() {
    $rvm = new ReevooMarkApi('REV', false, 'http://my_url');
    $this->assertEqual(
      $rvm->conversationsBadge(array("trkref" => "REV", "variant" => "stars", "registration" => "AB12CDE")),
      '<a class="reevoomark reevoo-conversations stars_variant" href="http://my_url/partner/REV/AB12CDE?identifier=registration"></a>'
    );
  }

  function test_product_series_badge() {
    $rvm = new ReevooMarkApi('REV', false, 'http://my_url');
    $this->assertEqual(
      $rvm->productSeriesBadge(array("trkref" => "REV", "variant" => "stars", "sku" => "123")),
      '<a class="reevoomark stars_variant" href="http://my_url/partner/REV/series:123"></a>'
    );
  }

  function test_conversation_series_badge() {
    $rvm = new ReevooMarkApi('REV', false, 'http://my_url');
    $this->assertEqual(
      $rvm->conversationSeriesBadge(array("trkref" => "REV", "variant" => "stars", "sku" => "123")),
      '<a class="reevoomark reevoo-conversations stars_variant" href="http://my_url/partner/REV/series:123"></a>'
    );
  }

  function test_overall_service_rating_badge() {
    $rvm = new ReevooMarkApi('REV', false, 'http://my_url');
    $this->assertEqual(
      $rvm->overallServiceRatingBadge(array("trkref" => "REV", "variant" => "stars")),
      '<a class="reevoo_reputation stars_variant" href="http://my_url/retailer/REV"></a>'
    );
  }

  function test_customer_service_rating_badge() {
    $rvm = new ReevooMarkApi('REV', false, 'http://my_url');
    $this->assertEqual(
      $rvm->customerServiceRatingBadge(array("trkref" => "REV", "variant" => "stars")),
      '<a class="reevoo_reputation customer_service stars_variant" href="http://my_url/retailer/REV"></a>'
    );
  }

  function test_delivery_rating_badge() {
    $rvm = new ReevooMarkApi('REV', false, 'http://my_url');
    $this->assertEqual(
      $rvm->deliveryRatingBadge(array("trkref" => "REV", "variant" => "stars")),
      '<a class="reevoo_reputation delivery stars_variant" href="http://my_url/retailer/REV"></a>'
    );
  }

  function test_product_reviews() {
    $rvm = $this->prepare_embedded_content_request("HTTP/1.1 200 OK\nX-Reevoo-ReviewCount: 5\n\nsome data");
    $rvm->http_client->expectOnce("getData", array("/reevoomark/embeddable_reviews?trkref=REV&sku=123"));
    $productReviews = $rvm->productReviews(array("trkref" => "REV", "sku" => "123"));
    $this->assertTrue($productReviews['notEmpty']);
    $this->assertEqual($productReviews['content'],'some data');
  }

  function test_product_reviews_with_registation() {
    $rvm = $this->prepare_embedded_content_request("HTTP/1.1 200 OK\nX-Reevoo-ReviewCount: 5\n\nsome data");
    $rvm->http_client->expectOnce("getData", array("/reevoomark/embeddable_reviews?trkref=REV&registration=AB12CDE"));
    $productReviews = $rvm->productReviews(array("trkref" => "REV", "registration" => "AB12CDE"));
    $this->assertTrue($productReviews['notEmpty']);
    $this->assertEqual($productReviews['content'],'some data');
  }

  function test_product_reviews_with_no_empty_message() {
    $rvm = $this->prepare_embedded_content_request("HTTP/1.1 200 OK\nX-Reevoo-ReviewCount: 0\n\nsome data");
    $rvm->http_client->expectOnce("getData", array("/reevoomark/embeddable_reviews?trkref=REV&sku=123"));
    $productReviews = $rvm->productReviews(array("trkref" => "REV", "sku" => "123", "showEmptyMessage" => false));
    $this->assertFalse($productReviews['notEmpty']);
    $this->assertEqual($productReviews['content'], '');
  }

  function test_product_reviews_with_pagination() {
    $rvm = $this->prepare_embedded_content_request("HTTP/1.1 200 OK\n\nsome data");
    $rvm->http_client->expectOnce("getData", array("/reevoomark/embeddable_reviews?trkref=REV&sku=123&per_page=default&page=1"));
    $productReviews = $rvm->productReviews(array("trkref" => "REV", "sku" => "123", "paginated" => true));
    $this->assertFalse($productReviews['notEmpty']);
  }

  function test_product_reviews_with_pagination_and_custom_number_of_reviews() {
    $rvm = $this->prepare_embedded_content_request("HTTP/1.1 200 OK\n\nsome data");
    $rvm->http_client->expectOnce("getData", array("/reevoomark/embeddable_reviews?trkref=REV&sku=123&per_page=6&page=1"));
    $productReviews = $rvm->productReviews(array("trkref" => "REV", "sku" => "123", "paginated" => true, "numberOfReviews" => 6));
    $this->assertFalse($productReviews['notEmpty']);
  }

  function test_product_reviews_without_pagination_and_custom_number_of_reviews() {
    $rvm = $this->prepare_embedded_content_request("HTTP/1.1 200 OK\n\nsome data");
    $rvm->http_client->expectOnce("getData", array("/reevoomark/embeddable_reviews?trkref=REV&sku=123&reviews=6"));
    $productReviews = $rvm->productReviews(array("trkref" => "REV", "sku" => "123", "numberOfReviews" => 6));
    $this->assertFalse($productReviews['notEmpty']);
  }

  function test_product_reviews_with_locale() {
    $rvm = $this->prepare_embedded_content_request("HTTP/1.1 200 OK\n\nsome data");
    $rvm->http_client->expectOnce("getData", array("/reevoomark/embeddable_reviews?trkref=REV&sku=123&per_page=default&page=1&locale=en-GB"));
    $productReviews = $rvm->productReviews(array("trkref" => "REV", "sku" => "123", "paginated" => true, "locale" => "en-GB"));
    $this->assertFalse($productReviews['notEmpty']);
  }

  function test_customer_experience_reviews() {
    $rvm = $this->prepare_embedded_content_request("HTTP/1.1 200 OK\nX-Reevoo-ReviewCount: 5\n\nsome data");
    $rvm->http_client->expectOnce("getData", array("/reevoomark/embeddable_customer_experience_reviews?trkref=REV"));
    $customerExperienceReviews = $rvm->customerExperienceReviews(array("trkref" => "REV"));
    $this->assertTrue($customerExperienceReviews['notEmpty']);
    $this->assertEqual($customerExperienceReviews['content'],'some data');
  }

  function test_customer_experience_reviews_with_no_empty_message() {
    $rvm = $this->prepare_embedded_content_request("HTTP/1.1 200 OK\nX-Reevoo-ReviewCount: 0\n\nsome data");
    $rvm->http_client->expectOnce("getData", array("/reevoomark/embeddable_customer_experience_reviews?trkref=REV"));
    $customerExperienceReviews = $rvm->customerExperienceReviews(array("trkref" => "REV", "showEmptyMessage" => false));
    $this->assertFalse($customerExperienceReviews['notEmpty']);
    $this->assertEqual($customerExperienceReviews['content'], '');
  }

  function test_customer_experience_reviews_with_pagination() {
    $rvm = $this->prepare_embedded_content_request("HTTP/1.1 200 OK\n\nsome data");
    $rvm->http_client->expectOnce("getData", array("/reevoomark/embeddable_customer_experience_reviews?trkref=REV&per_page=default&page=1"));
    $customerExperienceReviews = $rvm->customerExperienceReviews(array("trkref" => "REV", "sku" => "123", "paginated" => true));
    $this->assertFalse($customerExperienceReviews['notEmpty']);
  }

  function test_customer_experience_reviews_with_pagination_and_custom_review_number() {
    $rvm = $this->prepare_embedded_content_request("HTTP/1.1 200 OK\n\nsome data");
    $rvm->http_client->expectOnce("getData", array("/reevoomark/embeddable_customer_experience_reviews?trkref=REV&per_page=6&page=1"));
    $customerExperienceReviews = $rvm->customerExperienceReviews(array("trkref" => "REV", "sku" => "123", "paginated" => true, "numberOfReviews" => 6));
    $this->assertFalse($customerExperienceReviews['notEmpty']);
  }

  function test_customer_experience_reviews_withouth_pagination_and_custom_review_number() {
    $rvm = new ReevooMarkApi('REV', false, 'http://reevoo');
    $http_client =  new MockedReevooMarkHttpClient("base_uri","cache_path");
    $http_client->setReturnReference('getData', new ReevooMarkDocument("HTTP/1.1 200 OK\n\nsome data",123));
    $rvm->http_client = $http_client;
    $http_client->expectOnce("getData", array("/reevoomark/embeddable_customer_experience_reviews?trkref=REV&reviews=6"));
    $customerExperienceReviews = $rvm->customerExperienceReviews(array("trkref" => "REV", "sku" => "123", "numberOfReviews" => 6));
    $this->assertFalse($customerExperienceReviews['notEmpty']);
  }

  function test_customer_experience_reviews_with_locale() {
    $rvm = $this->prepare_embedded_content_request("HTTP/1.1 200 OK\n\nsome data");
    $rvm->http_client->expectOnce("getData", array("/reevoomark/embeddable_customer_experience_reviews?trkref=REV&reviews=6&locale=en-GB"));
    $customerExperienceReviews = $rvm->customerExperienceReviews(array("trkref" => "REV", "sku" => "123", "numberOfReviews" => 6, "locale" => "en-GB"));
    $this->assertFalse($customerExperienceReviews['notEmpty']);
  }

  function test_conversations() {
    $rvm = $this->prepare_embedded_content_request("HTTP/1.1 200 OK\nX-Reevoo-ConversationCount: 5\n\nsome data");
    $rvm->http_client->expectOnce("getData", array("/reevoomark/embeddable_conversations?trkref=REV&sku=123"));
    $conversations = $rvm->conversations(array("trkref" => "REV", "sku" => "123"));
    $this->assertTrue($conversations['notEmpty']);
    $this->assertEqual($conversations['content'],'some data');
  }

  function test_conversations_with_registration() {
    $rvm = $this->prepare_embedded_content_request("HTTP/1.1 200 OK\nX-Reevoo-ConversationCount: 5\n\nsome data");
    $rvm->http_client->expectOnce("getData", array("/reevoomark/embeddable_conversations?trkref=REV&registration=AB12CDE"));
    $conversations = $rvm->conversations(array("trkref" => "REV", "registration" => "AB12CDE"));
    $this->assertTrue($conversations['notEmpty']);
    $this->assertEqual($conversations['content'],'some data');
  }

  function test_conversations_with_no_empty_message() {
    $rvm = $this->prepare_embedded_content_request("HTTP/1.1 200 OK\nX-Reevoo-ConversationCount: 0\n\nsome data");
    $rvm->http_client->expectOnce("getData", array("/reevoomark/embeddable_conversations?trkref=REV&sku=123"));
    $conversations = $rvm->conversations(array("trkref" => "REV", "sku" => "123", "showEmptyMessage" => false));
    $this->assertFalse($conversations['notEmpty']);
    $this->assertEqual($conversations['content'],'');
  }

  function test_offers_widget() {
    $rvm = $this->prepare_embedded_content_request("HTTP/1.1 200 OK\nX-Reevoo-OfferCount: 3\n\nsome data");
    $rvm->http_client->expectOnce("getData", array("/widgets/offers?trkref=REV&sku=123"));
    $offersWidget = $rvm->offersWidget(array("trkref" => "REV", "sku" => "123"));
    $this->assertTrue($offersWidget['notEmpty']);
    $this->assertEqual($offersWidget['content'],'some data');
  }

  function test_offers_widget_with_registation() {
    $rvm = $this->prepare_embedded_content_request("HTTP/1.1 200 OK\nX-Reevoo-OfferCount: 3\n\nsome data");
    $rvm->http_client->expectOnce("getData", array("/widgets/offers?trkref=REV&registration=AB12CDE"));
    $offersWidget = $rvm->offersWidget(array("trkref" => "REV", "registration" => "AB12CDE"));
    $this->assertTrue($offersWidget['notEmpty']);
    $this->assertEqual($offersWidget['content'],'some data');
  }

  function test_purchase_tracking_event() {
    $rvm = new ReevooMarkApi('REV', false, 'http://my_url');
    $this->assertPattern(
      '/retailer\.track_purchase\(\"111,222,3333\"\.split\(\/\[ ,\]\+\/\), \"250\"\);/',
      $rvm->purchaseTrackingEvent(array("trkref" => "REV", "skus" => "111,222,3333", "value" => "250"))
    );
  }

  function test_propensity_to_buy_tracking_event() {
    $rvm = new ReevooMarkApi('REV', false, 'http://reevoo');
    $this->assertPattern(
      '/retailer\.Tracking\.ga_track_event\(\"Propensity to buy\", \"download_brochure\", \"123\"\);/',
      $rvm->propensityToBuyTrackingEvent(array("trkref" => "REV", "action" => "download_brochure", "sku" => "123"))
    );
  }

  private function prepare_embedded_content_request($document_data) {
    $rvm = new ReevooMarkApi('REV', false, 'http://reevoo');
    $http_client =  new MockedReevooMarkHttpClient("base_uri","cache_path");
    $http_client->setReturnReference('getData', new ReevooMarkDocument($document_data,123));
    $rvm->http_client = $http_client;
    return $rvm;
  }


}
