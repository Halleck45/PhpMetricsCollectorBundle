<?php
namespace Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DebugBarTest extends WebTestCase
{
    public function testCollectorIsEnabledByBundle()
    {
        $client = static::createClient();
        $client->followRedirects(true);
        $client->enableProfiler();
        $client->request('GET', '/route1');
        $collector = $client->getProfile()->getCollector('phpmetrics_collector');
        $this->assertInstanceOf('Hal\Bundle\PhpMetricsCollector\Collector\PhpMetricsCollector', $collector);
    }

    public function testCollectorCollectsInformations()
    {
        $client = static::createClient();
        $client->followRedirects(true);
        $client->enableProfiler();
        $client->request('GET', '/route1');
        $collector = $client->getProfile()->getCollector('phpmetrics_collector');
        $this->assertGreaterThan(0, $collector->getMaintainabilityIndex());
        $this->assertIsFloat($collector->getMaintainabilityIndex());
        $this->assertIsFloat($collector->getCommentWeight());
        $this->assertIsFloat($collector->getDifficulty());
        $this->assertIsFloat($collector->getBugs());
    }
}
