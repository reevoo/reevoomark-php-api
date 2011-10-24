<?php

  require_once('simpletest/unit_tester.php');
  require_once('simpletest/reporter.php');
  require_once('reevoo_mark_test.php');
  require_once('reevoo_mark_document_test.php');

  $test = new TestSuite('All file tests');
  $test->addTestCase(new ReevooMarkTest());
  $test->addTestCase(new ReevooMarkDocumentTest());
  $test->addTestCase(new NoneExpiredReevooMarkDocumentTest());
  $test->addTestCase(new ReevooMarkDocumentWithBlankLinesTest());
  $test->addTestCase(new ReevooMarkDocumentWithAHeaderModifiedByAProxyTest());
  $test->run(new TextReporter());
  
?>
