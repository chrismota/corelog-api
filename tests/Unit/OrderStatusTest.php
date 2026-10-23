<?php

namespace Tests\Unit;

use App\Enums\OrderStatus;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class OrderStatusTest extends TestCase
{
    #[DataProvider('allowedTransitions')]
    public function test_it_allows_valid_transitions(OrderStatus $current, OrderStatus $next): void
    {
        $this->assertTrue(
            $current->canTransitionTo($next)
        );
    }

    #[DataProvider('forbiddenTransitions')]
    public function test_it_rejects_invalid_transitions(OrderStatus $current, OrderStatus $next): void
    {
        $this->assertFalse(
            $current->canTransitionTo($next)
        );
    }

    public static function allowedTransitions(): array
    {
        return [
            'pending to processing' => [
                OrderStatus::PENDING,
                OrderStatus::PROCESSING,
            ],
            'pending to cancelled' => [
                OrderStatus::PENDING,
                OrderStatus::CANCELLED,
            ],
            'processing to ready to ship' => [
                OrderStatus::PROCESSING,
                OrderStatus::READY_TO_SHIP,
            ],
            'processing to cancelled' => [
                OrderStatus::PROCESSING,
                OrderStatus::CANCELLED,
            ],
            'ready to ship to shipped' => [
                OrderStatus::READY_TO_SHIP,
                OrderStatus::SHIPPED,
            ],
            'ready to ship to cancelled' => [
                OrderStatus::READY_TO_SHIP,
                OrderStatus::CANCELLED,
            ],
            'shipped to completed' => [
                OrderStatus::SHIPPED,
                OrderStatus::COMPLETED,
            ],
            'shipped to cancelled' => [
                OrderStatus::SHIPPED,
                OrderStatus::CANCELLED,
            ],
        ];
    }

    public static function forbiddenTransitions(): array
    {
        return [
            'pending to completed' => [
                OrderStatus::PENDING,
                OrderStatus::COMPLETED,
            ],
            'processing to shipped' => [
                OrderStatus::PROCESSING,
                OrderStatus::SHIPPED,
            ],
            'ready to ship to completed' => [
                OrderStatus::READY_TO_SHIP,
                OrderStatus::COMPLETED,
            ],
            'shipped to processing' => [
                OrderStatus::SHIPPED,
                OrderStatus::PROCESSING,
            ],
            'completed to cancelled' => [
                OrderStatus::COMPLETED,
                OrderStatus::CANCELLED,
            ],
            'completed to completed' => [
                OrderStatus::COMPLETED,
                OrderStatus::COMPLETED,
            ],
            'cancelled to pending' => [
                OrderStatus::CANCELLED,
                OrderStatus::PENDING,
            ],
            'cancelled to cancelled' => [
                OrderStatus::CANCELLED,
                OrderStatus::CANCELLED,
            ],
        ];
    }
}
