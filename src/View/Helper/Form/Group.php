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
    private $groupLabel = true;
    private $groupLabelAttributes;
    private $groupWrapper;
    private $groupWrapperTagName;
    private $groupWrapperAttributes;

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

        if ($this->groupLabel) {
            $str .= $this->form->label($element, $this->labelAttributes);
        }

        foreach ($element->getSwitches() as $switch) {

            if ($this->groupWrapperTagName) {
                $str .= '<'.$this->groupWrapperTagName;

                $str .= $this->form->implodeAttributes($this->groupWrapperAttributes).">\n";
            }

            if ($labelPosition === 'append') {
                $str .= $this->form->openLabel(null, $this->groupLabelAttributes ?: '');
            } elseif ($labelPosition === 'prepend') {
                $str .= $this->form->openLabel($switch, $this->groupLabelAttributes ?: '');
            } elseif ($labelPosition === 'default') {
                $str .= $this->form->label($switch, $this->groupLabelAttributes ?: '');
            }

            $str .= $switch;

            if ($labelPosition === 'append') {
                $str .= $this->form->closeLabel($switch);
            } elseif ($labelPosition === 'prepend') {
                $str .= $this->form->closeLabel();
            }

            if ($this->groupWrapperTagName) {
                $str .= '</'.$this->groupWrapperTagName.">\n";
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

    public function setGroupLabelAttributes($attributes)
    {
        if (is_string($attributes)) {
            $attributes = $this->parseAttributes($attributes);
        }

        $this->groupLabelAttributes = $attributes;

        return $this;
    }

    public function setGroupWrapper($tagName, $attributes)
    {
        if (is_string($attributes)) {
            $attributes = $this->parseAttributes($attributes);
        }

        $this->groupWrapperTagName = $tagName;
        $this->groupWrapperAttributes = $attributes;

        return $this;
    }

    public function setGroupWrapperTagName($tagName)
    {
        $this->groupWrapperTagName = $tagName;
    }

    public function getGroupWrapperTagName()
    {
        return $this->groupWrapperTagName;
    }

    public function setGroupWrapperAttributes($attributes)
    {
        $this->groupWrapperAttributes = $attributes;
    }

    public function getGroupWrapperAttributes()
    {
        return $this->groupWrapperAttributes;
    }

    public function setNoGroupLabel()
    {
        $this->groupLabel = false;

        return $this;
    }
}
