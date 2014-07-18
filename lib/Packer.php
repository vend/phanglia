<?php

namespace Phanglia;

/**
 * XDR packing
 */
class Packer
{
    /**
     * @var string
     */
    protected $value = '';

    /**
     * Strings are:
     *   * The length of the string encoded as a uint32
     *   * The string itself
     *   * NUL-padding to bring the string to a four-byte boundary
     *
     * @param string $value
     * @return \Phanglia\Packer
     */
    public function string($value)
    {
        $length = strlen(strval($value));
        $remainder = $length % 4;

        $padded = $length;
        if ($remainder != 0) {
            $padded = $length + (4 - $remainder);
        }

        $this->uint32($length);
        $this->value .= pack('a' . $padded, $value);

        return $this;
    }

    /**
     * XDR encodes ints as big-endian
     *
     * @param int $value
     * @return \Phanglia\Packer
     */
    public function uint32($value)
    {
        $this->value .= pack('N', $value);
        return $this;
    }

    /**
     * Clears the packed value
     *
     * @return void
     */
    public function clear()
    {
        $this->value = '';
    }

    /**
     * Gets the packed value
     *
     * @return string
     */
    public function __toString()
    {
        return $this->value;
    }
}