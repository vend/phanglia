# Phanglia
## PHP bindings for Ganglia

A small library for producing and sending GMetric packets for Ganglia monitoring.

[![Build Status](https://travis-ci.org/vend/phanglia.png)](https://travis-ci.org/vend/phanglia)
[![Scrutinizer Quality Score](https://scrutinizer-ci.com/g/vend/phanglia/badges/quality-score.png?s=86a0badbf27f88592185b7e046146cc7e11b5a00)](https://scrutinizer-ci.com/g/vend/phanglia/)
[![Code Coverage](https://scrutinizer-ci.com/g/vend/phanglia/badges/coverage.png?s=b9612b582ab4f994b1b688db76d7e9f751bc658b)](https://scrutinizer-ci.com/g/vend/phanglia/)

### Usage

```php
// Gets a metric definition
$metric = new Phanglia\Metric('name', Ganglia::TYPE_STRING, Ganglia::SLOPE_UNSPECIFIED);

// The metadata and value packets as binary strings, in case you want to send them yourself
$meta  = $metric->getMetadataPacket();
$value = $metric->getValuePacket('some value')

// Sending them on to a Ganglia collector (default udp://127.0.0.1:8649)
$socket = new Phanglia\Socket();
$socket->sendMetric($metric, 'some value');
```

### Installation

Phanglia is available via composer, with a package name of `vend/phanglia`. Or
point your PSR-4 autoloader at the lib directory.
