<?php

namespace JiraCloud\Changelog;

use JsonSerializable;

class ChangelogItem implements JsonSerializable
{
    public function __construct(
        protected string  $field,
        protected ?string $fieldtype = null,
        protected ?string $fieldId = null,
        protected ?string $from = null,
        protected ?string $fromString = null,
        protected ?string $to = null,
        protected ?string $toString = null,
    )
    {
    }

    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return array_filter(
            get_object_vars($this),
            fn($variable) => !is_null($variable)
        );
    }

    public function setField(string $field): void
    {
        $this->field = $field;
    }

    public function setFieldtype(?string $fieldtype): void
    {
        $this->fieldtype = $fieldtype;
    }

    public function setFieldId(?string $fieldId): void
    {
        $this->fieldId = $fieldId;
    }

    public function setFrom(string $from): void
    {
        $this->from = $from;
    }

    public function setFromString(?string $fromString): void
    {
        $this->fromString = $fromString;
    }

    public function setTo(?string $to): void
    {
        $this->to = $to;
    }

    public function setToString(?string $toString): void
    {
        $this->toString = $toString;
    }

    public function getField(): string
    {
        return $this->field;
    }

    public function getFieldtype(): ?string
    {
        return $this->fieldtype;
    }

    public function getFrom(): ?string
    {
        return $this->from;
    }

    public function getFromString(): ?string
    {
        return $this->fromString;
    }

    public function getTo(): ?string
    {
        return $this->to;
    }

    public function getToString(): ?string
    {
        return $this->toString;
    }
}

