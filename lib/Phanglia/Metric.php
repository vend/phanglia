<?php

namespace Phanglia;

use Phanglia\Packer;

class Metric
{
    /**
     * The host
     *
     * @var string
     */
    protected $host = '127.0.0.1';

    /**
     * The name of the metric
     *
     * @var string
     */
    protected $name;

    /**
     * Whether to spoof the metric's hostname
     *
     * @var int
     */
    protected $spoof = 0;

    /**
     * This metric's data type
     *
     * These correspond to XDR
     *
     * @var string See Ganglia::TYPE_* (string, int8, uint8, int16,
     *              uint16, int32, uint32, float, double)
     */
    protected $type;

    /**
     * Group name (optional)
     *
     * @var string
     */
    protected $group = null;

    /**
     * The slope of the metric
     *
     * @var int The sign of the derivative of the metric. See Ganglia::SLOPE_*
     */
    protected $slope = Ganglia::SLOPE_UNSPECIFIED;

    /**
     * The unit label
     *
     * @var string
     */
    protected $units = '';

    /**
     * @var int
     */
    protected $tmax = 0;

    /**
     * @var int
     */
    protected $dmax = 0;

    /**
     * Associated values
     *
     * @var array<string>
     */
    protected $values = array();

    /**
     * Constructor
     *
     * @param string $name
     * @param string $type The value data type, one of string, int8, uint8, int16, uint16, int32, uint32, float, double
     * @param int    $slope
     * @param int    $tmax
     * @param int    $dmax
     */
    public function __construct(
        $name,
        $type  = Ganglia::TYPE_DOUBLE,
        $slope = Ganglia::SLOPE_UNSPECIFIED,
        $tmax  = 60,
        $dmax  = 0,
        $units = ''
    ) {
        $this->name  = $name;
        $this->tmax  = $tmax;
        $this->dmax  = $dmax;
        $this->units = $units;

        $this->setType($type);
        $this->setSlope($slope);
    }

    /**
     * Sets the value type of this metric
     *
     * @param unknown $type
     * @throws \InvalidArgumentException
     */
    public function setType($type)
    {
        if (!in_array($type, Ganglia::$types)) {
            throw new \InvalidArgumentException('Invalid value type');
        }
        $this->type = $type;
    }

    /***
     * Sets the slope
     *
     * @param int $slope
     */
    public function setSlope($slope)
    {
        if (!in_array($slope, Ganglia::$slopes)) {
            throw new \InvalidArgumentException('Invalid slope type');
        }
        $this->slope = $slope;
    }

    /**
     * Sets the host for this metric
     *
     * @param string  $host
     * @param boolean $spoof
     */
    public function setHost($host, $spoof = false)
    {
        $this->host = $host;
        $this->spoof = (boolean)$spoof;
    }

    /**
     * Sets the group for this metric
     *
     * @param string $group
     */
    public function setGroup($group)
    {
        $this->group = $group;
    }

    /**
     * Adds a value to this metric
     *
     * @param string $value
     */
    public function addValue($value)
    {
        $this->values[] = $value;
    }

    /**
     * @param int $type See Ganglia::TYPE_*
     * @return \Phanglia\Xdr\Packer
     */
    protected function getPacker($type)
    {
        $packer = new Packer();

        $packer->uint32($type);        // Packet type (metadata or value)
        $packer->string($this->host);  // Host name
        $packer->string($this->name);  // Metric name
        $packer->uint32($this->spoof); // Spoofing enabled?

        return $packer;
    }

    /**
     * @return string
     */
    public function getMetadataPacket()
    {
        $packer = $this->getPacker(Ganglia::MAGIC_NUMBER_METADATA);

        $packer->string($this->type);  // Data type (see XDR types), as string
        $packer->string($this->name);  // Yes, name is in metadata twice
        $packer->string($this->units); // Unit name string
        $packer->uint32($this->slope); // Slope integer
        $packer->uint32($this->tmax);  // tmax
        $packer->uint32($this->dmax);  // dmax

        if (isset($this->group)) {
            $packer->uint32(1);        // The number of extra elements
            $packer->string('GROUP');
            $packer->string($this->group);
        } else {
            $packer->uint32(0);
        }

        return (string)$packer;
    }

    /**
     * @return array<string>
     */
    public function getValuePacket($value)
    {
        $packer = $this->getPacker(Ganglia::MAGIC_NUMBER_VALUE);

        $packer->string('%s'); // Format field
        $packer->string(strval($value));

        return (string)$packer;
    }
}