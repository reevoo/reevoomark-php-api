<?php

require_once('simpletest/autorun.php');
require_once(dirname(__FILE__).'/../lib/reevoo_mark.php');

Mock::generatePartial('ReevooMarkHttpClient', 'MockedReevooMarkHttpClient', array('loadFromRemote'));
Mock::generatePartial('ReevooMarkCache', 'MockedReevooMarkCache', array('saveToCache', 'loadFromCache'));


class ReevooMarkHttpClientTest extends UnitTestCase {

  function test_with_no_cached_copy_we_should_load_from_remote_server(){
    $http = new MockedReevooMarkHttpClient();
    $cache = new MockedReevooMarkCache();
    $http->cache = $cache;

    $cache->expectOnce("loadFromCache");
    $cache->setReturnValue("loadFromCache", false);

    $http->expectOnce("loadFromRemote");
    $cache_content = file_get_contents(dirname(__FILE__)."/expired_document.cache");
    $http->setReturnValue("loadFromRemote", $cache_content);
    $cache->expectOnce("saveToCache", array($cache_content, '/test-url'));

    $this->assertEqual("Here is my content.\n", $http->getData('/test-url')->body());
  }

  function test_with_an_expired_cache_we_should_load_from_remote(){
    $http = new MockedReevooMarkHttpClient();
    $cache = new MockedReevooMarkCache();
    $http->cache = $cache;

    $cache->expectOnce("loadFromCache");
    $cache->setReturnValue("loadFromCache", file_get_contents(dirname(__FILE__)."/expired_document.cache"));

    $http->expectOnce("loadFromRemote");
    $http_response = file_get_contents(dirname(__FILE__)."/not_expired_document.cache");
    $http->setReturnValue("loadFromRemote", $http_response);
    $cache->expectOnce("saveToCache", array($http_response, '/test-url'));

    $this->assertEqual("Some more content.\n", $http->getData('/test-url')->body());
  }

  function test_with_an_empty_cache_and_a_broken_server_we_should_save_to_cache(){
    $http = new MockedReevooMarkHttpClient();
    $cache = new MockedReevooMarkCache();
    $http->cache = $cache;

    $cache->expectOnce("loadFromCache");
    $cache->setReturnValue("loadFromCache", false);
    $http->expectOnce("loadFromRemote");
    $http->setReturnValue("loadFromRemote", false);
    $cache->expectOnce("saveToCache", array(false, '/test-url'));

    $this->assertEqual("", $http->getData('/test-url')->body());
  }

 function test_with_an_expired_cache_and_a_broken_server_we_should_load_from_cache(){
    $http = new MockedReevooMarkHttpClient();
    $cache = new MockedReevooMarkCache();
    $http->cache = $cache;

    $cache->expectOnce("loadFromCache");
    $cache->setReturnValue("loadFromCache", file_get_contents(dirname(__FILE__)."/expired_document.cache"));

    $http->expectOnce("loadFromRemote");
    $http->setReturnValue("loadFromRemote", false);

    $cache->expectNever("saveToCache");

    $this->assertEqual("Here is my content.\n", $http->getData('/test-url')->body());
  }

  function test_with_an_expired_cache_and_a_500ing_server_we_should_load_from_cache(){
    $http = new MockedReevooMarkHttpClient();
    $cache = new MockedReevooMarkCache();
    $http->cache = $cache;

    $cache->expectOnce("loadFromCache");
    $cache->setReturnValue("loadFromCache", file_get_contents(dirname(__FILE__)."/expired_document.cache"));

    $http->expectOnce("loadFromRemote");
    $http->setReturnValue("loadFromRemote", "HTTP/1.1 500 My face is on fire\nHead:foo\n\nYour mum.");

    $cache->expectNever("saveToCache");

    $this->assertEqual("Here is my content.\n", $http->getData('/test-url')->body());
  }

  function test_with_an_expired_cache_and_a_404ing_server_we_should_render_the_response(){
    $http = new MockedReevooMarkHttpClient();
    $cache = new MockedReevooMarkCache();
    $http->cache = $cache;

    $cache->expectOnce("loadFromCache");
    $cache->setReturnValue("loadFromCache", file_get_contents(dirname(__FILE__)."/expired_document.cache"));

    $http->expectOnce("loadFromRemote");
    $http->setReturnValue("loadFromRemote", "HTTP/1.1 404 page not found\nHead:foo\n\nNo review found.");

    $cache->expectOnce("saveToCache", array("HTTP/1.1 404 page not found\nHead:foo\n\nNo review found.", '/test-url'));

    $this->assertEqual("", $http->getData('/test-url')->body());
  }

}
