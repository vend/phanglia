<?php

namespace Phanglia;

class MetricTest extends Test
{
    public function testConstructor()
    {
        $metric = new Metric('test_metric', Ganglia::TYPE_DOUBLE, Ganglia::SLOPE_BOTH, 50, 10);
        $this->assertTrue($metric instanceof Metric);
        $this->assertEquals('test_metric', $metric->getName());
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

    public function testHostnameSpoofing()
    {
        $metadata = "\x00\x00\x00\x08\x00\x00\x00\x51\x37\xf6"
            . "\xd6\x56\xe2\x86\xf6\x37\x47\xe2\x56\x87\x16\xd6\x07"
            . "\xc6\x56\xe2\x36\xf6\xd6\x00\x00\x00\x00\x00\x00\x90"
            . "\x37\xf6\xd6\x56\x47\x86\x96\xe6\x76\x00\x00\x00\x00"
            . "\x00\x00\x00\x00\x00\x00\x50\x66\xc6\xf6\x16\x47\x00"
            . "\x00\x00\x00\x00\x00\x90\x37\xf6\xd6\x56\x47\x86\x96"
            . "\xe6\x76\x00\x00\x00\x00\x00\x00\x50\xd6\x56\x86\xf2"
            . "\x37\x00\x00\x00\x00\x00\x00\x30\x00\x00\x00\xc3\x00"
            . "\x00\x00\x87\x00\x00\x00\x30\x00\x00\x00\x50\x45\x94"
            . "\x45\xc4\x54\x00\x00\x00\x00\x00\x00\x70\x14\x02\x47"
            . "\x96\x47\xc6\x56\x00\x00\x00\x00\x40\x44\x54\x35\x34"
            . "\x00\x00\x00\xd0\x14\x02\x46\x56\x37\x36\x27\x96\x07"
            . "\x47\x96\xf6\xe6\x00\x00\x00\x00\x00\x00\x50\x74\x25"
            . "\xf4\x55\x05\x00\x00\x00\x00\x00\x00\xa0\x37\xf6\xd6"
            . "\x56\xd2\x76\x27\xf6\x57\x07\x00\x00";

        $value = "\x00\x00\x00\x58\x00\x00\x00\x51\x37\xf6\xd6\x56"
            . "\xe2\x86\xf6\x37\x47\xe2\x56\x87\x16\xd6\x07\xc6\x56"
            . "\xe2\x36\xf6\xd6\x00\x00\x00\x00\x00\x00\x90\x37\xf6"
            . "\xd6\x56\x47\x86\x96\xe6\x76\x00\x00\x00\x00\x00\x00"
            . "\x00\x00\x00\x00\x20\x52\x37\x00\x00\x00\x00\x00\x70"
            . "\x43\x23\xe2\x33\x33\x33\x33\x00";

        $metric = new Metric('something', Ganglia::TYPE_FLOAT, Ganglia::SLOPE_BOTH, 60, 0, 'meh/s');
        $metric->setHost('some.host.example.com', true);
        $metric->setGroup('some-group');

        $this->assertEquals($metadata, $metric->getMetadataPacket());
        $this->assertEquals($value, $metric->getValuePacket(42.3333));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSetTypeInvalid()
    {
        new Metric('test_metric', 'some_invalid_type');
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSetSlopeInvalid()
    {
        new Metric('test_metric', Ganglia::TYPE_DOUBLE, 'some_invalid_slope');
    }
}
