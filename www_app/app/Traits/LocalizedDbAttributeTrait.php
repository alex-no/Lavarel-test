<?php

namespace App\Traits;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;
use App\Exceptions\MissingLocalizedAttributeException;

trait LocalizedDbAttributeTrait
{
    /**
     * Prefix for localized attributes, customizable.
     * Example: "@@" results in `@@title` → `title_en`
     */
    public string $localizedPrefix = '@@';

    public bool $isStrict = true;

    public string $defaultLanguage = 'en';

    /**
     * Get the localized attribute name.
     */
    protected function getLocalizedAttributeName(string $name): string
    {
        if (!str_starts_with($name, $this->localizedPrefix)) {
            return $name;
        }

        $lang = App::getLocale();
        $baseName = substr($name, strlen($this->localizedPrefix));

        $localized = "{$baseName}_{$lang}";

        // Check for the presence of the localized attribute
        if (!array_key_exists($localized, $this->getAttributes()) && !method_exists($this, 'get' . Str::studly($localized) . 'Attribute')) {
            if ($this->isStrict) {
                throw new MissingLocalizedAttributeException($localized);
            }
            $localized = "{$baseName}_{$this->defaultLanguage}";
        }

        return $localized;
    }
}
