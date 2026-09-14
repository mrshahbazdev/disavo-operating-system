<?php

namespace Tests\Feature;

use App\Domains\Knowledge\Models\Learning;
use App\Domains\Knowledge\Models\Observation;
use App\Domains\Knowledge\Models\Question;
use App\Domains\Knowledge\States\Learning\Draft;
use App\Domains\Tenancy\Context\TenantContext;
use App\Domains\Tenancy\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class QuickCaptureTest extends TestCase
{
    use DatabaseTransactions;

    protected Tenant $tenant;
    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->tenant = Tenant::create([
            'name' => 'Capture Tenant',
            'slug' => 'capture-tenant',
        ]);
        TenantContext::setTenant($this->tenant);

        $this->user = User::create([
            'name'     => 'Quick Capture User',
            'email'    => 'quick@capture.test',
            'password' => bcrypt('secret'),
        ]);
    }

    public function test_can_capture_observation_quickly(): void
    {
        $response = $this->actingAs($this->user)
            ->postJson('/api/v1/capture', [
                'type'    => 'observation',
                'title'   => 'Late supplier notice on raw materials',
                'content' => 'Notice arrived 2 days before promised delivery window',
                'context' => ['supplier' => 'Supplier XYZ', 'severity' => 'high'],
            ], [
                'X-Tenant-ID' => $this->tenant->id,
            ]);

        $response->assertStatus(201)
            ->assertJsonFragment([
                'type'    => 'observation',
                'message' => 'Successfully captured observation in DOS.',
            ]);

        $this->assertDatabaseHas('observations', [
            'tenant_id' => $this->tenant->id,
            'title'     => 'Late supplier notice on raw materials',
        ]);
    }

    public function test_can_capture_learning_quickly(): void
    {
        $response = $this->actingAs($this->user)
            ->postJson('/api/v1/capture', [
                'type'    => 'learning',
                'title'   => 'Supplier SLA must require 14 days delay notification',
                'content' => 'Empirical delay buffer needed to preserve manufacturing continuity',
            ], [
                'X-Tenant-ID' => $this->tenant->id,
            ]);

        $response->assertStatus(201);

        $learning = Learning::where('title', 'Supplier SLA must require 14 days delay notification')->first();
        $this->assertNotNull($learning);
        $this->assertInstanceOf(Draft::class, $learning->state);
    }

    public function test_can_capture_open_question_quickly(): void
    {
        $response = $this->actingAs($this->user)
            ->postJson('/api/v1/capture', [
                'type'    => 'question',
                'title'   => 'What is the contractual penalty for unnotified supplier delays?',
                'content' => 'Review legal terms with supplier network',
            ], [
                'X-Tenant-ID' => $this->tenant->id,
            ]);

        $response->assertStatus(201);

        $this->assertDatabaseHas('questions', [
            'tenant_id' => $this->tenant->id,
            'title'     => 'What is the contractual penalty for unnotified supplier delays?',
            'status'    => Question::STATUS_OPEN,
        ]);
    }
}
