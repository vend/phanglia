<?php

namespace Phanglia;

class PackerTest extends Test
{
    public function testStringAlignment()
    {
        $packer = new Packer();
        $packer->string('foo');
        $this->assertEquals(strlen($packer), 8);

        $packer = new Packer();
        $packer->string('a');
        $this->assertEquals(strlen($packer), 8);

        $packer = new Packer();
        $packer->string('test');
        $this->assertEquals(strlen($packer), 8);

        $packer = new Packer();
        $packer->string('another test yo');
        $this->assertEquals(strlen($packer), 20);
    }

    public function testEmptyStringPacking()
    {
        $packer = new Packer();
        $packer->string('');
        $this->assertEquals((string)$packer, "\x00\x00\x00\x00");
    }

    public function testStringPacking()
    {
        $packer = new Packer();
        $packer->string('foo');
        $this->assertEquals((string)$packer, "\x00\x00\x00\x03foo\x00");

        $packer = new Packer();
        $packer->string('test');
        $this->assertEquals((string)$packer, "\x00\x00\x00\x04test");
    }

    public function testUInt32Packing()
    {
        $packer = new Packer();
        $packer->uint32(0);
        $this->assertEquals((string)$packer, "\x00\x00\x00\x00");

        $packer = new Packer();
        $packer->uint32(4294967294);
        $this->assertEquals((string)$packer, "\xFF\xFF\xFF\xFE");
    }

    public function testChaining()
    {
        $packer = new Packer();
        $packer->string('foo');
        $packer->uint32(1);
        $this->assertEquals((string)$packer, "\x00\x00\x00\x03foo\x00\x00\x00\x00\x01");
    }

    public function testClear()
    {
        $packer = new Packer();
        $packer->string('foo');
        $packer->uint32(1);
        $packer->clear();
        $packer->string('bar');

        $this->assertEquals((string)$packer, "\x00\x00\x00\x03bar\x00");
    }
}
