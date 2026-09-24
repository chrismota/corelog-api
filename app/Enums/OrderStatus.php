<?php

namespace App\Enums;

enum OrderStatus: string
{
    case PENDING = 'PENDING';
    case PROCESSING = 'PROCESSING';
    case READY_TO_SHIP = 'READY_TO_SHIP';
    case SHIPPED = 'SHIPPED';
    case COMPLETED = 'COMPLETED';
    case CANCELLED = 'CANCELLED';

    public function canTransitionTo(self $status): bool
    {
        return match ($this) {
            self::PENDING => in_array($status, [
                self::PROCESSING,
                self::CANCELLED,
            ], true),

            self::PROCESSING => in_array($status, [
                self::READY_TO_SHIP,
                self::CANCELLED,
            ], true),

            self::READY_TO_SHIP => in_array($status, [
                self::SHIPPED, 
                self::CANCELLED,
            ], true),

            self::SHIPPED => in_array($status, [
                self::COMPLETED,
                self::CANCELLED,
            ], true),

            self::COMPLETED,
            self::CANCELLED => false,
        };
    }
}
