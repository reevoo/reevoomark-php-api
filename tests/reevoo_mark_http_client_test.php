<?php

require_once('simpletest/autorun.php');
require_once(dirname(__FILE__).'/../lib/reevoo_mark.php');

Mock::generatePartial('ReevooMarkHttpClient', 'MockedReevooMarkHttpClient', array('loadFromRemote'));
Mock::generatePartial('ReevooMarkDocument', 'MockedReevooMarkDocument', array('hasExpired'));
Mock::generatePartial('ReevooMarkCache', 'MockedReevooMarkCache', array('newDocumentFromCache' , 'saveToCache', 'loadFromCache'));



class ReevooMarkHttpClientTest extends UnitTestCase {

  function test_with_expired_cache_copy_should_load_from_remote_server(){
    $rvm = new MockedReevooMarkHttpClient("base_url", "cache_path");
    $cache =  new MockedReevooMarkCache("/path_to_cache");
    $rvm->cache = $cache;
    $document = new MockedReevooMarkDocument("url",111);
    $expired = true;
    $document->setReturnReference('hasExpired', $expired);
    $cache->setReturnReference('newDocumentFromCache', $document);
    $rvm->getData("an_url");
    $rvm->expectOnce("loadFromRemote");
  }

  function test_with_not_expired_cache_copy_should_load_from_cache(){
    $rvm = new MockedReevooMarkHttpClient("base_url", "cache_path");
    $cache =  new MockedReevooMarkCache("/path_to_cache");
    $rvm->cache = $cache;
    $document = new MockedReevooMarkDocument("url",111);
    $expired = false;
    $document->setReturnReference('hasExpired', $expired);
    $cache->setReturnReference('newDocumentFromCache', $document);
    $rvm->getData("an_url");
    $rvm->expectNever("loadFromRemote");
  }

  function test_with_an_empty_cache_and_a_broken_server_we_should_save_to_cache(){
    $rvm = new MockedReevooMarkHttpClient();
    $cache = new MockedReevooMarkCache("/path_to_cache");
    $rvm->cache = $cache;
    $rvm->cache->expectOnce("saveToCache", array(new AnythingExpectation(), "/reevoomark/embeddable_reviews?trkref=REV&sku=123"));
    $expired = true;
    $document = new MockedReevooMarkDocument("url", 111);
    $document->setReturnReference('hasExpired', $expired);
    $cache->setReturnReference('newDocumentFromCache', $document);
    $reevoo_mark = new ReevooMark("REV","/temp");
    $reevoo_mark->http_client = $rvm;
    $reevoo_mark->productReviews(array("trkref" => "REV", "sku" => 123));
    $out1 = ob_get_contents();
  }
//
//  function test_with_an_expired_cache_and_a_broken_server_we_should_load_from_cache(){
//    $rvm = new MockedReevooMark();
//    $rvm->expectOnce("loadFromCache");
//    $rvm->setReturnValue("loadFromCache", file_get_contents(dirname(__FILE__)."/example_document.cache"));
//
//    $rvm->expectOnce("loadFromRemote");
//    $rvm->setReturnValue("loadFromRemote", false);
//
//    $rvm->expectNever("saveToCache");
//
//    $rvm->ReevooMark(null, "http://example.com/mark_url", "AAA", "1234567890");
//
//    $this->assertEqual("Here is my content.\n", $rvm->body());
//  }
//
//  function test_with_an_expired_cache_and_a_500ing_server_we_should_load_from_cache(){
//    $rvm = new MockedReevooMark();
//    $rvm->expectOnce("loadFromCache");
//    $rvm->setReturnValue("loadFromCache", file_get_contents(dirname(__FILE__)."/example_document.cache"));
//
//    $rvm->expectOnce("loadFromRemote");
//    $rvm->setReturnValue("loadFromRemote", "HTTP/1.1 500 My face is on fire\nHead:foo\n\nYour mum.");
//
//    $rvm->expectNever("saveToCache");
//
//    $rvm->ReevooMark(null, "http://example.com/mark_url", "AAA", "1234567890");
//
//    $this->assertEqual("Here is my content.\n", $rvm->body());
//  }
//
//  function test_with_an_expired_cache_and_a_404ing_server_we_should_render_the_response(){
//    $rvm = new MockedReevooMark();
//    $rvm->expectOnce("loadFromCache");
//    $rvm->setReturnValue("loadFromCache", file_get_contents(dirname(__FILE__)."/example_document.cache"));
//
//    $rvm->expectOnce("loadFromRemote");
//    $rvm->setReturnValue("loadFromRemote", "HTTP/1.1 404 page not found\nHead:foo\n\nNo review found.");
//
//    $rvm->expectOnce("saveToCache", array("HTTP/1.1 404 page not found\nHead:foo\n\nNo review found."));
//
//    $rvm->ReevooMark(null, "http://example.com/mark_url", "AAA", "1234567890");
//
//    $this->assertEqual("", $rvm->body());
//  }
//
//  function test_should_report_number_of_reviews(){
//    $rvm = new MockedReevooMark();
//    $rvm->expectOnce("loadFromCache");
//    $rvm->setReturnValue("loadFromCache", "HTTP/1.1 200 OK\nX-Reevoo-ReviewCount:10\n\nHello  10 reviews");
//
//    $rvm->ReevooMark(null, "http://example.com/mark_url", "AAA", "1234567890");
//
//    $this->assertEqual(10, $rvm->reviewCount());
//  }
//
//  function test_should_report_the_best_price(){
//    $rvm = new MockedReevooMark();
//    $rvm->expectOnce("loadFromCache");
//    $rvm->setReturnValue("loadFromCache", "HTTP/1.1 200 OK\nX-Reevoo-BestPrice:£423\n\nHello  10 reviews");
//
//    $rvm->ReevooMark(null, "http://example.com/mark_url", "AAA", "1234567890");
//
//    $this->assertEqual('£423', $rvm->bestPrice());
//  }
//
//  function test_should_generate_cachepath_from_sku(){
//    $rvm = new MockedReevooMarkWithPublicCachePath();
//    $rvm->ReevooMark("foo", "http://example.com/mark_url", "AAA", "1234567890");
//    $digest = md5("http://example.com/mark_url?sku=1234567890&trkref=AAA");
//    $this->assertEqual("foo/$digest.cache", $rvm->getCachePath());
//  }
//
//  function test_should_generate_remote_url(){
//    $rvm = new MockedReevooMarkWithPublicCachePath();
//    $rvm->ReevooMark("foo", "http://example.com/mark_url", "AAA", "1234567890");
//    $this->assertEqual("http://example.com/mark_url?sku=1234567890&trkref=AAA", $rvm->getRemoteUrl());
//  }
}

?>
