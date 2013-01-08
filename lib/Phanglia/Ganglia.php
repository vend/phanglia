<?php

namespace Phanglia;

class Ganglia
{
    // Slopes
    const SLOPE_ZERO        = 0;
    const SLOPE_POSITIVE    = 1;
    const SLOPE_NEGATIVE    = 2;
    const SLOPE_BOTH        = 3;
    const SLOPE_UNSPECIFIED = 4;

    // Valid slopes
    public static $slopes = array(
        self::SLOPE_ZERO,
        self::SLOPE_POSITIVE,
        self::SLOPE_NEGATIVE,
        self::SLOPE_BOTH,
        self::SLOPE_UNSPECIFIED
    );

    // Magic numbers
    const MAGIC_NUMBER_METADATA = 128;
    const MAGIC_NUMBER_VALUE    = 133;

    // XDR types
    const TYPE_STRING          = 'string';
    const TYPE_SIGNED_BYTE     = 'int8';
    const TYPE_UNSIGNED_BYTE   = 'uint8';
    const TYPE_SHORT           = 'int16';
    const TYPE_UNSIGNED_SHORT  = 'uint16';
    const TYPE_INT             = 'int32';
    const TYPE_UNSIGNED_INT    = 'uint32';
    const TYPE_FLOAT           = 'float';
    const TYPE_DOUBLE          = 'double';

    // Valid XDR types
    public static $types = array(
        self::TYPE_DOUBLE,
        self::TYPE_FLOAT,
        self::TYPE_INT,
        self::TYPE_SHORT,
        self::TYPE_SIGNED_BYTE,
        self::TYPE_STRING,
        self::TYPE_UNSIGNED_BYTE,
        self::TYPE_UNSIGNED_INT,
        self::TYPE_UNSIGNED_SHORT
    );
}