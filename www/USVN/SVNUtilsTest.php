<?php
/**
 * Usefull static method to manipulate an svn repository
 *
 * @author Team USVN <contact@usvn.info>
 * @link http://www.usvn.info
 * @license http://www.cecill.info/licences/Licence_CeCILL_V2-en.txt CeCILL V2
 * @copyright Copyright 2007, Team USVN
 * @since 0.5
 * @package client
 * @subpackage utils
 *
 * This software has been written at EPITECH <http://www.epitech.net>
 * EPITECH, European Institute of Technology, Paris - FRANCE -
 * This project has been realised as part of
 * end of studies project.
 *
 * $Id$
 */

// Call USVN_SVNUtilsTest::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
    define("PHPUnit_MAIN_METHOD", "USVN_SVNUtilsTest::main");
}

require_once "PHPUnit/Framework/TestCase.php";
require_once "PHPUnit/Framework/TestSuite.php";

require_once 'www/USVN/autoload.php';

/**
 * Test class for USVN_SVNUtils.
 * Generated by PHPUnit_Util_Skeleton on 2007-03-10 at 17:59:54.
 */
class USVN_SVNUtilsTest extends USVN_Test_Test {
    /**
     * Runs the test methods of this class.
     *
     * @access public
     * @static
     */
    public static function main() {
        require_once "PHPUnit/TextUI/TestRunner.php";

        $suite  = new PHPUnit_Framework_TestSuite("USVN_SVNUtilsTest");
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

    public function setUp()
    {
		parent::setUp();
        $this->clean();
        USVN_SVNUtils::createSvnDirectoryStruct("tests/tmp/testrepository");
        @mkdir('tests/tmp/fakerepository');
    }

    public function tearDown()
    {
        $this->clean();
		parent::tearDown();
    }

    private function clean()
    {
		USVN_DirectoryUtils::removeDirectory('tests/tmp/testrepository');
		USVN_DirectoryUtils::removeDirectory('tests/tmp/fakerepository');
		USVN_DirectoryUtils::removeDirectory('tests/tmp/svndirectorystruct');
		USVN_DirectoryUtils::removeDirectory('tests/tmp/svndirectory');
    }

    public function test_isSVNRepository()
    {
        $this->assertTrue(USVN_SVNUtils::isSVNRepository('tests/tmp/testrepository'));
        $this->assertFalse(USVN_SVNUtils::isSVNRepository('tests/tmp/fakerepository'));
    }

	public function test_changedFiles()
	{
        $this->assertEquals(array(array("U", "tutu")), USVN_SVNUtils::changedFiles("U tutu\n"));
        $this->assertEquals(array(array("U", "tutu"), array("U", "tata")), USVN_SVNUtils::changedFiles("U tutu\nU tata\n"));
        $this->assertEquals(array(array("U", "tutu"), array("U", "U")), USVN_SVNUtils::changedFiles("U tutu\nU U\n"));
        $this->assertEquals(array(array("U", "tutu"), array("U", "hello world"), array("U", "toto")), USVN_SVNUtils::changedFiles("U tutu\nU hello world\nU toto\n"));
	}

	public function test_createSvnDirectoryStruct()
	{
		USVN_SVNUtils::createSvnDirectoryStruct('tests/tmp/svndirectorystruct');
		 $this->assertTrue(file_exists('tests/tmp/svndirectorystruct'));
		 $this->assertTrue(file_exists('tests/tmp/svndirectorystruct/hooks'));
	}

	public function test_createSvn()
	{
		USVN_SVNUtils::createSvn('tests/tmp/svndirectory');
		 $this->assertTrue(file_exists('tests/tmp/svndirectory'));
		 $this->assertTrue(USVN_SVNUtils::isSVNRepository('tests/tmp/svndirectory'));
	}

	public function test_createSvnBadDir()
	{
		try {
			USVN_SVNUtils::createSvn('tests/tmp/svndirectory/test/tutu');
		}
		catch (USVN_Exception $e) {
			return ;
		}
		$this->fail();
	}

	public function test_parseSvnVersion()
	{
		$this->assertEquals(array(1, 1, 4), USVN_SVNUtils::parseSvnVersion("svn, version 1.1.4 (r13838)
   compiled May 13 2005, 06:29:47

Copyright (C) 2000-2004 CollabNet.
Subversion is open source software, see http://subversion.tigris.org/
This product includes software developed by CollabNet (http://www.Collab.Net/).

The following repository access (RA) modules are available:

* ra_dav : Module for accessing a repository via WebDAV (DeltaV) protocol.
  - handles 'http' schema
  - handles 'https' schema
* ra_local : Module for accessing a repository on local disk.
  - handles 'file' schema
* ra_svn : Module for accessing a repository using the svn network protocol.
  - handles 'svn' schema
"));
	}
}

// Call USVN_SVNUtilsTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "USVN_SVNUtilsTest::main") {
    USVN_SVNUtilsTest::main();
}
?>
