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
class Row
{
    private $name;
    private $form;
    private $labelAttributes;
    private $elementAttributes;
    private $errorAttributes;
    private $printAllErrors = false;
    private $labelPosition = 'default';

    public function __construct($name, Form $form)
    {
        $this->name = $name;
        $this->form = $form;
    }

    public function setLabelAttributes($attributes)
    {
        if (is_string($attributes)) {
            $attributes = $this->form->parseAttributes($attributes);
        }

        $this->labelAttributes = $attributes;

        return $this;
    }

    public function setElementAttributes($attributes)
    {
        if (is_string($attributes)) {
            $attributes = $this->form->parseAttributes($attributes);
        }

        $this->elementAttributes = $attributes;

        return $this;
    }

    public function setErrorAttributes($attributes)
    {
        if (is_string($attributes)) {
            $attributes = $this->form->parseAttributes($attributes);
        }

        $this->errorAttributes = $attributes;

        return $this;
    }

    public function setLabelPosition($position)
    {
        $this->labelPosition = $position;

        return $this;
    }

    public function printAllErrors()
    {
        $this->printAllErrors = true;

        return $this;
    }

    public function __toString()
    {
        $str = '';

        if ($this->form->getRowWrapperTagName()) {
            $str .= '<'.$this->form->getRowWrapperTagName();

            $attributes = $this->form->getRowWrapperAttributes() ?: [];

            $errorClass = $this->form->getRowWrapperErrorClass();
            if ($errorClass && $this->form->hasErrors($this->name)) {
                if (array_key_exists('class', $attributes)) {
                    $attributes['class'] .= ' '.$errorClass;
                }
                else {
                    $attributes['class'] = $errorClass;
                }
            }

            $str .= $this->form->implodeAttributes($attributes).">\n";
        }

        if ($this->labelPosition === 'append') {
            $str .= $this->form->openLabel(null, $this->labelAttributes);
        } elseif ($this->labelPosition === 'prepend') {
            $str .= $this->form->openLabel($this->name, $this->labelAttributes);
        } else {
            $str .= $this->form->label($this->name, $this->labelAttributes);
        }

        $str .= $this->form->element($this->name, $this->elementAttributes);

        if ($this->labelPosition === 'append') {
            $str .= $this->form->closeLabel($this->name);
        } elseif ($this->labelPosition === 'prepend') {
            $str .= $this->form->closeLabel();
        }

        if ($this->printAllErrors) {
            $str .= $this->form->errors($this->name, $this->errorAttributes);
        } else {
            $str .= $this->form->error($this->name, $this->errorAttributes);
        }

        if ($this->form->getRowWrapperTagName()) {
            $str .= '</'.$this->form->getRowWrapperTagName().">\n";
        }

        return $str;
    }
}
