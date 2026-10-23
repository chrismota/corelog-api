<?php

namespace Tests\Unit;

use App\Enums\ShipmentStatus;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class ShipmentStatusTest extends TestCase
{
    #[DataProvider('allowedTransitions')]
    public function test_it_allows_valid_transitions(ShipmentStatus $current, ShipmentStatus $next): void
    {
        $this->assertTrue(
            $current->canTransitionTo($next)
        );
    }

    #[DataProvider('forbiddenTransitions')]
    public function test_it_rejects_invalid_transitions(ShipmentStatus $current, ShipmentStatus $next): void
    {
        $this->assertFalse(
            $current->canTransitionTo($next)
        );
    }

    public static function allowedTransitions(): array
    {
        return [
            'created to posted' => [
                ShipmentStatus::CREATED,
                ShipmentStatus::POSTED,
            ],
            'created to cancelled' => [
                ShipmentStatus::CREATED,
                ShipmentStatus::CANCELLED,
            ],
            'posted to in transit' => [
                ShipmentStatus::POSTED,
                ShipmentStatus::IN_TRANSIT,
            ],
            'posted to cancelled' => [
                ShipmentStatus::POSTED,
                ShipmentStatus::CANCELLED,
            ],
            'in transit to out for delivery' => [
                ShipmentStatus::IN_TRANSIT,
                ShipmentStatus::OUT_FOR_DELIVERY,
            ],
            'in transit to delayed' => [
                ShipmentStatus::IN_TRANSIT,
                ShipmentStatus::DELAYED,
            ],
            'in transit to delivery problem' => [
                ShipmentStatus::IN_TRANSIT,
                ShipmentStatus::DELIVERY_PROBLEM,
            ],
            'in transit to cancelled' => [
                ShipmentStatus::IN_TRANSIT,
                ShipmentStatus::CANCELLED,
            ],
            'out for delivery to delivered' => [
                ShipmentStatus::OUT_FOR_DELIVERY,
                ShipmentStatus::DELIVERED,
            ],
            'out for delivery to delivery problem' => [
                ShipmentStatus::OUT_FOR_DELIVERY,
                ShipmentStatus::DELIVERY_PROBLEM,
            ],
            'out for delivery to cancelled' => [
                ShipmentStatus::OUT_FOR_DELIVERY,
                ShipmentStatus::CANCELLED,
            ],
            'delayed to in transit' => [
                ShipmentStatus::DELAYED,
                ShipmentStatus::IN_TRANSIT,
            ],
            'delayed to delivery problem' => [
                ShipmentStatus::DELAYED,
                ShipmentStatus::DELIVERY_PROBLEM,
            ],
            'delayed to returned' => [
                ShipmentStatus::DELAYED,
                ShipmentStatus::RETURNED,
            ],
            'delayed to cancelled' => [
                ShipmentStatus::DELAYED,
                ShipmentStatus::CANCELLED,
            ],
            'delivery problem to in transit' => [
                ShipmentStatus::DELIVERY_PROBLEM,
                ShipmentStatus::IN_TRANSIT,
            ],
            'delivery problem to returned' => [
                ShipmentStatus::DELIVERY_PROBLEM,
                ShipmentStatus::RETURNED,
            ],
            'delivery problem to cancelled' => [
                ShipmentStatus::DELIVERY_PROBLEM,
                ShipmentStatus::CANCELLED,
            ],
        ];
    }

    public static function forbiddenTransitions(): array
    {
        return [
            'created to delivered' => [
                ShipmentStatus::CREATED,
                ShipmentStatus::DELIVERED,
            ],
            'posted to delivered' => [
                ShipmentStatus::POSTED,
                ShipmentStatus::DELIVERED,
            ],
            'in transit to delivered' => [
                ShipmentStatus::IN_TRANSIT,
                ShipmentStatus::DELIVERED,
            ],
            'delivered to delivered' => [
                ShipmentStatus::DELIVERED,
                ShipmentStatus::DELIVERED,
            ],
            'delivered to cancelled' => [
                ShipmentStatus::DELIVERED,
                ShipmentStatus::CANCELLED,
            ],
            'delayed to delivered' => [
                ShipmentStatus::DELAYED,
                ShipmentStatus::DELIVERED,
            ],
            'delivery problem to delivered' => [
                ShipmentStatus::DELIVERY_PROBLEM,
                ShipmentStatus::DELIVERED,
            ],
            'returned to in transit' => [
                ShipmentStatus::RETURNED,
                ShipmentStatus::IN_TRANSIT,
            ],
            'returned to cancelled' => [
                ShipmentStatus::RETURNED,
                ShipmentStatus::CANCELLED,
            ],
            'cancelled to posted' => [
                ShipmentStatus::CANCELLED,
                ShipmentStatus::POSTED,
            ],
            'cancelled to cancelled' => [
                ShipmentStatus::CANCELLED,
                ShipmentStatus::CANCELLED,
            ],
        ];
    }
}
