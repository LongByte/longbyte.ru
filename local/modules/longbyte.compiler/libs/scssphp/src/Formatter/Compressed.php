<?php

/**
 * SCSSPHP
 *
 * @copyright 2012-2015 Leaf Corcoran
 *
 * @license http://opensource.org/licenses/MIT MIT
 *
 * @link http://leafo.github.io/scssphp
 */

namespace Leafo\ScssPhp\Formatter;

use Leafo\ScssPhp\Formatter;
use Leafo\ScssPhp\Formatter\OutputBlock;

/**
 * Compressed formatter
 *
 * @author Leaf Corcoran <leafot@gmail.com>
 */
class Compressed extends Formatter
{

    /**
     * {@inheritdoc}
     */
    public function __construct()
    {
        $this->indentLevel = 0;
        $this->indentChar = '  ';
        $this->break = '';
        $this->open = '{';
        $this->close = '}';
        $this->tagSeparator = ',';
        $this->assignSeparator = ':';
        $this->keepSemicolons = false;
    }

    /**
     * {@inheritdoc}
     */
    public function blockLines(OutputBlock $block)
    {
        $inner = $this->indentStr();

        $glue = $this->break . $inner;

        foreach ($block->lines as $index => $line) {
            if (substr($line, 0, 2) === '/*' && substr($line, 2, 1) !== '!') {
                unset($block->lines[$index]);
            } elseif (substr($line, 0, 3) === '/*!') {
                $block->lines[$index] = '/*' . substr($line, 3);
            }
        }

        echo $inner . implode($glue, $block->lines);

        if (!empty($block->children)) {
            echo $this->break;
        }
    }

}
