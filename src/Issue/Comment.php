<?php

namespace JiraCloud\Issue;

use DateTimeInterface;
use InvalidArgumentException;
use JiraCloud\ADF\AtlassianDocumentFormat;

class Comment implements \JsonSerializable
{
    use VisibilityTrait;

    public string $self;

    public string $id;

    public Reporter $author;

    public AtlassianDocumentFormat|null $body = null;

    public Reporter $updateAuthor;

    public ?DateTimeInterface $created;

    public ?DateTimeInterface $updated;

    public ?Visibility $visibility = null;

    public bool $jsdPublic;

    public string $renderedBody;

    /**
     * Comment constructor.
     */
    public function __construct(
        string                   $self = '',
        string                   $id = '',
        ?Reporter                $author = null,
        ?AtlassianDocumentFormat $body = null,
        ?Reporter                $updateAuthor = null,
        ?DateTimeInterface       $created = null,
        ?DateTimeInterface       $updated = null,
        ?Visibility              $visibility = null,
        bool                     $jsdPublic = false,
        string                   $renderedBody = ''
    )
    {
        $this->self         = $self;
        $this->id           = $id;
        $this->author       = $author ?? new Reporter();
        $this->body         = $body;
        $this->updateAuthor = $updateAuthor ?? new Reporter();
        $this->created      = $created;
        $this->updated      = $updated;
        $this->visibility   = $visibility;
        $this->jsdPublic    = $jsdPublic;
        $this->renderedBody = $renderedBody;
    }

    public function getAuthor(): ?Reporter
    {
        return $this->author;
    }

    public function getAuthorAccountId(): ?string
    {
        return $this->author->accountId;
    }

    /**
     * mapping function for json_mapper.
     *
     * @param string $body
     *
     * @return $this
     */
    public function setBody(mixed $body): static
    {
        if ($body instanceof AtlassianDocumentFormat) {
            $this->body = $body;
        } else if (is_array($body)) {
            $this->body = AtlassianDocumentFormat::fromArray($body);
        } else if ($body instanceof \stdClass) {
            $this->body = AtlassianDocumentFormat::fromArray((array)$body);
        } else {
            throw new InvalidArgumentException('Unsupported type for comment body');
        }

        return $this;
    }

    #[\ReturnTypeWillChange]
    public function jsonSerialize(): array
    {
        return array_filter(get_object_vars($this), function ($var) {
            return !is_null($var);
        });
    }

    public static function builder(): CommentBuilder
    {
        return new CommentBuilder();
    }
}
