<?php

namespace JiraCloud\ADF;

use DH\Adf\Node\Block\Document;
use DH\Adf\Node\Node;
use JiraCloud\Issue\CommentBuilder;

/**
 * Class AtlassianDocumentFormat.
 */
class AtlassianDocumentFormat implements \JsonSerializable
{
    public array $type;

    public array $content;

    public string $version;

    private Document|Node|null $document = null;

    public function __construct(Document|Node|string|null $document)
    {
        if (is_string($document)) {
            $document = CommentBuilder::createDocument([
                self::createParagraph($document),
            ]);
        }

        $this->document = $document;
    }

    #[\ReturnTypeWillChange]
    public function jsonSerialize(): array
    {
        return $this->document->jsonSerialize();
    }

    public function setDocument(Document|Node|null $document)
    {
        $this->document = $document;
    }

    public function getDocument(): Document|Node|null
    {
        return $this->document;
    }

    public static function fromArray(array $adf): self
    {
        $adf = self::toArrayRecursive($adf);

        /** @var Document $document */
        $document = Document::load($adf);

        return new self($document);
    }

    private static function toArrayRecursive(mixed $data): mixed
    {
        if (is_object($data)) {
            $data = (array)$data;
        }

        if (is_array($data)) {
            return array_map([self::class, 'toArrayRecursive'], $data);
        }

        return $data;
    }

    public static function createText(string $text, array $marks = []): array
    {
        $textNode = [
            'type' => 'text',
            'text' => $text,
        ];

        if (!empty($marks)) {
            $textNode['marks'] = array_map(function ($mark) {
                if (is_string($mark)) {
                    return ['type' => $mark];
                } elseif (is_array($mark)) {
                    if (isset($mark['type'])) {
                        return $mark;
                    }

                    if (isset($mark['textColor'])) {
                        return [
                            'type' => 'textColor',
                            'attrs' => ['color' => $mark['textColor']],
                        ];
                    }

                    if (isset($mark['alignment'])) {
                        return [
                            'type' => 'alignment',
                            'attrs' => ['align' => $mark['alignment']],
                        ];
                    }
                }

                return $mark;
            }, $marks);
        }

        return $textNode;
    }

    public static function createParagraph(string|self $text, array $marks = []): array
    {
        if ($text instanceof self) {
            $inlineContent = [];
            $serialized = json_decode(json_encode($text->jsonSerialize()), true);

            foreach ($serialized['content'] ?? [] as $blockNode) {
                foreach ($blockNode['content'] ?? [] as $inlineNode) {
                    $inlineContent[] = $inlineNode;
                }
            }

            return [
                'type' => 'paragraph',
                'content' => $inlineContent,
            ];
        }

        return [
            'type' => 'paragraph',
            'content' => [
                self::createText($text, $marks),
            ],
        ];
    }

    public static function createParagraphWithContent(array $content): array
    {
        return [
            'type' => 'paragraph',
            'content' => $content,
        ];
    }

    public static function createHeading(string $text, int $level, array $marks = []): array
    {
        return [
            'type' => 'heading',
            'attrs' => [
                'level' => max(1, min(6, $level)),
            ],
            'content' => [
                self::createText($text, $marks),
            ],
        ];
    }

    public static function createLink(string $text, string $url): array
    {
        return [
            'type' => 'text',
            'text' => $text,
            'marks' => [
                [
                    'type' => 'link',
                    'attrs' => [
                        'href' => $url,
                    ],
                ],
            ],
        ];
    }

    public static function createListItem(array $content): array
    {
        return [
            'type' => 'listItem',
            'content' => $content,
        ];
    }

    public static function createBulletList(array $items): array
    {
        $listItems = [];
        foreach ($items as $item) {
            if (is_string($item)) {
                $listItems[] = self::createListItem([
                    self::createParagraph($item),
                ]);
            } elseif (is_array($item)) {
                $listItems[] = self::createListItem($item);
            }
        }

        return [
            'type' => 'bulletList',
            'content' => $listItems,
        ];
    }

    public static function createTableHeader(string $text, array $marks = []): array
    {
        return [
            'type' => 'tableHeader',
            'content' => [
                self::createParagraph($text, $marks),
            ],
        ];
    }

    public static function createTableCell(string $text, array $marks = [], array $attrs = []): array
    {
        $cell = [
            'type' => 'tableCell',
            'content' => [
                self::createParagraph($text, $marks),
            ],
        ];

        if (!empty($attrs)) {
            $cell['attrs'] = $attrs;
        }

        return $cell;
    }

    public static function createTableCellWithContent(array $content, array $attrs = []): array
    {
        $cell = [
            'type' => 'tableCell',
            'content' => [
                [
                    'type'    => 'paragraph',
                    'content' => $content,
                ],
            ],
        ];

        if (!empty($attrs)) {
            $cell['attrs'] = $attrs;
        }

        return $cell;
    }


    public static function createTableRow(array $cells): array
    {
        return [
            'type' => 'tableRow',
            'content' => $cells,
        ];
    }

    public static function createTable(array $rows, array $attrs = []): array
    {
        $defaultAttrs = [
            'isNumberColumnEnabled' => false,
            'layout' => 'default',
            'width' => 760,
        ];

        return [
            'type' => 'table',
            'attrs' => array_merge($defaultAttrs, $attrs),
            'content' => $rows,
        ];
    }

    public static function fromJsonString(string $json): self
    {
        $adf = json_decode($json, true);
        return self::fromArray($adf);
    }

    public function toJsonString(): string
    {
        return json_encode($this->jsonSerialize());
    }
}
