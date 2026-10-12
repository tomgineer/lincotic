<?php

namespace App\Services\Editing;

final class EditStatus
{
    public const string CREATED   = 'created';
    public const string UPDATED   = 'updated';
    public const string DELETED   = 'deleted';
    public const string INVALID   = 'invalid';
    public const string NOT_FOUND = 'not_found';
}
