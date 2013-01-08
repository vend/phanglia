# Phanglia
## PHP bindings for Ganglia

A small set of PSR-0 compatible classes for producing and sending GMetric packets
for Ganglia monitoring.

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
point your PSR-0 autoloader at the lib directory.