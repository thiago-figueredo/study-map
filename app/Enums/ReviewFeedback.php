<?php

namespace App\Enums;

enum ReviewFeedback: int
{
    case AGAIN = 0;
    case HARD = 3;
    case MEDIUM = 2;
    case EASY = 1;
}