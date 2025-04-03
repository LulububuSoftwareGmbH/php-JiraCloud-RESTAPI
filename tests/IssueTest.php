<?php

namespace JiraCloud\Test;

use DateInterval;
use DateTime;
use DH\Adf\Node\Block\Document;
use Exception;
use JiraCloud\ADF\AtlassianDocumentFormat;
use PHPUnit\Framework\TestCase;
use JiraCloud\Issue\Comment;
use JiraCloud\Issue\IssueField;
use JiraCloud\Issue\IssueService;
use JiraCloud\Issue\Transition;
use JiraCloud\JiraException;

class IssueTest extends TestCase
{
    /**
     * @test
     * @return void
     */
    public function testIssue()
    {
        $this->markTestIncomplete();
        try {
            $issueService = new IssueService();

            $issue = $issueService->get('TEST-702');

            file_put_contents('jira-issue.json', json_encode($issue, JSON_PRETTY_PRINT));

            print_r($issue->getFields()->versions[0]);
        } catch (Exception $e) {
            $this->fail($e->getMessage());
        }
    }

    /**
     * @test
     * @return string
     */
    public function create_issue() : string
    {
        try {
            $issueField = new IssueField();

            $due = (new DateTime('NOW'))->add(DateInterval::createFromDateString('1 month 5 day'));

            $code =<<<CODE
<?php
\$i = 123;
\$a = ['hello', 'world', ];
var_dump([\$i => \$a]);
CODE;

            $doc = (new Document())
                ->heading(1)            // header level 1, can have child blocks (needs to be closed with `->end()`)
                  ->text('h1')        // simple unstyled text, cannot have child blocks (no `->end()` needed)
                ->end()                 // closes `heading` node
                ->paragraph()           // paragraph, can have child blocks (needs to be closed with `->end()`)
                    ->text('we’re ')    // simple unstyled text
                    ->strong('support') // text node embedding a `strong` mark
                    ->text(' ')         // simple unstyled text
                    ->em('markdown')    // text node embedding a `em` mark
                    ->text('. ')        // simple unstyled text
                    ->underline('like') // text node embedding a `underline` mark
                    ->text(' this.')    // simple unstyled text
                ->end()                 // closes `paragraph` node
                ->heading(2)            // header level 2
                    ->text('h2')        // simple unstyled text
                ->end()                 // closes `heading` node
                ->heading(3)
                    ->text('heading 3')
                ->end()
                ->paragraph()           // paragraph
                    ->text('also support heading.') // simple unstyled text
                ->end()                 // closes `paragraph` node
                ->codeblock('php')
                   ->text($code)
                ->end()
            ;

            $descV3 = new AtlassianDocumentFormat($doc);

            $issueField->setProjectKey('TEST')
                        ->setSummary("something's wrong")
                        ->setAssigneeNameAsString('lesstif')
                        ->setPriorityNameAsString('Highest')
                        ->setIssueTypeAsString('Story')
                        ->setDescription($descV3)
//                        ->addVersionAsArray(['1.0.1', '1.0.3'])
                        //->addComponentsAsArray(['Component-1', 'Component-2'])
                        ->addComponentAsString('Component-1')
//                        ->setDueDateAsDateTime(
//                            (new DateTime('NOW'))->add(DateInterval::createFromDateString('1 month 5 day'))
//                        )
                        //->setDueDateAsString('2022-10-03')
            ;

            $issueService = new IssueService();

            $ret = $issueService->create($issueField);

            //If success, Returns a link to the created issue.
            print_r($ret);

            $issueKey = $ret->{'key'};

            $this->assertNotNull($issueKey);

            return $issueKey;
        } catch (Exception $e) {
            $this->fail('Create Failed : ' . $e->getMessage());
        }
    }

    /**
     * @test
     * @depends create_issue
     */
    public function get_created_issue(string $issueKey) : string
    {
        try {
            $issueService = new IssueService();

            $ret = $issueService->get($issueKey);

            //print_r($ret);
            $issueKey = $ret->{'key'};

            $this->assertNotNull($issueKey);
            $this->assertNotNull($ret->getFields()->summary);
            $this->assertNotNull($ret->getFields()->issuetype);

            return $issueKey;
        } catch (Exception $e) {
            $this->fail('Create Failed : ' . $e->getMessage());
        }
    }

    /**
     * @test
     * @depends get_created_issue
     */
    public function update_issue(string $subTaskIssueKey) :string
    {
        //$this->markTestIncomplete();
        try {
            $issueField = new IssueField(true);
            $doc = (new Document())
                ->heading(1)            // header level 1, can have child blocks (needs to be closed with `->end()`)
                    ->text('title 1')        // simple unstyled text, cannot have child blocks (no `->end()` needed)
                ->end()                 // closes `heading` node
                ->paragraph()           // paragraph, can have child blocks (needs to be closed with `->end()`)
                    ->text('It\'s updated via REST API')    // simple unstyled text
                    ->strong('support') // text node embedding a `strong` mark
                    ->text(' ')         // simple unstyled text
                    ->em('markdown')    // text node embedding a `em` mark
                    ->text('. ')        // simple unstyled text
                    ->underline('like') // text node embedding a `underline` mark
                    ->text(' this.')    // simple unstyled text
                ->end()                 // closes `paragraph` node
            ;

            $descV3 = new AtlassianDocumentFormat($doc);

            $issueField->setAssigneeNameAsString('lesstif')
                //->setPriorityNameAsString('Major')
                ->setIssueTypeAsString('Story')
                ->addLabelAsString('test-label-first')
                ->addLabelAsString('test-label-second')
//                ->addVersionAsString('1.0.1')
//                ->addVersionAsArray(['1.0.2'])
                ->setDescription($descV3);

            $issueService = new IssueService();

            $issueService->update($subTaskIssueKey, $issueField);

            $this->assertNotNull($subTaskIssueKey);

            return $subTaskIssueKey;
        } catch (Exception $e) {
            $this->fail('update Failed : ' . $e->getMessage());
        }
    }

    /**
     * @test
     * @depends update_issue
     */
    public function create_subTask_issue(string $issueKey) :string
    {
        try {
            $issueField = new IssueField();

            $doc = (new Document())
                ->paragraph()           // paragraph, can have child blocks (needs to be closed with `->end()`)
                    ->strong('Subtask') // text node embedding a `strong` mark
                    ->text('- Full description for issue ')    // simple unstyled text
                ->end()                 // closes `paragraph` node
            ;

            $descV3 = new AtlassianDocumentFormat($doc);

            $issueField->setProjectKey('TEST')
                ->setSummary("Subtask - something's wrong")
                ->setAssigneeNameAsString('lesstif')
                //->setPriorityNameAsString('Critical')
                ->setDescription($descV3)
//                ->addVersionAsString('1.0.1')
//                ->addVersionAsString('1.0.3')
                ->setIssueTypeAsString('Sub-task')
                ->setParentKeyOrId($issueKey);

            $issueService = new IssueService();

            $ret = $issueService->create($issueField);

            $subTaskIssueKey = $ret->{'key'};

            $this->assertNotNull($subTaskIssueKey);

            return $subTaskIssueKey;
        } catch (Exception $e) {
            $this->fail('Create Failed : ' . $e->getMessage());
        }
    }

    /**
     * @test
     * @depends create_subTask_issue
     */
    public function get_created_subtask_issue(string $subTaskIssueKey) : string
    {
        try {
            $issueService = new IssueService();

            $ret = $issueService->get($subTaskIssueKey);

            $issueKey = $ret->{'key'};

            $this->assertNotNull($issueKey);
            $this->assertNotNull($ret->getFields()->summary);
            //$this->assertEquals('Sub-task', $ret->fields->issuetype->name);

            return $subTaskIssueKey;
        } catch (Exception $e) {
            $this->fail('Create Failed : ' . $e->getMessage());
        }
    }

    /**
     * @test
     * @depends get_created_subtask_issue
     */
    public function add_attachment_on_subtask_issue(string $subTaskIssueKey) :string
    {
        try {
            $files = [
                'screen_capture_스크린-캡춰.png',
                'bug-description.pdf',
                'README.md',
            ];

            $issueService = new IssueService();

            // $ret is Array of JiraCloud\Issue\Attachment
            $ret = $issueService->addAttachments($subTaskIssueKey, $files);

            $this->assertNotNull($subTaskIssueKey);
            $this->assertSameSize($files, $ret);

            return $subTaskIssueKey;
        } catch (Exception $e) {
            $this->fail('Attach Failed : ' . $e->getMessage());
        }
    }

    /**
     * @test
     * @depends add_attachment_on_subtask_issue
     */
    public function change_assignee(string $subTaskIssueKey) :string
    {
        try {
            $issueService = new IssueService();

            $ret = $issueService->changeAssigneeByAccountId($subTaskIssueKey, 'replace:by_account_id');

            print_r($ret);

            $this->assertNotNull($subTaskIssueKey);

            return $subTaskIssueKey;
        } catch (Exception $e) {
            $this->fail('Change assignee failed : ' . $e->getMessage());
        }
    }

    /**
     * @test
     * @depends change_assignee
     */
    public function delete_issue(string $subTaskIssueKey) :string
    {
        $this->markTestSkipped();

        try {
            $issueService = new IssueService();

            $ret = $issueService->deleteIssue($subTaskIssueKey);

            print_r($ret);

            return $subTaskIssueKey;
        } catch (Exception $e) {
            $this->fail('delete issue failed : '.$e->getMessage());
        }
    }

    /**
     * @test
     * @depends change_assignee
     */
    public function add_comments(string $subTaskIssueKey) :string
    {
        //$this->markTestIncomplete();
        try {
            $comment = new Comment();

            $comment = new Comment();

            $code =<<<CODE
<?php
\$i = 123;
\$a = ['hello', 'world', ];
var_dump([\$i => \$a]);
CODE;

            $doc = (new Document())
                ->heading(1)            // header level 1, can have child blocks (needs to be closed with `->end()`)
                ->text('h1')        // simple unstyled text, cannot have child blocks (no `->end()` needed)
                ->end()                 // closes `heading` node
                ->paragraph()           // paragraph, can have child blocks (needs to be closed with `->end()`)
                ->text('we’re ')    // simple unstyled text
                ->strong('support') // text node embedding a `strong` mark
                ->text(' ')         // simple unstyled text
                ->em('markdown')    // text node embedding a `em` mark
                ->text('. ')        // simple unstyled text
                ->underline('like') // text node embedding a `underline` mark
                ->text(' this.')    // simple unstyled text
                ->text(' date=' . date("Y-m-d H:i:s"))
                ->end()                 // closes `paragraph` node
                ->heading(2)            // header level 2
                ->text('h2')        // simple unstyled text
                ->end()                 // closes `heading` node
                ->heading(3)
                ->text('heading 3')
                ->end()
                ->paragraph()           // paragraph
                ->text('also support heading.') // simple unstyled text
                ->end()                 // closes `paragraph` node
                ->codeblock('php')
                ->text($code)
                ->end()
            ;

            $comment->setBodyByAtlassianDocumentFormat($doc)
                //    ->setVisibilityAsString('role', 'Users')
            ;

            $issueService = new IssueService();
            $ret = $issueService->addComment($subTaskIssueKey, $comment);

            print_r($ret);
            $this->assertNotNull($ret);

            return $subTaskIssueKey;
        } catch (Exception $e) {
            $this->fail('add Comment Failed : ' . $e->getMessage());
        }
    }

    /**
     * @test
     * @depends add_comments
     */
    public function transition_issue(string $subTaskIssueKey) : string
    {
        try {
            $transition = new Transition();
            $transition->setTransitionName('In Progress');

            $doc = (new Document())
                ->paragraph()           // paragraph, can have child blocks (needs to be closed with `->end()`)
                    ->text('Issue ')    // simple unstyled text
                    ->strong(' status') // text node embedding a `strong` mark
                    ->text(' ')         // simple unstyled text
                    ->text(' changed ')    // text node embedding a `em` mark
                    ->text('. ')        // simple unstyled text
                    ->underline('by') // text node embedding a `underline` mark
                    ->em(' REST API.')    // simple unstyled text
                ->end()                 // closes `paragraph` node
            ;

            $comment = new AtlassianDocumentFormat($doc);

            $transition->setCommentBody($comment);

            $issueService = new IssueService();

            $ret = $issueService->transition($subTaskIssueKey, $transition);

            $this->assertNotNull($ret);

            return $subTaskIssueKey;
        } catch (Exception $e) {
            $this->fail('testTransition Failed : ' . $e->getMessage());
        }
    }

    /**
     * @test
     * @depends transition_issue
     */
    public function issue_search()
    {
        $jql = 'project not in (TEST)  and assignee = currentUser() and status in (Resolved, closed)';
        try {
            $issueService = new IssueService();

            $ret = $issueService->search($jql);
            $this->assertNotNull($ret);

            // Dumper::dump($ret);
        } catch (Exception $e) {
            $this->fail('testSearch Failed : ' . $e->getMessage());
        }
    }

    /**
     * @test
     * @depends issue_search
     */
    public function testCustomField()
    {
        $jql = 'project not in (TEST)  and assignee = currentUser() and status in (Resolved, closed)';
        try {
            $issueService = new IssueService();

            $ret = $issueService->search($jql);
            $this->assertNotNull($ret);

            //Dumper::dump($ret);
        } catch (Exception $e) {
            $this->fail('testSearch Failed : ' . $e->getMessage());
        }
    }
}
