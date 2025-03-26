<?php

namespace JiraCloud\ADF;

use DH\Adf\Node\Block\CodeBlock;
use DH\Adf\Node\Block\Document;
use DH\Adf\Node\Block\Paragraph;
use DH\Adf\Node\Node;

/**
 * Class AtlassianDocumentFormat.
 */
class AtlassianDocumentFormat implements \JsonSerializable
{
    public array $type;

    public array $content;

    public string $version;

    private ?Document $document = null;

    public function __construct(Document|Node $document)
    {
        $this->document = $document;
    }

    #[\ReturnTypeWillChange]
    public function jsonSerialize(): array
    {
        return $this->document->jsonSerialize();
    }

    public function setDocument(Document|Node $document)
    {
        $this->document = $document;
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
}
