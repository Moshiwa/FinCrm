<?php

namespace App\Enums;

enum CommentTypeEnum: string
{
    case comment = 'comment';
    case document = 'document';
    case audio = 'audio';
    case action = 'action';
}
