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

use JsonMapper;
use JsonSerializable;

/**
 * Class Changelog
 *
 * @author  Philippos Tiropoulos <philippos@lulububu.de>
 *
 * @package JiraCloud\Changelog
 */
class Changelog implements JsonSerializable
{
    protected string $id;

    /**
     * @var ChangelogItem[]
     */
    protected array $items = [];

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
                $mapped = new ChangelogItem();

                $mapper->map($item, $mapped);

                $this->items[] = $mapped;
            }
        }
    }
}

