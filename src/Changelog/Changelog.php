<?php

namespace JiraCloud\Changelog;

use JsonMapper;
use JsonSerializable;

class Changelog implements JsonSerializable
{
    /**
     * @param string $id
     * @param ChangelogItem[] $items
     */
    public function __construct(
        protected string $id,
        protected array  $items = [],
    )
    {
    }

    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return array_filter(get_object_vars($this));
    }

    /**
     * @return ChangelogItem[]
     */
    public function getItems(): array
    {
        return $this->items;
    }

    public function setItems(array $items): void
    {
        $this->items = [];
        $mapper      = new JsonMapper();

        foreach ($items as $item) {
            if ($item instanceof ChangelogItem) {
                $this->items[] = $item;
            } else if (is_object($item)) {
                $mapped = new ChangelogItem('');

                $mapper->map($item, $mapped);

                $this->items[] = $mapped;
            }
        }
    }
}

