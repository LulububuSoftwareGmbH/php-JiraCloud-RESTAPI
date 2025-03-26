<?php

namespace JiraCloud\Test;

use JiraCloud\Changelog\Changelog;
use JsonMapper;
use PHPUnit\Framework\TestCase;

/**
 * Class ChangelogTest
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
        $changelog     = $mapper->map($data, new Changelog(''));
        $changeLogItem = $changelog->getItems()[0];

        $this->assertEquals('summary', $changeLogItem->getField());
        $this->assertEquals('Old summary', $changeLogItem->getFromString());
    }
}

