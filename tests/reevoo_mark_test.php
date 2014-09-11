<?php

require_once('simpletest/autorun.php');
require_once(dirname(__FILE__).'/../lib/reevoo_mark.php');

Mock::generatePartial('ReevooMark', 'MockedReevooMark', array('loadFromCache', 'saveToCache', 'loadFromRemote'));

class MockedReevooMarkWithPublicCachePath extends MockedReevooMark{
  function getRemoteUrl(){
    return $this->remote_url;
  }

  function getCachePath(){
    return $this->cachePath();
  }
}



class ReevooMarkTest extends UnitTestCase {
  function test_with_no_cached_copy_we_should_load_from_remote_server(){
    $rvm = new MockedReevooMark();
    $rvm->expectOnce("loadFromCache");
    $rvm->setReturnValue("loadFromCache", false);

    $rvm->expectOnce("loadFromRemote");
    $rvm->setReturnValue("loadFromRemote", file_get_contents(dirname(__FILE__)."/example_document.cache"));

    $rvm->expectOnce("saveToCache", array(file_get_contents(dirname(__FILE__)."/example_document.cache")));

    $rvm->ReevooMark(null, "http://example.com/mark_url", "AAA", "1234567890");

    $this->assertEqual("Here is my content.\n", $rvm->body());
  }

  function test_with_an_expired_cache_we_should_load_from_remote(){
    $rvm = new MockedReevooMark();
    $rvm->expectOnce("loadFromCache");
    $rvm->setReturnValue("loadFromCache", file_get_contents(dirname(__FILE__)."/example_document.cache"));

    $rvm->expectOnce("loadFromRemote");
    $rvm->setReturnValue("loadFromRemote", file_get_contents(dirname(__FILE__)."/example_document_two.cache"));

    $rvm->expectOnce("saveToCache", array(file_get_contents(dirname(__FILE__)."/example_document_two.cache")));

    $rvm->ReevooMark(null, "http://example.com/mark_url", "AAA", "1234567890");

    $this->assertEqual("Some more content.\n", $rvm->body());
  }

  function test_with_an_empty_cache_and_a_broken_server_we_should_save_to_cache(){
    $rvm = new MockedReevooMark();
    $rvm->expectOnce("loadFromCache");
    $rvm->setReturnValue("loadFromCache", false);

    $rvm->expectOnce("loadFromRemote");
    $rvm->setReturnValue("loadFromRemote", false);

    $rvm->expectOnce("saveToCache", array(false));

    $rvm->ReevooMark(null, "http://example.com/mark_url", "AAA", "1234567890");

    $this->assertEqual("", $rvm->body());
  }

  function test_with_an_expired_cache_and_a_broken_server_we_should_load_from_cache(){
    $rvm = new MockedReevooMark();
    $rvm->expectOnce("loadFromCache");
    $rvm->setReturnValue("loadFromCache", file_get_contents(dirname(__FILE__)."/example_document.cache"));

    $rvm->expectOnce("loadFromRemote");
    $rvm->setReturnValue("loadFromRemote", false);

    $rvm->expectNever("saveToCache");

    $rvm->ReevooMark(null, "http://example.com/mark_url", "AAA", "1234567890");

    $this->assertEqual("Here is my content.\n", $rvm->body());
  }

  function test_with_an_expired_cache_and_a_500ing_server_we_should_load_from_cache(){
    $rvm = new MockedReevooMark();
    $rvm->expectOnce("loadFromCache");
    $rvm->setReturnValue("loadFromCache", file_get_contents(dirname(__FILE__)."/example_document.cache"));

    $rvm->expectOnce("loadFromRemote");
    $rvm->setReturnValue("loadFromRemote", "HTTP/1.1 500 My face is on fire\nHead:foo\n\nYour mum.");

    $rvm->expectNever("saveToCache");

    $rvm->ReevooMark(null, "http://example.com/mark_url", "AAA", "1234567890");

    $this->assertEqual("Here is my content.\n", $rvm->body());
  }

  function test_with_an_expired_cache_and_a_404ing_server_we_should_render_the_response(){
    $rvm = new MockedReevooMark();
    $rvm->expectOnce("loadFromCache");
    $rvm->setReturnValue("loadFromCache", file_get_contents(dirname(__FILE__)."/example_document.cache"));

    $rvm->expectOnce("loadFromRemote");
    $rvm->setReturnValue("loadFromRemote", "HTTP/1.1 404 page not found\nHead:foo\n\nNo review found.");

    $rvm->expectOnce("saveToCache", array("HTTP/1.1 404 page not found\nHead:foo\n\nNo review found."));

    $rvm->ReevooMark(null, "http://example.com/mark_url", "AAA", "1234567890");

    $this->assertEqual("", $rvm->body());
  }

  function test_should_report_number_of_reviews(){
    $rvm = new MockedReevooMark();
    $rvm->expectOnce("loadFromCache");
    $rvm->setReturnValue("loadFromCache", "HTTP/1.1 200 OK\nX-Reevoo-ReviewCount:10\n\nHello  10 reviews");

    $rvm->ReevooMark(null, "http://example.com/mark_url", "AAA", "1234567890");

    $this->assertEqual(10, $rvm->reviewCount());
  }

  function test_should_report_the_best_price(){
    $rvm = new MockedReevooMark();
    $rvm->expectOnce("loadFromCache");
    $rvm->setReturnValue("loadFromCache", "HTTP/1.1 200 OK\nX-Reevoo-BestPrice:£423\n\nHello  10 reviews");

    $rvm->ReevooMark(null, "http://example.com/mark_url", "AAA", "1234567890");

    $this->assertEqual('£423', $rvm->bestPrice());
  }

  function test_should_generate_cachepath_from_sku(){
    $rvm = new MockedReevooMarkWithPublicCachePath();
    $rvm->ReevooMark("foo", "http://example.com/mark_url", "AAA", "1234567890");
    $digest = md5("http://example.com/mark_url?sku=1234567890&trkref=AAA");
    $this->assertEqual("foo/$digest.cache", $rvm->getCachePath());
  }

  function test_should_generate_remote_url(){
    $rvm = new MockedReevooMarkWithPublicCachePath();
    $rvm->ReevooMark("foo", "http://example.com/mark_url", "AAA", "1234567890");
    $this->assertEqual("http://example.com/mark_url?sku=1234567890&trkref=AAA", $rvm->getRemoteUrl());
  }
}

?>
