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

namespace JiraCloud\Test;

use JiraCloud\Changelog\Changelog;
use JsonMapper;
use PHPUnit\Framework\TestCase;

/**
 * Class ChangelogTest
 *
 * @author  Philippos Tiropoulos <philippos@lulububu.de>
 *
 * @package JiraCloud\Test
 */
class ChangelogTest extends TestCase
{
    public function testChangelogMapping()
    {
        $json = <<<JSON
{
    "id": "10124",
    "items": [
        {
            "field": "summary",
            "fromString": "Old summary",
            "toString": "New summary"
        }
    ]
}
JSON;

        $data          = json_decode($json);
        $mapper        = new JsonMapper();
        $changelog     = $mapper->map($data, new Changelog());
        $changeLogItem = $changelog->getItems()[0];

        $this->assertEquals('summary', $changeLogItem->getField());
        $this->assertEquals('Old summary', $changeLogItem->getFromString());
    }
}

