<?php

namespace Phanglia;

class SocketTest extends Test
{
    public function testConstructor()
    {
        $socket = new Socket('127.0.0.1', 32129, []);
        $this->assertInstanceOf('Phanglia\Socket', $socket);
    }

    public function testLifecycle()
    {
        $socket = new Socket('127.0.0.1', 32131, []);

        $socket->send('some_payload');
        $this->assertTrue($socket->isOpen());

        $socket->close();
        $this->assertFalse($socket->isOpen());
    }

    public function testSendMetric()
    {
        $mock = $this->getMockMetric();

        $mock->expects($this->once())
            ->method('getMetadataPacket')
            ->will($this->returnValue('metadata'));

        $mock->expects($this->once())
            ->method('getValuePacket')
            ->will($this->returnValue('value'));

        $socket = new Socket('127.0.0.1', 32131, []);
        $socket->sendMetric($mock, 0.0);
    }

    /**
     * @expectedException Phanglia\ConnectionException
     */
    public function testConnectionProblem()
    {
        $socket = new Socket('-_/#$*&!)@&#$', 32132, []);

        $socket->send('some_payload');
        $this->assertTrue($socket->isOpen());

        $socket->close();
        $this->assertFalse($socket->isOpen());
    }
}
