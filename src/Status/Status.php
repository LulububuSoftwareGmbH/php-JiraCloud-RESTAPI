<?php

namespace JiraCloud\Status;

class Status implements \JsonSerializable
{
    /** @var int */
    public $id;

    /** @var string */
    public $name;

    /** @var string */
    public $untranslatedName;

    /** @var string */
    public $description;

    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return array_filter(get_object_vars($this));
    }
}
