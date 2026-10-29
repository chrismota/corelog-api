<?php

namespace Tests\Feature;

use App\Contexts\OrganizationContext;
use App\DTOs\Order\CreateOrderDTO;
use App\DTOs\Order\CreateOrderItemDTO;
use App\DTOs\Order\IndexOrderDTO;
use App\DTOs\Order\UpdateOrderStatusDTO;
use App\Enums\OrderStatus;
use App\Models\Address;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Organization;
use App\Services\OrderService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class OrderServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_creates_an_order_with_correct_totals_and_items(): void
    {
        $organization = Organization::factory()->create();

        app(OrganizationContext::class)->set($organization);

        $customer = Customer::factory()->create([
            'organization_id' => $organization->id,
            'name' => 'Cliente Teste',
        ]);

        $address = Address::factory()->create([
            'customer_id' => $customer->id,
            'street' => 'Rua de Teste',
            'number' => '123',
            'city' => 'São Paulo',
            'state' => 'SP',
        ]);

        $dto = new CreateOrderDTO(
            customerId: $customer->id,
            addressId: $address->id,
            items: [
                new CreateOrderItemDTO(
                    productName: 'Produto A',
                    quantity: 2,
                    unitPrice: '50.00',
                ),
                new CreateOrderItemDTO(
                    productName: 'Produto B',
                    quantity: 1,
                    unitPrice: '30.00',
                ),
            ],
            shippingCost: '10.00',
            discount: '5.00',
        );

        $service = app(OrderService::class);
        $order = $service->create($dto);

        $this->assertInstanceOf(Order::class, $order);

        $this->assertSame(
            OrderStatus::PENDING,
            $order->status,
        );

        $this->assertSame('130.00', $order->subtotal);
        $this->assertSame('10.00', $order->shipping_cost);
        $this->assertSame('5.00', $order->discount);
        $this->assertSame('135.00', $order->total);

        $this->assertCount(2, $order->items);

        $this->assertSame(
            $organization->id,
            $order->organization_id,
        );

        $this->assertSame(
            $customer->id,
            $order->customer_id,
        );

        $this->assertSame('Cliente Teste', $order->shipping_name);
        $this->assertSame('Rua de Teste', $order->shipping_street);
        $this->assertSame('123', $order->shipping_number);
        $this->assertSame('São Paulo', $order->shipping_city);
        $this->assertSame('SP', $order->shipping_state);

        $this->assertDatabaseHas('order_items', [
            'order_id' => $order->id,
            'product_name' => 'Produto A',
            'quantity' => 2,
        ]);

        $this->assertDatabaseHas('order_items', [
            'order_id' => $order->id,
            'product_name' => 'Produto B',
            'quantity' => 1,
        ]);

        $this->assertDatabaseCount('orders', 1);
        $this->assertDatabaseCount('order_items', 2);
    }

    public function test_it_cannot_create_an_order_for_a_customer_from_another_organization(): void
    {
        $organization = Organization::factory()->create();
        $anotherOrganization = Organization::factory()->create();

        app(OrganizationContext::class)->set($organization);

        $customer = Customer::factory()->create([
            'organization_id' => $anotherOrganization->id,
        ]);

        $address = Address::factory()->create([
            'customer_id' => $customer->id,
        ]);

        $dto = new CreateOrderDTO(
            customerId: $customer->id,
            addressId: $address->id,
            items: [
                new CreateOrderItemDTO(
                    productName: 'Produto A',
                    quantity: 1,
                    unitPrice: '50.00',
                ),
            ],
            shippingCost: '10.00',
            discount: '0.00',
        );

        try {
            app(OrderService::class)->create($dto);

            $this->fail('Era esperado um erro de validação para customer_id.');
        } catch (ValidationException $exception) {
            $this->assertArrayHasKey(
                'customer_id',
                $exception->errors(),
            );
        }

        $this->assertDatabaseCount('orders', 0);
        $this->assertDatabaseCount('order_items', 0);
    }

    public function test_it_cannot_create_an_order_for_an_address_from_another_customer(): void
    {
        $organization = Organization::factory()->create();

        app(OrganizationContext::class)->set($organization);

        $customer = Customer::factory()->create([
            'organization_id' => $organization->id,
        ]);

        $anotherCustomer = Customer::factory()->create([
            'organization_id' => $organization->id,
        ]);

        $address = Address::factory()->create([
            'customer_id' => $anotherCustomer->id,
        ]);

        $dto = new CreateOrderDTO(
            customerId: $customer->id,
            addressId: $address->id,
            items: [
                new CreateOrderItemDTO(
                    productName: 'Produto A',
                    quantity: 1,
                    unitPrice: '50.00',
                ),
            ],
            shippingCost: '10.00',
            discount: '0.00',
        );

        try {
            app(OrderService::class)->create($dto);

            $this->fail('Era esperado um erro de validação para address_id.');
        } catch (ValidationException $exception) {
            $this->assertArrayHasKey(
                'address_id',
                $exception->errors(),
            );
        }

        $this->assertDatabaseCount('orders', 0);
        $this->assertDatabaseCount('order_items', 0);
    }

    public function test_it_cannot_create_an_order_with_discount_greater_than_subtotal(): void
    {
        $organization = Organization::factory()->create();

        app(OrganizationContext::class)->set($organization);

        $customer = Customer::factory()->create([
            'organization_id' => $organization->id,
        ]);

        $address = Address::factory()->create([
            'customer_id' => $customer->id,
        ]);

        $dto = new CreateOrderDTO(
            customerId: $customer->id,
            addressId: $address->id,
            items: [
                new CreateOrderItemDTO(
                    productName: 'Produto A',
                    quantity: 1,
                    unitPrice: '50.00',
                ),
            ],
            shippingCost: '10.00',
            discount: '60.00',
        );

        try {
            app(OrderService::class)->create($dto);

            $this->fail(
                'Era esperado um erro de validação para discount.'
            );
        } catch (ValidationException $exception) {
            $this->assertArrayHasKey(
                'discount',
                $exception->errors(),
            );
        }

        $this->assertDatabaseCount('orders', 0);
        $this->assertDatabaseCount('order_items', 0);
    }

    public function test_it_can_update_order_status_to_processing(): void
    {
        $organization = Organization::factory()->create();

        app(OrganizationContext::class)->set($organization);

        $customer = Customer::factory()->create([
            'organization_id' => $organization->id,
        ]);

        $order = Order::factory()->create([
            'organization_id' => $organization->id,
            'customer_id' => $customer->id,
            'status' => OrderStatus::PENDING,
        ]);

        $dto = new UpdateOrderStatusDTO(
            status: OrderStatus::PROCESSING,
        );

        $updatedOrder = app(OrderService::class)
            ->updateStatus($order, $dto);

        $this->assertSame(
            OrderStatus::PROCESSING,
            $updatedOrder->status,
        );

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => OrderStatus::PROCESSING->value,
        ]);
    }

    public function test_it_can_update_order_status_to_ready_to_ship(): void
    {
        $organization = Organization::factory()->create();

        app(OrganizationContext::class)->set($organization);

        $customer = Customer::factory()->create([
            'organization_id' => $organization->id,
        ]);

        $order = Order::factory()->create([
            'organization_id' => $organization->id,
            'customer_id' => $customer->id,
            'status' => OrderStatus::PROCESSING,
        ]);

        $dto = new UpdateOrderStatusDTO(
            status: OrderStatus::READY_TO_SHIP,
        );

        $updatedOrder = app(OrderService::class)->updateStatus($order, $dto);

        $this->assertSame(
            OrderStatus::READY_TO_SHIP,
            $updatedOrder->status,
        );

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => OrderStatus::READY_TO_SHIP->value,
        ]);
    }

    public function test_it_can_cancel_a_pending_order(): void
    {
        $organization = Organization::factory()->create();

        app(OrganizationContext::class)->set($organization);

        $customer = Customer::factory()->create([
            'organization_id' => $organization->id,
        ]);

        $order = Order::factory()->create([
            'organization_id' => $organization->id,
            'customer_id' => $customer->id,
            'status' => OrderStatus::PENDING,
        ]);

        $dto = new UpdateOrderStatusDTO(
            status: OrderStatus::CANCELLED,
        );

        $updatedOrder = app(OrderService::class)->updateStatus($order, $dto);

        $this->assertSame(OrderStatus::CANCELLED, $updatedOrder->status);

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => OrderStatus::CANCELLED->value,
        ]);
    }

    public function test_it_cannot_update_order_status_directly_to_shipped(): void
    {
        $organization = Organization::factory()->create();

        app(OrganizationContext::class)->set($organization);

        $customer = Customer::factory()->create([
            'organization_id' => $organization->id,
        ]);

        $order = Order::factory()->create([
            'organization_id' => $organization->id,
            'customer_id' => $customer->id,
            'status' => OrderStatus::READY_TO_SHIP,
        ]);

        $dto = new UpdateOrderStatusDTO(
            status: OrderStatus::SHIPPED,
        );

        try {
            app(OrderService::class)->updateStatus($order, $dto);

            $this->fail('Era esperada uma ValidationException.');
        } catch (\Illuminate\Validation\ValidationException $exception) {
            $this->assertArrayHasKey('status', $exception->errors());
        }

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => OrderStatus::READY_TO_SHIP->value,
        ]);
    }

    public function test_it_cannot_update_order_status_directly_to_completed(): void
    {
        $organization = Organization::factory()->create();

        app(OrganizationContext::class)->set($organization);

        $customer = Customer::factory()->create([
            'organization_id' => $organization->id,
        ]);

        $order = Order::factory()->create([
            'organization_id' => $organization->id,
            'customer_id' => $customer->id,
            'status' => OrderStatus::SHIPPED,
        ]);

        $dto = new UpdateOrderStatusDTO(
            status: OrderStatus::COMPLETED,
        );

        try {
            app(OrderService::class)->updateStatus($order, $dto);

            $this->fail('Era esperada uma ValidationException.');
        } catch (ValidationException $exception) {
            $this->assertArrayHasKey('status', $exception->errors());
        }

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => OrderStatus::SHIPPED->value,
        ]);
    }

    public function test_it_cannot_transition_order_to_an_invalid_status(): void
    {
        $organization = Organization::factory()->create();

        app(OrganizationContext::class)->set($organization);

        $customer = Customer::factory()->create([
            'organization_id' => $organization->id,
        ]);

        $order = Order::factory()->create([
            'organization_id' => $organization->id,
            'customer_id' => $customer->id,
            'status' => OrderStatus::PENDING,
        ]);

        $dto = new UpdateOrderStatusDTO(
            status: OrderStatus::COMPLETED,
        );

        try {
            app(OrderService::class)->updateStatus($order, $dto);

            $this->fail('Era esperada uma ValidationException.');
        } catch (\Illuminate\Validation\ValidationException $exception) {
            $this->assertArrayHasKey('status', $exception->errors());
        }

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => OrderStatus::PENDING->value,
        ]);
    }

    public function test_it_cannot_update_a_cancelled_order(): void
    {
        $organization = Organization::factory()->create();

        app(OrganizationContext::class)->set($organization);

        $customer = Customer::factory()->create([
            'organization_id' => $organization->id,
        ]);

        $order = Order::factory()->create([
            'organization_id' => $organization->id,
            'customer_id' => $customer->id,
            'status' => OrderStatus::CANCELLED,
        ]);

        $dto = new UpdateOrderStatusDTO(
            status: OrderStatus::PROCESSING,
        );

        try {
            app(OrderService::class)->updateStatus($order, $dto);

            $this->fail('Era esperada uma ValidationException.');
        } catch (\Illuminate\Validation\ValidationException $exception) {
            $this->assertArrayHasKey('status', $exception->errors());
        }

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => OrderStatus::CANCELLED->value,
        ]);
    }

    public function test_it_lists_orders_from_current_organization(): void
    {
        $organization = Organization::factory()->create();

        app(OrganizationContext::class)->set($organization);

        $customer = Customer::factory()->create([
            'organization_id' => $organization->id,
        ]);

        Order::factory()
            ->count(3)
            ->create([
                'organization_id' => $organization->id,
                'customer_id' => $customer->id,
            ]);

        $dto = new IndexOrderDTO(
            status: null,
            orderNumber: null,
            customerId: null,
            dateFrom: null,
            dateTo: null,
        );

        $result = app(OrderService::class)->list($dto);

        $this->assertCount(3, $result->items());
        $this->assertSame(15, $result->perPage());

        $this->assertTrue(
            collect($result->items())->every(
                fn (Order $order) =>
                    $order->organization_id === $organization->id
            )
        );

        $this->assertTrue(collect($result->items())->every(
            fn (Order $order) =>
                $order->relationLoaded('customer')
                && $order->relationLoaded('items')
        ));
    }

    public function test_it_does_not_list_orders_from_other_organizations(): void
    {
        $organizationA = Organization::factory()->create();
        $organizationB = Organization::factory()->create();

        app(OrganizationContext::class)->set($organizationA);

        $customerA = Customer::factory()->create([
            'organization_id' => $organizationA->id,
        ]);

        $customerB = Customer::factory()->create([
            'organization_id' => $organizationB->id,
        ]);

        Order::factory()->count(2)->create([
            'organization_id' => $organizationA->id,
            'customer_id' => $customerA->id,
        ]);

        Order::factory()->count(3)->create([
            'organization_id' => $organizationB->id,
            'customer_id' => $customerB->id,
        ]);

        $dto = new IndexOrderDTO(
            status: null,
            orderNumber: null,
            customerId: null,
            dateFrom: null,
            dateTo: null,
        );

        $result = app(OrderService::class)->list($dto);

        $this->assertCount(2, $result->items());

        $this->assertSame(
            2,
            $result->total(),
        );

        $this->assertTrue(collect($result->items())->every(
            fn (Order $order) =>
                $order->organization_id === $organizationA->id
        ));
    }

    public function test_it_filters_orders_by_status(): void
    {
        $organization = Organization::factory()->create();

        app(OrganizationContext::class)->set($organization);

        $customer = Customer::factory()->create([
            'organization_id' => $organization->id,
        ]);

        Order::factory()->count(2)->create([
            'organization_id' => $organization->id,
            'customer_id' => $customer->id,
            'status' => OrderStatus::PENDING,
        ]);

        Order::factory()->create([
            'organization_id' => $organization->id,
            'customer_id' => $customer->id,
            'status' => OrderStatus::PROCESSING,
        ]);

        $dto = new IndexOrderDTO(
            status: OrderStatus::PENDING,
            orderNumber: null,
            customerId: null,
            dateFrom: null,
            dateTo: null,
        );

        $result = app(OrderService::class)->list($dto);

        $this->assertCount(2, $result->items());

        $this->assertTrue(collect($result->items())->every(
            fn (Order $order) =>
                $order->status === OrderStatus::PENDING
        ));
    }

    public function test_it_filters_orders_by_order_number(): void
    {
        $organization = Organization::factory()->create();
        app(OrganizationContext::class)->set($organization);

        $customer = Customer::factory()->create([
            'organization_id' => $organization->id,
        ]);

        Order::factory()->create([
            'organization_id' => $organization->id,
            'customer_id' => $customer->id,
            'order_number' => '2026000001',
        ]);

        Order::factory()->create([
            'organization_id' => $organization->id,
            'customer_id' => $customer->id,
            'order_number' => '2026000002',
        ]);

        $dto = new IndexOrderDTO(
            status: null,
            orderNumber: '2026000001',
            customerId: null,
            dateFrom: null,
            dateTo: null,
        );

        $result = app(OrderService::class)->list($dto);

        $this->assertCount(1, $result->items());
        $this->assertSame('2026000001', $result->items()[0]->order_number);
    }

    public function test_it_filters_orders_by_customer(): void
    {
        $organization = Organization::factory()->create();
        app(OrganizationContext::class)->set($organization);

        $customerA = Customer::factory()->create([
            'organization_id' => $organization->id,
        ]);

        $customerB = Customer::factory()->create([
            'organization_id' => $organization->id,
        ]);

        Order::factory()->count(2)->create([
            'organization_id' => $organization->id,
            'customer_id' => $customerA->id,
        ]);

        Order::factory()->create([
            'organization_id' => $organization->id,
            'customer_id' => $customerB->id,
        ]);

        $dto = new IndexOrderDTO(
            status: null,
            orderNumber: null,
            customerId: $customerA->id,
            dateFrom: null,
            dateTo: null,
        );

        $result = app(OrderService::class)->list($dto);

        $this->assertCount(2, $result->items());

        $this->assertTrue(
            collect($result->items())->every(
                fn (Order $order) => $order->customer_id === $customerA->id
            )
        );
    }

    public function test_it_filters_orders_by_date_range(): void
    {
        $organization = Organization::factory()->create();
        app(OrganizationContext::class)->set($organization);

        $customer = Customer::factory()->create([
            'organization_id' => $organization->id,
        ]);

        $createOrder = fn (string $date) => Order::factory()->create([
            'organization_id' => $organization->id,
            'customer_id' => $customer->id,
            'created_at' => $date,
            'updated_at' => $date,
        ]);

        $createOrder('2026-01-09 12:00:00');
        $createOrder('2026-01-15 12:00:00');
        $createOrder('2026-01-21 12:00:00');

        $dto = new IndexOrderDTO(
            status: null,
            orderNumber: null,
            customerId: null,
            dateFrom: '2026-01-10 00:00:00',
            dateTo: '2026-01-20 23:59:59',
        );

        $result = app(OrderService::class)->list($dto);

        $this->assertCount(1, $result->items());
        $this->assertSame(
            '2026-01-15 12:00:00',
            $result->items()[0]->created_at->format('Y-m-d H:i:s'),
        );
    }

    public function test_it_paginates_orders_fifteen_per_page(): void
    {
        $organization = Organization::factory()->create();
        app(OrganizationContext::class)->set($organization);

        $customer = Customer::factory()->create([
            'organization_id' => $organization->id,
        ]);

        Order::factory()->count(17)->create([
            'organization_id' => $organization->id,
            'customer_id' => $customer->id,
        ]);

        $dto = new IndexOrderDTO(
            status: null,
            orderNumber: null,
            customerId: null,
            dateFrom: null,
            dateTo: null,
        );

        $result = app(OrderService::class)->list($dto);

        $this->assertCount(15, $result->items());
        $this->assertSame(17, $result->total());
        $this->assertSame(15, $result->perPage());
        $this->assertSame(1, $result->currentPage());
    }
}
