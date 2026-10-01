<?php

namespace App;

enum Role: string
{
    case Admin = 'admin';
    case Author = 'author';
    case User = 'user';
}
