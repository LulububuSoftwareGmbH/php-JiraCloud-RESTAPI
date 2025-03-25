<?php

namespace JiraCloud\Test;

use DateTime;
use DateTimeInterface;
use JiraCloud\Changelog\Changelog;
use JiraCloud\Issue\Comment;
use JiraCloud\Issue\Issue;
use JiraCloud\Issue\IssueService;
use JiraCloud\User\User;
use JsonMapper;
use PHPUnit\Framework\TestCase;

/**
 * Class WebhookMappingTest
 */
class WebhookMappingTest extends TestCase
{
    public function testWebhookPayloadMapping()
    {
        $json = file_get_contents(__DIR__ . '/../test-data/jira-issue-closed.json');
        $data = json_decode($json);

        $mapper                                            = new JsonMapper();
        $mapper->classMap['\\' . DateTimeInterface::class] = DateTime::class;

        $this->assertEquals(
            1741176177731,
            $data->timestamp
        );
        $this->assertEquals(
            'jira:issue_updated',
            $data->webhookEvent
        );

        $user = $mapper->map($data->user, new User());

        $this->assertInstanceOf(
            User::class,
            $user
        );
        $this->assertEquals(
            '557058:0459787c-4689-4c30-9570-b5eca6591759',
            $user->accountId
        );

        $issueService = new IssueService();
        $issue        = $issueService->getIssueFromJSON($data->issue);

        $this->assertInstanceOf(
            Issue::class,
            $issue
        );
        $this->assertEquals(
            'TEST-5672',
            $issue->getKey()
        );

        $changelog = $mapper->map(
            $data->changelog,
            new Changelog('')
        );

        $this->assertInstanceOf(
            Changelog::class,
            $changelog
        );
        $this->assertCount(
            2,
            $changelog->getItems()
        );
        $this->assertEquals(
            'resolution',
            $changelog->getItems()[0]->getField()
        );

        if (isset($data->comment)) {
            $comment = $mapper->map($data->comment, new Comment());

            $this->assertInstanceOf(
                Comment::class,
                $comment
            );
            $this->assertNotEmpty($comment->body);
        } else {
            $this->assertTrue(
                true,
                'No comment in this webhook – skipping.'
            );
        }
    }
}
