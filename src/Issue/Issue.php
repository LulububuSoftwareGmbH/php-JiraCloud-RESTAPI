<?php

namespace JiraCloud\Issue;

class Issue implements \JsonSerializable
{
    /**
     * return only if Project query by key(not id).
     */
    protected ?string $expand;

    protected string $self;

    protected string $id;

    protected string $key;

    protected IssueField $fields;

    protected ?array $renderedFields;

    protected ?array $names;

    protected ?array $schema;

    protected ?array $transitions;

    protected ?array $operations;

    protected ?array $editmeta;

    protected ?ChangeLog $changelog;

    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return array_filter(get_object_vars($this));
    }

    public function getFields(): ?IssueField
    {
        return $this->fields;
    }

    public function getStatus(): IssueStatus
    {
        return $this->fields->status;
    }

    public function getStatusId(): string
    {
        return $this->fields->status->id;
    }

    public function getExpand(): ?string
    {
        return $this->expand;
    }

    public function getSelf(): string
    {
        return $this->self;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getKey(): string
    {
        return $this->key;
    }

    public function getRenderedFields(): ?array
    {
        return $this->renderedFields;
    }

    public function getNames(): ?array
    {
        return $this->names;
    }

    public function getSchema(): ?array
    {
        return $this->schema;
    }

    public function getTransitions(): ?array
    {
        return $this->transitions;
    }

    public function getOperations(): ?array
    {
        return $this->operations;
    }

    public function getEditmeta(): ?array
    {
        return $this->editmeta;
    }

    public function getChangelog(): ?ChangeLog
    {
        return $this->changelog;
    }

    public function setExpand(?string $expand): void
    {
        $this->expand = $expand;
    }

    public function setSelf(string $self): void
    {
        $this->self = $self;
    }

    public function setId(string $id): void
    {
        $this->id = $id;
    }

    public function setKey(string $key): void
    {
        $this->key = $key;
    }

    public function setFields(IssueField $fields): void
    {
        $this->fields = $fields;
    }

    public function setRenderedFields(?array $renderedFields): void
    {
        $this->renderedFields = $renderedFields;
    }

    public function setNames(?array $names): void
    {
        $this->names = $names;
    }

    public function setSchema(?array $schema): void
    {
        $this->schema = $schema;
    }

    public function setTransitions(?array $transitions): void
    {
        $this->transitions = $transitions;
    }

    public function setOperations(?array $operations): void
    {
        $this->operations = $operations;
    }

    public function setEditmeta(?array $editmeta): void
    {
        $this->editmeta = $editmeta;
    }

    public function setChangelog(?ChangeLog $changelog): void
    {
        $this->changelog = $changelog;
    }
}
