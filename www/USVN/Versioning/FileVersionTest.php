<?php
// Call USVN_Versioning_FileVersionTest::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
    define("PHPUnit_MAIN_METHOD", "USVN_Versioning_FileVersionTest::main");
}

require_once "PHPUnit/Framework/TestCase.php";
require_once "PHPUnit/Framework/TestSuite.php";

require_once 'www/USVN/autoload.php';

/**
 * Test class for USVN_Versioning_FileVersion.
 * Generated by PHPUnit_Util_Skeleton on 2007-03-13 at 15:56:20.
 */
class USVN_Versioning_FileVersionTest extends USVN_Versioning_AbstractVersioningTest{
    /**
     * Runs the test methods of this class.
     *
     * @access public
     * @static
     */
    public static function main() {
        require_once "PHPUnit/TextUI/TestRunner.php";

        $suite  = new PHPUnit_Framework_TestSuite("USVN_Versioning_FileVersionTest");
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

	public function testGetVersion() {
	}
/*
    public function testGetVersion() {
		$file = new USVN_Versioning_FileVersion(1, 'trunk/test', 1);
		$this->assertEquals(1, $file->getVersion());
		$file = new USVN_Versioning_FileVersion(1, 'trunk/test', 2);
		$this->assertEquals(2, $file->getVersion());
		$file = new USVN_Versioning_FileVersion(1, 'trunk/test', 10);
		$this->assertEquals(2, $file->getVersion());
    }

    public function testGetPath() {
		$file = new USVN_Versioning_FileVersion(1, 'trunk/test', 1);
		$this->assertEquals('trunk/test', $file->getPath());
    }
	*/
}

// Call USVN_Versioning_FileVersionTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "USVN_Versioning_FileVersionTest::main") {
    USVN_Versioning_FileVersionTest::main();
}
?>
