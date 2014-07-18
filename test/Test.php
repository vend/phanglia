<?php

namespace Phanglia;

abstract class Test extends \PHPUnit_Framework_TestCase
{
    protected function getMockMetric()
    {
        return $this->getMockBuilder('Phanglia\Metric')
            ->disableOriginalConstructor()
            ->getMock();
    }
}
