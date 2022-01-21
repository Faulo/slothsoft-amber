<?php
declare(strict_types = 1);
namespace Slothsoft\Amber\ParameterFilters;

use Slothsoft\Core\IO\Sanitizer\FileNameSanitizer;
use Slothsoft\Farah\Module\Asset\ParameterFilterStrategy\AbstractMapParameterFilter;
use Slothsoft\Amber\AmberUser;

class ResourceParameterFilter extends AbstractMapParameterFilter {

    const PARAM_GAME = 'game';

    const PARAM_VERSION = 'version';

    const PARAM_USER = 'user';

    const PARAM_INFOSET_ID = 'infosetId';

    const PARAM_ARCHIVE_ID = 'archiveId';

    const PARAM_FILE_ID = 'fileId';

    protected function createValueSanitizers(): array {
        return [
            self::PARAM_GAME => new FileNameSanitizer('ambermoon'),
            self::PARAM_VERSION => new FileNameSanitizer('Thalion-v1.05-DE'),
            self::PARAM_USER => new FileNameSanitizer((string) AmberUser::getId()),
            self::PARAM_INFOSET_ID => new FileNameSanitizer(''),
            self::PARAM_ARCHIVE_ID => new FileNameSanitizer(''),
            self::PARAM_FILE_ID => new FileNameSanitizer('')
        ];
    }
}

