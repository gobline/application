<?php

/*
 * Mendo Framework
 *
 * (c) Mathieu Decaffmeyer <mdecaffmeyer@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mendo\Mvc\View\Helper\Form;

/**
 * @author Mathieu Decaffmeyer <mdecaffmeyer@gmail.com>
 */
trait HelperTrait
{
    private $elementAttributes;
    private $labelAttributes;
    private $errorAttributes;
    private $rowWrapperTagName;
    private $rowWrapperAttributes;
    private $rowWrapperErrorClass;
    private $rowPrintAllErrors;
    private $rowLabelPosition;

    public function setElementAttributes($attributes)
    {
        if (is_string($attributes)) {
            $attributes = $this->parseAttributes($attributes);
        }

        $this->elementAttributes = $attributes;

        return $this;
    }

    public function setLabelAttributes($attributes)
    {
        if (is_string($attributes)) {
            $attributes = $this->parseAttributes($attributes);
        }

        $this->labelAttributes = $attributes;

        return $this;
    }

    public function setErrorAttributes($attributes)
    {
        if (is_string($attributes)) {
            $attributes = $this->parseAttributes($attributes);
        }

        $this->errorAttributes = $attributes;

        return $this;
    }

    public function setRowWrapper($tagName, $attributes, $errorClass)
    {
        if (is_string($attributes)) {
            $attributes = $this->parseAttributes($attributes);
        }

        $this->rowWrapperTagName = $tagName;
        $this->rowWrapperAttributes = $attributes;
        $this->rowWrapperErrorClass = $errorClass;

        return $this;
    }

    public function getRowWrapperTagName()
    {
        return $this->rowWrapperTagName;
    }

    public function getRowWrapperAttributes()
    {
        return $this->rowWrapperAttributes;
    }

    public function getRowWrapperErrorClass()
    {
        return $this->rowWrapperErrorClass;
    }

    public function setRowLabelPosition($position)
    {
        $this->rowLabelPosition = $position;

        return $this;
    }

    public function getRowLabelPosition()
    {
        return $this->rowLabelPosition;
    }

    public function setRowPrintAllErrors($printAllErrors)
    {
        $this->rowPrintAllErrors = $printAllErrors;

        return $this;
    }

    public function isRowPrintAllErrors()
    {
        return (bool) $this->rowPrintAllErrors;
    }

    public function parseAttributes($attributes)
    {
        preg_match_all('/(?<prop>\\w+)=\"(?<val>.*?)\"\\s?/', $attributes, $matches);

        return array_combine($matches['prop'], $matches['val']);
    }
}