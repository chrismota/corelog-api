<?php

namespace App\Enums;

enum ShipmentStatus: string
{
    case CREATED = 'CREATED';
    case POSTED = 'POSTED';
    case IN_TRANSIT = 'IN_TRANSIT';
    case OUT_FOR_DELIVERY = 'OUT_FOR_DELIVERY';
    case DELIVERED = 'DELIVERED';
    case DELAYED = 'DELAYED';
    case DELIVERY_PROBLEM = 'DELIVERY_PROBLEM';
    case RETURNED = 'RETURNED';
    case CANCELLED = 'CANCELLED';

    public function canTransitionTo(self $status): bool
    {
        return match ($this) {
            self::CREATED => in_array($status, [
                self::POSTED,
                self::CANCELLED,
            ], true),

            self::POSTED => in_array($status, [
                self::IN_TRANSIT,
                self::CANCELLED,
            ], true),

            self::IN_TRANSIT => in_array($status, [
                self::OUT_FOR_DELIVERY,
                self::DELAYED,
                self::DELIVERY_PROBLEM,
                self::CANCELLED,
            ], true),

            self::OUT_FOR_DELIVERY => in_array($status, [
                self::DELIVERED,
                self::DELIVERY_PROBLEM,
            ], true),

            self::DELAYED => in_array($status, [
                self::IN_TRANSIT,
                self::CANCELLED,
            ], true),

            self::DELIVERY_PROBLEM => in_array($status, [
                self::IN_TRANSIT,
                self::RETURNED,
                self::CANCELLED,
            ], true),

            self::DELIVERED,
            self::RETURNED,
            self::CANCELLED => false,
        };
    }
}
