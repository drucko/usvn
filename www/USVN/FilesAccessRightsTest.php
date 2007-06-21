<?php
/**
 * Check if a group can access to a file on the subversion
 *
 * @author Team USVN <contact@usvn.info>
 * @link http://www.usvn.info
 * @license http://www.cecill.info/licences/Licence_CeCILL_V2-en.txt CeCILL V2
 * @copyright Copyright 2007, Team USVN
 * @since 0.5
 * @package usvn
 *
 * This software has been written at EPITECH <http://www.epitech.net>
 * EPITECH, European Institute of Technology, Paris - FRANCE -
 * This project has been realised as part of
 * end of studies project.
 *
 * $Id: DirectoryUtils.php 404 2007-05-14 10:43:41Z duponc_j $
 */
// Call USVN_FilesAccesRightsTest::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
    define("PHPUnit_MAIN_METHOD", "USVN_FilesAccesRightsTest::main");
}

require_once "PHPUnit/Framework/TestCase.php";
require_once "PHPUnit/Framework/TestSuite.php";

require_once 'www/USVN/autoload.php';

/**
 * Test class for USVN_FilesAccesRights.
 * Generated by PHPUnit_Util_Skeleton on 2007-04-03 at 09:22:11.
 */
class USVN_FilesAccesRightsTest extends USVN_Test_DB {
	private $_projectid1;
	private $_projectid2;
	private $_groupid1;

    /**
     * Runs the test methods of this class.
     *
     * @access public
     * @static
     */
    public static function main() {
        require_once "PHPUnit/TextUI/TestRunner.php";

        $suite  = new PHPUnit_Framework_TestSuite("USVN_FilesAccesRightsTest");
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

    public function setUp()
    {
    	parent::setUp();

		$table = new USVN_Db_Table_Users();
		$this->_user = $table->fetchNew();
		$this->_user->setFromArray(array('users_login' 	=> 'test',
																'users_password' 	=> 'password',
																'users_firstname' 	=> 'firstname',
																'users_lastname' 	=> 'lastname',
																'users_email' 		=> 'email@email.fr'));
		$this->_user->save();

		$this->_projectid1 = USVN_Project::createProject(array('projects_name'  => "project1"), "test", true, false, false)->id;
		$this->_projectid2 = USVN_Project::createProject(array('projects_name'  => "project2"), "test", true, false, false)->id;

		$group_table = new USVN_Db_Table_Groups();
		$group = $group_table->fetchNew();
		$group->setFromArray(array("groups_name" => "toto"));
		$this->_groupid1 = $group->save();

		$group_table = new USVN_Db_Table_Groups();
		$group = $group_table->fetchNew();
		$group->setFromArray(array("groups_name" => "titi"));
		$this->_groupid2 = $group->save();
    }

    public function test_findByPath()
    {
    	$file_rights1 = new USVN_FilesAccessRights($this->_projectid1);
    	$file_rights2 = new USVN_FilesAccessRights($this->_projectid2);

    	$rights = $file_rights1->findByPath($this->_groupid1, '/');
    	$this->assertFalse($rights['read']);
    	$this->assertFalse($rights['write']);

    	$table_files = new USVN_Db_Table_FilesRights();
    	$fileid = $table_files->findByPath($this->_projectid1, '/')->id;
		$table_groupstofiles = new USVN_Db_Table_GroupsToFilesRights();
		$table_groupstofiles->insert(array(
			'files_rights_id' 		  => $fileid,
		   'files_rights_is_readable' => true,
		   'files_rights_is_writable' => true,
		   'groups_id'	 			  => $this->_groupid1
		));

    	$rights = $file_rights1->findByPath($this->_groupid1, '/');
    	$this->assertTrue($rights['read']);
    	$this->assertTrue($rights['write']);

    	$fileid = $table_files->insert(array(
    		'projects_id'		=> $this->_projectid1,
			'files_rights_path' => '/trunk'
		));

    	$rights = $file_rights1->findByPath($this->_groupid1, '/trunk');
    	$this->assertTrue($rights['read']);
    	$this->assertTrue($rights['write']);

		$table_groupstofiles->insert(array(
			'files_rights_id' 		  => $fileid,
		   'files_rights_is_readable' => true,
		   'files_rights_is_writable' => true,
		   'groups_id'	 			  => $this->_groupid1
		));

		$rights = $file_rights1->findByPath($this->_groupid1, '/trunk');
    	$this->assertTrue($rights['read']);
    	$this->assertTrue($rights['write']);
    }

    public function test_findByPathInherits()
    {
    	$file_rights1 = new USVN_FilesAccessRights($this->_projectid1);
    	$table_files = new USVN_Db_Table_FilesRights();
    	$fileid = $table_files->findByPath($this->_projectid1, '/')->id;
		$table_groupstofiles = new USVN_Db_Table_GroupsToFilesRights();
		$table_groupstofiles->insert(array(
			'files_rights_id' 		  => $fileid,
		   'files_rights_is_readable' => true,
		   'files_rights_is_writable' => true,
		   'groups_id'	 			  => $this->_groupid1
		));

		$rights = $file_rights1->findByPath($this->_groupid1, '/trunk/test/tutu/titi');
    	$this->assertTrue($rights['read']);
    	$this->assertTrue($rights['write']);

    	$fileid = $table_files->insert(array(
    		'projects_id'		=> $this->_projectid1,
			'files_rights_path' => '/trunk'
		));
		$table_groupstofiles->insert(array(
			'files_rights_id' 		  => $fileid,
		   'files_rights_is_readable' => true,
		   'files_rights_is_writable' => false,
		   'groups_id'	 			  => $this->_groupid1
		));
		$rights = $file_rights1->findByPath($this->_groupid1, '/trunk/test/tutu/titi');
    	$this->assertTrue($rights['read']);
    	$this->assertFalse($rights['write']);

		$fileid = $table_files->insert(array(
    		'projects_id'		=> $this->_projectid1,
			'files_rights_path' => '/trunk/test/tutu/'
		));
		$table_groupstofiles->insert(array(
			'files_rights_id' 		  => $fileid,
		   'files_rights_is_readable' => true,
		   'files_rights_is_writable' => true,
		   'groups_id'	 			  => $this->_groupid1
		));
		$rights = $file_rights1->findByPath($this->_groupid1, '/trunk/test/tutu/titi');
    	$this->assertTrue($rights['read']);
    	$this->assertTrue($rights['write']);
    }

    public function test_findByPathInheritsTowGroups()
    {
    	$file_right = new USVN_FilesAccessRights($this->_projectid1);
    	$file_right->setRightByPath($this->_groupid1, "/", true, true);
    	$file_right->setRightByPath($this->_groupid1, "/tags", true, false);

    	$rights = $file_right->findByPath($this->_groupid1, "/");
    	$this->assertTrue($rights['read']);
    	$this->assertTrue($rights['write']);

    	$rights = $file_right->findByPath($this->_groupid1, "/branches");
    	$this->assertTrue($rights['read']);
    	$this->assertTrue($rights['write']);

    	$rights = $file_right->findByPath($this->_groupid1, "/tags");
    	$this->assertTrue($rights['read']);
    	$this->assertFalse($rights['write']);

    	$rights = $file_right->findByPath($this->_groupid1, "/tags/tutu");
    	$this->assertTrue($rights['read']);
    	$this->assertFalse($rights['write']);

    	$rights = $file_right->findByPath($this->_groupid2, "/");
    	$this->assertFalse($rights['read']);
    	$this->assertFalse($rights['write']);

    	$file_right->setRightByPath($this->_groupid2, "/", true, true);

    	$rights = $file_right->findByPath($this->_groupid2, "/");
    	$this->assertTrue($rights['read']);
    	$this->assertTrue($rights['write']);

    	$rights = $file_right->findByPath($this->_groupid2, "/branches");
    	$this->assertTrue($rights['read']);
    	$this->assertTrue($rights['write']);

    	$rights = $file_right->findByPath($this->_groupid2, "/tags");
    	$this->assertTrue($rights['read']);
    	$this->assertTrue($rights['write']);

    	$rights = $file_right->findByPath($this->_groupid2, "/tags/tutu");
    	$this->assertTrue($rights['read']);
    	$this->assertTrue($rights['write']);
    }

    public function test_findByPathTowGroupsNoRightsOnSlash()
    {
    	$file_right = new USVN_FilesAccessRights($this->_projectid1);
    	$file_right->setRightByPath($this->_groupid1, "/tags", true, false);

    	$rights = $file_right->findByPath($this->_groupid2, "/branches");
    	$this->assertFalse($rights['read']);
    	$this->assertFalse($rights['write']);
        
        $rights = $file_right->findByPath($this->_groupid2, "/");
    	$this->assertFalse($rights['read']);
    	$this->assertFalse($rights['write']);
    }

    public function test_findByPathSlashSlash()
    {
    	$file_right = new USVN_FilesAccessRights($this->_projectid1);
    	$file_right->setRightByPath($this->_groupid1, "/tags", true, false);

    	$rights = $file_right->findByPath($this->_groupid2, "//");
    	$this->assertFalse($rights['read']);
    	$this->assertFalse($rights['write']);

    	$rights = $file_right->findByPath($this->_groupid2, "/../../../../../../../../../../..");
    	$this->assertFalse($rights['read']);
    	$this->assertFalse($rights['write']);
    }

    public function test_findByPathError()
    {
    	$file_rights1 = new USVN_FilesAccessRights($this->_projectid1);
		try {
			$rights = $file_rights1->findByPath($this->_groupid1, '');
		}
		catch (USVN_Exception $e){
			return;
		}
		$this->fail();
	}

	public function test_setRightByPath()
	{
		$file_rights1 = new USVN_FilesAccessRights($this->_projectid1);
		$file_rights1->setRightByPath($this->_groupid1, '/trunk', true, false);
    	$rights = $file_rights1->findByPath($this->_groupid1, '/trunk');
    	$this->assertTrue($rights['read']);
    	$this->assertFalse($rights['write']);

		$file_rights1->setRightByPath($this->_groupid2, '/trunk', true, true);
    	$rights = $file_rights1->findByPath($this->_groupid2, '/trunk');
    	$this->assertTrue($rights['read']);
    	$this->assertTrue($rights['write']);

		$file_rights1->setRightByPath($this->_groupid1, '/trunk', true, true);
    	$rights = $file_rights1->findByPath($this->_groupid1, '/trunk');
    	$this->assertTrue($rights['read']);
    	$this->assertTrue($rights['write']);
	}
}

// Call USVN_FilesAccesRightsTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "USVN_FilesAccesRightsTest::main") {
    USVN_FilesAccesRightsTest::main();
}
