<?php
declare(strict_types = 1);
namespace Slothsoft\Amber\ParameterFilters;

use Slothsoft\Core\IO\Sanitizer\FileNameSanitizer;

final class DatasetParameterFilter extends ResourceParameterFilter {
    
    protected function createValueSanitizers(): array {
        return [
            self::PARAM_INFOSET_ID => new FileNameSanitizer('')
        ] + parent::createValueSanitizers();
    }
}

