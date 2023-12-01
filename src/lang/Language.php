<?php

namespace RRZU\Validation\Lang;

trait Language
{
    protected $langData;

    public $language;

    public function translate($key)
    {
        $this->default();

        if (!$this->langData && $this->language) {
            $this->load($this->language);
        }

        return $this->langData[$key] ?? '';
    }

    public function default()
    {
        if (!$this->language) {
            $this->language = 'zh_CN';
        }
    }

    public function load($language)
    {
        $this->langData = require __DIR__ . '/' . $language . '/validation.php';
    }
}
