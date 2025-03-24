<?php

//  _        _      _         _
// | |  _  _| |_  _| |__ _  _| |__ _  _
// | |_| || | | || | '_ \ || | '_ \ || |
// |____\_,_|_|\_,_|_.__/\_,_|_.__/\_,_|
//
// Copyright © Lulububu Software GmbH - All Rights Reserved
// https://lulububu.de
//
// Unauthorized copying of this file, via any medium is strictly prohibited!
// Proprietary and confidential.

namespace JiraCloud\Changelog;

use JsonSerializable;

/**
 * Class ChangelogItem
 *
 * @author  Philippos Tiropoulos <philippos@lulububu.de>
 *
 * @package JiraCloud\Changelog
 */
class ChangelogItem implements JsonSerializable
{
    protected string $field;
    protected ?string $fieldtype = null;
    protected ?string $from = null;
    protected ?string $fromString = null;
    protected ?string $to = null;
    protected ?string $toString = null;

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

