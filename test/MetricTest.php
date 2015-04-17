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
        $metadata = "\x00\x00\x00\x80\x00\x00\x00\x15\x73\x6f\x6d\x65\x2e\x68\x6f\x73\x74\x2e\x65\x78\x61\x6d\x70\x6c\x65\x2e\x63\x6f\x6d\x00\x00\x00\x00\x00\x00\x09\x73\x6f\x6d\x65\x74\x68\x69\x6e\x67\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x05\x66\x6c\x6f\x61\x74\x00\x00\x00\x00\x00\x00\x09\x73\x6f\x6d\x65\x74\x68\x69\x6e\x67\x00\x00\x00\x00\x00\x00\x05\x6d\x65\x68\x2f\x73\x00\x00\x00\x00\x00\x00\x03\x00\x00\x00\x3c\x00\x00\x00\x78\x00\x00\x00\x03\x00\x00\x00\x05\x54\x49\x54\x4c\x45\x00\x00\x00\x00\x00\x00\x07\x41\x20\x74\x69\x74\x6c\x65\x00\x00\x00\x00\x04\x44\x45\x53\x43\x00\x00\x00\x0d\x41\x20\x64\x65\x73\x63\x72\x69\x70\x74\x69\x6f\x6e\x00\x00\x00\x00\x00\x00\x05\x47\x52\x4f\x55\x50\x00\x00\x00\x00\x00\x00\x0a\x73\x6f\x6d\x65\x2d\x67\x72\x6f\x75\x70\x00\x00";
        $value = "\x00\x00\x00\x85\x00\x00\x00\x15\x73\x6f\x6d\x65\x2e\x68\x6f\x73\x74\x2e\x65\x78\x61\x6d\x70\x6c\x65\x2e\x63\x6f\x6d\x00\x00\x00\x00\x00\x00\x09\x73\x6f\x6d\x65\x74\x68\x69\x6e\x67\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x02\x25\x73\x00\x00\x00\x00\x00\x07\x34\x32\x2e\x33\x33\x33\x33\x00";

        $metric = new Metric('something', Ganglia::TYPE_FLOAT, Ganglia::SLOPE_BOTH, 60, 0, 'meh/s');
        $metric->setHost('some.host.example.com', false);
        $metric->setGroup('some-group');
        $metric->setTitle('A title');
        $metric->setDesc('A description');

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
