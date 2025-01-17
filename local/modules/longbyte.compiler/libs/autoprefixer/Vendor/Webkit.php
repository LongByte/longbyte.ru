<?php

namespace Padaliyajay\PHPAutoprefixer\Vendor;

use Padaliyajay\PHPAutoprefixer\Vendor\Vendor;

class Webkit extends Vendor
{
    protected static $RULE_PROPERTY = array(
        'column-count' => '-webkit-column-count',
        'column-gap' => '-webkit-column-gap',
        'user-select' => '-webkit-user-select',
        'appearance' => '-webkit-appearance',
        'animation' => '-webkit-animation',
        'transition' => '-webkit-transition',
        'transform' => '-webkit-transform',
        'backface-visibility' => '-webkit-backface-visibility',
        'perspective' => '-webkit-perspective',
        'background-clip' => '-webkit-background-clip',
        'filter' => '-webkit-filter',

    );

    protected static $RULE_VALUE = array(
        'display' => array(
            'flex' => '-webkit-flex',
            'inline-flex' => '-webkit-inline-flex',
        ),
        'position' => array('sticky' => '-webkit-sticky'),
    );

    protected static $PSEUDO = array(
        '::placeholder' => '::-webkit-input-placeholder',
    );

    protected static $AT_RULE = array('keyframes' => '-webkit-keyframes');
}

