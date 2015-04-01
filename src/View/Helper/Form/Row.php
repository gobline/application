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
    use HelperTrait;

    private $name;
    private $form;

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

        if ($labelPosition === 'append') {
            $str .= $this->form->openLabel(null, $this->labelAttributes);
        } elseif ($labelPosition === 'prepend') {
            $str .= $this->form->openLabel($this->name, $this->labelAttributes);
        } else {
            $str .= $this->form->label($this->name, $this->labelAttributes);
        }

        $str .= $this->form->element($this->name, $this->elementAttributes);

        if ($labelPosition === 'append') {
            $str .= $this->form->closeLabel($this->name);
        } elseif ($labelPosition === 'prepend') {
            $str .= $this->form->closeLabel();
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
}
