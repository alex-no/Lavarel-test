<?php

namespace App\Traits;

use Illuminate\Support\Facades\App;

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
        if (!in_array($localized, $this->getFillable())) {
            if ($this->isStrict) {
                throw new \Exception("Localized attribute {$localized} not found.");
            }
            $localized = "{$baseName}_{$this->defaultLanguage}";
        }

        return $localized;
    }
}
