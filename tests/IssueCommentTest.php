<?php

namespace JiraCloud\Test;

use InvalidArgumentException;
use JiraCloud\ADF\AtlassianDocumentFormat;
use JiraCloud\Issue\Comment;
use PHPUnit\Framework\TestCase;

class IssueCommentTest extends TestCase
{
    public function testSetBodyWithADFInstance(): void
    {
        $adf     = AtlassianDocumentFormat::fromArray($this->getValidADFArray());
        $comment = new Comment();

        $comment->setBody($adf);
        $this->assertSame(
            $adf,
            $comment->body
        );
    }

    public function testSetBodyWithArray(): void
    {
        $comment = new Comment();

        $comment->setBody($this->getValidADFArray());
        $this->assertInstanceOf(
            AtlassianDocumentFormat::class,
            $comment->body
        );
    }

    public function testSetBodyWithStdClass(): void
    {
        $std     = json_decode(
            json_encode(
                $this->getValidADFArray()
            )
        );
        $comment = new Comment();

        $comment->setBody($std);
        $this->assertInstanceOf(
            AtlassianDocumentFormat::class,
            $comment->body
        );
    }

    public function testSetBodyWithString(): void
    {
        $comment = new Comment();

        $comment->setBody("This is a plain text comment");
        $this->assertInstanceOf(
            AtlassianDocumentFormat::class,
            $comment->body
        );

        $document      = $comment->body->getDocument();
        $paragraph     = $document->getContent()[0];
        $inlineContent = $paragraph->getContent()[0];

        $this->assertEquals(
            "This is a plain text comment",
            $inlineContent->getText(),
        );
    }

    public function testSetBodyWithNullThrows(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $comment = new Comment();

        $comment->setBody(null);
    }

    public function testSetBodyWithIntThrows(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $comment = new Comment();

        $comment->setBody(42);
    }

    private function getValidADFArray(): array
    {
        return [
            'type'    => 'doc',
            'version' => 1,
            'content' => [
                [
                    'type'    => 'paragraph',
                    'content' => [
                        ['type' => 'text', 'text' => 'Hello World'],
                    ],
                ],
            ],
        ];
    }
}
