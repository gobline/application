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
class Group
{
    use HelperTrait;

    private $name;
    private $form;
    private $groupElementLabelAttributes;
    private $groupElementWrapper;
    private $groupElementWrapperTagName;
    private $groupElementWrapperAttributes;

    public function __construct($name, Form $form)
    {
        $this->name = $name;
        $this->form = $form;
    }

    public function __toString()
    {
        $str = '';

        $rowTagName = ($this->rowWrapperTagName !== null) ? $this->rowWrapperTagName : $this->form->getRowWrapperTagName();
        $rowAttributes = ($this->rowWrapperAttributes !== null) ? $this->rowWrapperAttributes : $this->form->getRowWrapperAttributes() ?: [];
        $rowErrorClass = ($this->rowWrapperErrorClass !== null) ? $this->rowWrapperErrorClass : $this->form->getRowWrapperErrorClass();
        $labelPosition = ($this->labelPosition !== null) ? $this->labelPosition : $this->form->getLabelPosition();
        $printAllErrors = ($this->printAllErrors !== null) ? $this->printAllErrors : $this->form->isPrintAllErrors();

        $element = $this->form->element($this->name, $this->elementAttributes);

        if ($rowTagName) {
            $str .= '<'.$rowTagName;

            if ($rowErrorClass && $this->form->hasErrors($this->name)) {
                if (array_key_exists('class', $rowAttributes)) {
                    $rowAttributes['class'] .= ' '.$rowErrorClass;
                }
                else {
                    $rowAttributes['class'] = $rowErrorClass;
                }
            }

            $str .= $this->form->implodeAttributes($rowAttributes).">\n";
        }

        $str .= $this->form->label($element, $this->labelAttributes);

        foreach ($element->getSwitches() as $switch) {

            if ($this->groupElementWrapperTagName) {
                $str .= '<'.$this->groupElementWrapperTagName;

                $str .= $this->form->implodeAttributes($this->groupElementWrapperAttributes).">\n";
            }

            if ($labelPosition === 'append') {
                $str .= $this->form->openLabel(null, $this->groupElementLabelAttributes ?: '');
            } elseif ($labelPosition === 'prepend') {
                $str .= $this->form->openLabel($switch, $this->groupElementLabelAttributes ?: '');
            } else {
                $str .= $this->form->label($switch, $this->groupElementLabelAttributes ?: '');
            }

            $str .= $switch;

            if ($labelPosition === 'append') {
                $str .= $this->form->closeLabel($switch);
            } elseif ($labelPosition === 'prepend') {
                $str .= $this->form->closeLabel();
            }

            if ($this->groupElementWrapperTagName) {
                $str .= '</'.$this->groupElementWrapperTagName.">\n";
            }

        }

        if ($printAllErrors) {
            $str .= $this->form->errors($this->name, $this->errorAttributes);
        } else {
            $str .= $this->form->error($this->name, $this->errorAttributes);
        }

        if ($rowTagName) {
            $str .= '</'.$rowTagName.">\n";
        }

        return $str;
    }

    public function setGroupElementLabelAttributes($attributes)
    {
        if (is_string($attributes)) {
            $attributes = $this->parseAttributes($attributes);
        }

        $this->groupElementLabelAttributes = $attributes;

        return $this;
    }

    public function setGroupElementWrapper($tagName, $attributes)
    {
        if (is_string($attributes)) {
            $attributes = $this->parseAttributes($attributes);
        }

        $this->groupElementWrapperTagName = $tagName;
        $this->groupElementWrapperAttributes = $attributes;

        return $this;
    }

    public function getGroupElementWrapperTagName()
    {
        return $this->groupElementWrapperTagName;
    }

    public function getGroupElementWrapperAttributes()
    {
        return $this->groupElementWrapperAttributes;
    }
}
