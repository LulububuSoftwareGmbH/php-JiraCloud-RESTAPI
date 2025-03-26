<?php

namespace JiraCloud\Test;

use JiraCloud\Issue\Issue;
use JiraCloud\Issue\IssueService;
use PHPUnit\Framework\TestCase;

class IssueFromJsonTest extends TestCase
{
    public function testGetIssueFromJsonFile()
    {
        $issueService = new IssueService();
        $jsonPath     = __DIR__ . '/../test-data/jira_issue_v3.json';
        $json         = json_decode(file_get_contents($jsonPath));
        $issue        = $issueService->getIssueFromJSON($json);

        $this->assertInstanceOf(
            Issue::class,
            $issue
        );
        $this->assertEquals(
            'ROBOTHOMAS-1015',
            $issue->key
        );
        $this->assertNotEmpty($issue->fields->summary);

        $commentBodyDocument = $issue->fields->comment->comments[0]->body;
        $documentContent     = $commentBodyDocument->jsonSerialize()['content'] ?? [];
        $firstParagraph      = $documentContent[0] ?? null;
        $firstTextNode       = $firstParagraph->getContent()[0] ?? null;
        $text                = $firstTextNode->getText() ?? null;

        $this->assertEquals(
            'Dieses Ticket ist jetzt im Backlog, da der Epic (',
            $text,
            'first comment in adf format doesnt match'
        );
    }
}
