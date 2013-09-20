<?php
  require_once('simpletest/autorun.php');

  $test = new TestSuite('All file tests');
  $test->addFile('tests/reevoo_mark_test.php');
  $test->addFile('tests/reevoo_mark_document_test.php');
  exit ($test->run(new TextReporter()) ? 0 : 1);
?>
