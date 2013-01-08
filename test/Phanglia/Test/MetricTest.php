<?php

namespace Phanglia\Test;

use Phanglia\Ganglia;
use Phanglia\Packer;
use Phanglia\Metric;

class MetricTest extends Test
{
    public function testConstructor()
    {
        $metric = new Metric('test_metric', Ganglia::TYPE_DOUBLE, Ganglia::SLOPE_BOTH, 50, 10);
        $this->assertTrue($metric instanceof Metric);
    }

    public function testGetPackets()
    {
        $metric   = new Metric('test_metric', Ganglia::TYPE_DOUBLE);
        $metadata = $metric->getMetadataPacket();
        $value    = $metric->getValuePacket('1.0000');

        $this->assertNotEmpty($metadata);
        $this->assertNotEmpty($value);
    }

    public function testNoGroupMetadata()
    {
        $correct = ""
                 . "\x00\x00\x00\x80"
                 . "\x00\x00\x00\x00"
                 . "\x00\x00\x00\x03foo\x00"
                 . "\x00\x00\x00\x00"
                 . "\x00\x00\x00\x06string\x00\x00"
                 . "\x00\x00\x00\x03foo\x00"
                 . "\x00\x00\x00\x00"
                 . "\x00\x00\x00\x04"
                 . "\x00\x00\x00<"
                 . "\x00\x00\x00\x00"
                 . "\x00\x00\x00\x00";

        $metric = new Metric('foo', Ganglia::TYPE_STRING, Ganglia::SLOPE_UNSPECIFIED, 60, 0);
        $metric->setHost('', false);
        $this->assertEquals($correct, $metric->getMetadataPacket());
    }

    public function testGroupMetadata()
    {
        $correct = ""
            . "\x00\x00\x00\x80"
            . "\x00\x00\x00\x08hostname"
            . "\x00\x00\x00\x03foo\x00"
            . "\x00\x00\x00\x00"
            . "\x00\x00\x00\x06string\x00\x00"
            . "\x00\x00\x00\x03foo\x00"
            . "\x00\x00\x00\x00"
            . "\x00\x00\x00\x03"
            . "\x00\x00\x00<"
            . "\x00\x00\x00\x00"
            . "\x00\x00\x00\x01"
            . "\x00\x00\x00\x05GROUP\x00\x00\x00"
            . "\x00\x00\x00\x09groupname\x00\x00\x00";

        $metric = new Metric('foo', Ganglia::TYPE_STRING, Ganglia::SLOPE_BOTH, 60, 0);
        $metric->setHost('hostname', false);
        $metric->setGroup('groupname');

        $this->assertEquals($correct, $metric->getMetadataPacket());
    }

    public function testStringValue()
    {
        $correct = ""
            . "\x00\x00\x00\x85"
            . "\x00\x00\x00\x08hostname"
            . "\x00\x00\x00\003foo\x00"
            . "\x00\x00\x00\x00"
            . "\x00\x00\x00\002%s\x00\x00"
            . "\x00\x00\x00\003bar\x00";

        $metric = new Metric('foo', Ganglia::TYPE_STRING, Ganglia::SLOPE_NEGATIVE, 60, 0);
        $metric->setHost('hostname', false);

        $this->assertEquals($correct, $metric->getValuePacket('bar'));
    }

    public function testIntValue()
    {
        $correct = ""
            . "\x00\x00\x00\x85"
            . "\x00\x00\x00\x08hostname"
            . "\x00\x00\x00\vmetric_name\x00"
            . "\x00\x00\x00\x01"
            . "\x00\x00\x00\002%s\x00\x00"
            . "\x00\x00\x00\x041025";

        $metric = new Metric('metric_name', Ganglia::TYPE_UNSIGNED_INT, Ganglia::SLOPE_POSITIVE, 60, 0);
        $metric->setHost('hostname', true);
        $metric->setGroup('agroup');

        $this->assertEquals($correct, $metric->getValuePacket(1025));
    }
}
