<?php

namespace JiraCloud\Test;

use DH\Adf\Node\Block\Document;
use JiraCloud\ADF\AtlassianDocumentFormat;
use PHPUnit\Framework\TestCase;

class AtlassianDocumentFormatTest extends TestCase
{
    public function testFromArrayWithSupportedNodes()
    {
        $adf = [
            'version' => 1,
            'type' => 'doc',
            'content' => [
                [
                    'type' => 'paragraph',
                    'content' => [
                        [
                            'type' => 'text',
                            'text' => 'Hello world',
                        ],
                    ],
                ],
            ],
        ];

        $result = AtlassianDocumentFormat::fromArray($adf);

        $this->assertInstanceOf(AtlassianDocumentFormat::class, $result);
        $this->assertInstanceOf(Document::class, $result->getDocument());

        $serialized = $result->jsonSerialize();
        $this->assertEquals('doc', $serialized['type']);
        $this->assertCount(1, $serialized['content']);
    }

    public function testFromArrayFiltersUnsupportedTopLevelNodes()
    {
        $adf = [
            'version' => 1,
            'type' => 'doc',
            'content' => [
                [
                    'type' => 'paragraph',
                    'content' => [
                        [
                            'type' => 'text',
                            'text' => 'Before media',
                        ],
                    ],
                ],
                [
                    'type' => 'mediaInline',
                    'attrs' => [
                        'id' => 'abc-123',
                        'collection' => 'some-collection',
                    ],
                ],
                [
                    'type' => 'paragraph',
                    'content' => [
                        [
                            'type' => 'text',
                            'text' => 'After media',
                        ],
                    ],
                ],
            ],
        ];

        $result = AtlassianDocumentFormat::fromArray($adf);

        $this->assertInstanceOf(AtlassianDocumentFormat::class, $result);

        $serialized = json_decode(json_encode($result->jsonSerialize()), true);
        $this->assertCount(2, $serialized['content']);
        $this->assertEquals('paragraph', $serialized['content'][0]['type']);
        $this->assertEquals('paragraph', $serialized['content'][1]['type']);
    }

    public function testFromArrayFiltersUnsupportedNestedNodes()
    {
        $adf = [
            'version' => 1,
            'type' => 'doc',
            'content' => [
                [
                    'type' => 'paragraph',
                    'content' => [
                        [
                            'type' => 'text',
                            'text' => 'Some text',
                        ],
                        [
                            'type' => 'mediaInline',
                            'attrs' => [
                                'id' => 'abc-123',
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $result = AtlassianDocumentFormat::fromArray($adf);

        $this->assertInstanceOf(AtlassianDocumentFormat::class, $result);

        $serialized = json_decode(json_encode($result->jsonSerialize()), true);
        $this->assertCount(1, $serialized['content']);

        $paragraph = $serialized['content'][0];
        $this->assertEquals('paragraph', $paragraph['type']);
        $this->assertCount(1, $paragraph['content']);
        $this->assertEquals('text', $paragraph['content'][0]['type']);
    }

    public function testFromArrayWithOnlyUnsupportedNodes()
    {
        $adf = [
            'version' => 1,
            'type' => 'doc',
            'content' => [
                [
                    'type' => 'unknownNodeType',
                    'content' => [],
                ],
                [
                    'type' => 'anotherUnknownType',
                    'attrs' => ['foo' => 'bar'],
                ],
            ],
        ];

        $result = AtlassianDocumentFormat::fromArray($adf);

        $this->assertInstanceOf(AtlassianDocumentFormat::class, $result);

        $serialized = $result->jsonSerialize();
        $this->assertCount(0, $serialized['content']);
    }

    public function testFromArrayWithEmptyContent()
    {
        $adf = [
            'version' => 1,
            'type' => 'doc',
            'content' => [],
        ];

        $result = AtlassianDocumentFormat::fromArray($adf);

        $this->assertInstanceOf(AtlassianDocumentFormat::class, $result);

        $serialized = $result->jsonSerialize();
        $this->assertCount(0, $serialized['content']);
    }

    public function testFromArrayPreservesTaskListAndTaskItemAttrs()
    {
        $adf = [
            'version' => 1,
            'type' => 'doc',
            'content' => [
                [
                    'type' => 'taskList',
                    'attrs' => ['localId' => 'list-1'],
                    'content' => [
                        [
                            'type' => 'taskItem',
                            'attrs' => ['localId' => 'item-1', 'state' => 'TODO'],
                            'content' => [
                                ['type' => 'text', 'text' => 'Do the thing'],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $serialized = AtlassianDocumentFormat::fromArray($adf)->jsonSerialize();

        $taskList = $serialized['content'][0];
        $this->assertSame('list-1', $taskList['attrs']['localId']);

        $taskItem = $taskList['content'][0];
        $this->assertSame('item-1', $taskItem['attrs']['localId']);
        $this->assertSame('TODO', $taskItem['attrs']['state']);
    }
}
