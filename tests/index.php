<?php
  require_once('simpletest/autorun.php');

  $test = new TestSuite('All file tests');
  $test->addFile(dirname(__FILE__) . '/reevoo_mark_test.php');
  $test->addFile(dirname(__FILE__) . '/reevoo_mark_document_test.php');
  $test->addFile(dirname(__FILE__) . '/reevoo_mark_http_client_test.php');
  exit ($test->run(new TextReporter()) ? 0 : 1);
?>
