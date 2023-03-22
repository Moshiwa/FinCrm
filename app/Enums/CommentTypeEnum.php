<?php

namespace App\Enums;

use App\Traits\EnumUpdateTrait;

enum CommentTypeEnum: string
{
    use EnumUpdateTrait;

    case COMMENT = 'comment';
    case ACTION = 'action';
    case DOCUMENT = 'document';
    case REMOTE = 'remote';
    case AUDIO = 'audio';
}
