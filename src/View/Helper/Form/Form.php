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

use Mendo\Mvc\View\Helper\ViewHelperInterface;
use Mendo\Form\Form as ModelForm;
use Mendo\Http\Request\HttpRequestInterface;
use Mendo\Translator\Translator;

/**
 * @author Mathieu Decaffmeyer <mdecaffmeyer@gmail.com>
 */
class Form implements ViewHelperInterface
{
    use HelperTrait;

    private $containers = [];
    private $request;
    private $translator;

    public function __construct(Translator $translator = null, HttpRequestInterface $request = null)
    {
        $this->translator = $translator;
        $this->request = $request;
    }

    public function form()
    {
        return $this;
    }

    public function open(ModelForm $form, $attributes = [])
    {
        if ($this->containers) {
            throw new \RuntimeException('Prior call to close() is required');
        }

        if (is_string($attributes)) {
            $attributes = $this->parseAttributes($attributes);
        }

        foreach ($attributes as $attribute => $value) {
            $form->setAttribute($attribute, $value);
        }

        $this->containers[] = $form;

        return '<form'.$this->implodeAttributes($form->getAttributes()).">\n";
    }

    public function close()
    {
        $this->containers = [];

        return "</form>\n";
    }

    public function getContainer()
    {
        if (!$this->containers) {
            throw new \RuntimeException('Prior call to open() is required');
        }

        $c = end($this->containers);
        reset($this->containers);

        return $c;
    }

    public function openFieldset($name, $attributes = [])
    {
        if (is_string($attributes)) {
            $attributes = $this->parseAttributes($attributes);
        }

        $container = $this->getContainer();
        $fieldSet = $container->getComponent($name);

        foreach ($attributes as $attribute => $value) {
            $fieldSet->setAttribute($attribute, $value);
        }

        $this->containers[] = $fieldSet;

        $str = '<fieldset'.$this->implodeAttributes($fieldSet->getAttributes()).">\n";

        $legend = $fieldSet->getLegend();
        if ($legend) {
            if ($this->translator) {
                $legend = $this->translator->translate($legend, [], $this->request->getLanguage());
                $fieldSet->setLegend($legend);
            }
            $str .= '<legend>'.$legend."</legend>\n";
        }

        return $str;
    }

    public function closeFieldset()
    {
        array_pop($this->containers);

        return "</fieldset>\n";
    }

    public function row($name)
    {
        return new Row($name, $this);
    }

    public function group($name)
    {
        return new Group($name, $this);
    }

    public function element($name, $attributes = null)
    {
        if ($attributes === null) {
            $attributes = $this->elementAttributes ?: [];
        } elseif (is_string($attributes)) {
            $attributes = $this->parseAttributes($attributes);
        }

        $container = $this->getContainer();
        $element = $container->getComponent($name);

        foreach ($attributes as $attribute => $value) {
            $element->setAttribute($attribute, $value);
        }

        if (!$element->hasAttribute('id')) {
            $element->setAttribute('id', $this->hyphenate($element->getAttribute('name')).'-id');
        }

        if ($element->hasAttribute('placeholder') && $this->translator) {
            $placeholder = $element->getAttribute('placeholder');
            $placeholder = $this->translator->translate($placeholder, [], $this->request->getLanguage());
            $element->setAttribute('placeholder', $placeholder);
        }

        return $element;
    }

    public function hasElement($name)
    {
        $container = $this->getContainer();
        return $container->hasComponent($name);
    }

    public function openLabel($element = null, $attributes = null)
    {
        if ($attributes === null) {
            $attributes = $this->labelAttributes ?: [];
        } elseif (is_string($attributes)) {
            $attributes = $this->parseAttributes($attributes);
        }

        $str = '<label'.$this->implodeAttributes($attributes).'>';

        if ($element) {
            if (is_string($element)) {
                $container = $this->getContainer();
                $element = $container->getComponent($element);
            }

            $label = $element->getLabel();

            if ($this->translator) {
                $label = $this->translator->translate($label, [], $this->request->getLanguage());
                $element->setLabel($label);
            }

            $str .= '<span>'.$label."</span>\n";
        }

        return $str;
    }

    public function closeLabel($element = null)
    {
        $str = '';

        if ($element) {
            if (is_string($element)) {
                $container = $this->getContainer();
                $element = $container->getComponent($element);
            }

            $label = $element->getLabel();

            if ($this->translator) {
                $label = $this->translator->translate($label, [], $this->request->getLanguage());
                $element->setLabel($label);
            }

            $str .= '<span>'.$label.'</span>';
        }

        return $str."</label>\n";
    }

    public function label($element, $attributes = null)
    {
        if (is_string($element)) {
            $container = $this->getContainer();
            $element = $container->getComponent($element);
        }

        $label = $element->getLabel();

        if ($this->translator) {
            $label = $this->translator->translate($label, [], $this->request->getLanguage());
            $element->setLabel($label);
        }

        if ($attributes === null) {
            $attributes = $this->labelAttributes ?: [];
        } elseif (is_string($attributes)) {
            $attributes = $this->parseAttributes($attributes);
        }

        if (!isset($attributes['for'])) {
            $attributes['for'] = $this->hyphenate($element->getAttribute('name')).'-id';
        }

        return $this->openLabel(null, $attributes).$label."</label>\n";
    }

    public function errors($name, $attributes = null)
    {
        if ($attributes === null) {
            $attributes = $this->errorAttributes ?: ['class' => 'errors'];
        } elseif (is_string($attributes)) {
            $attributes = $this->parseAttributes($attributes);
        }

        $container = $this->getContainer();
        $element = $container->getComponent($name);

        $errors = $element->getErrors();

        if ($element->hasErrors()) {
            $str = '<ul'.$this->implodeAttributes($attributes).">\n";
            foreach ($errors as $error) {
                $str .= '<li>'.$error."</li>\n";
            }
            $str .= "</ul>\n";

            return $str;
        }

        return '';
    }

    public function error($name, $attributes = null)
    {
        if ($attributes === null) {
            $attributes = $this->errorAttributes ?: ['class' => 'error'];
        } elseif (is_string($attributes)) {
            $attributes = $this->parseAttributes($attributes);
        }

        $container = $this->getContainer();
        $element = $container->getComponent($name);

        $errors = $element->getErrors();

        if ($element->hasErrors()) {
            return '<span'.$this->implodeAttributes($attributes).'>'.current($errors)."</span>\n";
        }

        return '';
    }

    public function hasErrors($name, $return = null)
    {
        $container = $this->getContainer();
        $element = $container->getComponent($name);

        if ($return) {
            return $element->hasErrors() ? $return : '';
        }

        return $element->hasErrors();
    }

    public function implodeAttributes(array $attributes)
    {
        $s = '';
        foreach ($attributes as $attribute => $value) {
            if ($value === null) {
                continue;
            }
            $s .= ' '.$attribute.'="'.$value.'"';
        }

        return $s;
    }

    private function hyphenate($str)
    {
        $str = preg_replace('/[^a-z0-9]+/i', ' ', $str);
        $str = trim($str);
        $str = str_replace(" ", "-", $str);
        $str = strtolower($str);

        return $str;
    }
}
