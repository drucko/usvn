<?php
/**
 * Send an email after each commit
 *
 * @author Team USVN <contact@usvn.info>
 * @link http://www.usvn.info
 * @license http://www.cecill.info/licences/Licence_CeCILL_V2-en.txt CeCILL V2
 * @copyright Copyright 2007, Team USVN
 * @since 0.8
 * @package usvn
 * @subpackage hooks
 *
 * This software has been written at EPITECH <http://www.epitech.net>
 * EPITECH, European Institute of Technology, Paris - FRANCE -
 * This project has been realised as part of
 * end of studies project.
 *
 * $Id$
 */

// Call NotifByMailTest:main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
    define("PHPUnit_MAIN_METHOD", "NotifByMailTest::main");
}

require_once "PHPUnit/Framework/TestCase.php";
require_once "PHPUnit/Framework/TestSuite.php";

require_once 'www/USVN/autoload.php';
require_once 'www/hooks/NotifByMail.php';

class FakeSendMail extends Zend_Mail_Transport_Sendmail
{
	public function _sendMail()
	{
	}

	public function getSubject()
	{
		return $this->_mail->getSubject();
	}

	public function getBodyText()
	{
		return $this->_mail->getBodyText(true);
	}

	public function getFrom()
	{
		return $this->_mail->getFrom();
	}

	public function getTo()
	{
		return $this->_mail->getRecipients();
	}
}


/**
 * Test class for NotifByMail.
 * Generated by PHPUnit_Util_Skeleton on 2007-03-26 at 17:42:45.
 */
class NotifByMailTest extends USVN_Test_DB {
	private $project = "testrepos";
	private $repos;
	private $co = "tests/checkout";
	private $sendmail;

    /**
     * Runs the test methods of this class.
     *
     * @access public
     * @static
     */
    public static function main() {
        require_once "PHPUnit/TextUI/TestRunner.php";

        $suite  = new PHPUnit_Framework_TestSuite("NotifByMailTest");
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

	public function setUp()
	{
		parent::setUp();
		$config = Zend_Registry::get('config');
		$this->repos = $config->subversion->path . DIRECTORY_SEPARATOR . "svn";

		$table = new USVN_Db_Table_Users();
		$user1 = $table->fetchNew();
		$user1->setFromArray(array('users_login' 	=> 'test',
																'users_password' 	=> 'password',
																'users_firstname' 	=> 'firstname',
																'users_lastname' 	=> 'lastname',
																'users_email' 		=> 'email@email.fr'));
		$user1->save();
		$user2 = $table->fetchNew();
		$user2->setFromArray(array('users_login' 	=> 'test2',
																'users_password' 	=> 'password',
																'users_firstname' 	=> 'firstname',
																'users_lastname' 	=> 'lastname',
																'users_email' 		=> 'email2@email.fr'));
		$user2->save();
		$user3 = $table->fetchNew();
		$user3->setFromArray(array('users_login' 	=> 'test3',
																'users_password' 	=> 'password',
																'users_firstname' 	=> 'firstname',
																'users_lastname' 	=> 'lastname',
																'users_email' 		=> ''));
		$user3->save();


		$this->groups = new USVN_Db_Table_Groups();
		$this->groups->insert(
			array(
				"groups_id" => 42,
				"groups_name" => "test",
				"groups_description" => "test"
			)
		);
		$group = $this->groups->find(42)->current();
		$group->addUser($user1);
		$group->addUser($user2);
		$group->addUser($user3);

		USVN_Project::createProject(array('projects_name'  => $this->project), "test", true, false, false);


		$projects = new USVN_Db_Table_Projects();
		$project = $projects->findByName($this->project);
		$project->addGroup($group);


		USVN_DirectoryUtils::removeDirectory($this->repos . DIRECTORY_SEPARATOR . $this->project . DIRECTORY_SEPARATOR . 'hooks');
		USVN_SVNUtils::checkoutSvn($this->repos . DIRECTORY_SEPARATOR . $this->project, $this->co);
		$path = getcwd();
		chdir($this->co);
		mkdir('testdir');
		`svn add testdir`;
		`svn commit --non-interactive -m Test`;
		chdir($path);

		$this->sendmail = new FakeSendMail();
		Zend_Mail::setDefaultTransport($this->sendmail);
	}

	public function testpostCommit()
	{
		$h = new HookNotifByMail();
		$h->postCommit($this->repos . DIRECTORY_SEPARATOR  . $this->project, 1);
		$this->assertEquals("[{$this->project}] Revision 1", $this->sendmail->getSubject());
		$this->assertEquals("Project: {$this->project}=0ARevision: 1=0A", $this->sendmail->getBodyText());
		$this->assertEquals('nobody@usvn.info', $this->sendmail->getFrom());
		$this->assertEquals(2, count($this->sendmail->getTo()));
		$this->assertEquals(array('email@email.fr', 'email2@email.fr'), $this->sendmail->getTo());
	}
}

// Call USVN_ConfigTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "NotifByMailTest::main") {
    NotifByMailTest::main();
}