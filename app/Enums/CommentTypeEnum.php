<?php

namespace App\Enums;

enum CommentTypeEnum: string
{
    case text = 'text';
    case image = 'image';
    case document = 'document';
    case audio = 'audio';
    case action = 'action';
}
