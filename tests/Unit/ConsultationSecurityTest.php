<?php

namespace Tests\Unit;

use App\Casts\EncryptedSessionCast;
use App\Models\Tenants\Consultation;
use App\Models\Tenants\Employee;
use App\Repositories\Tenants\ConsultationRepository;
use App\Services\Tenants\ConsultationService;
use Illuminate\Database\Eloquent\Builder;
use Mockery;
use ReflectionMethod;
use Tests\TestCase;

class ConsultationSecurityTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();

        parent::tearDown();
    }

    public function test_consultation_sensitive_fields_are_encrypted_at_rest_and_decrypted_for_the_model(): void
    {
        $cast = new EncryptedSessionCast();
        $model = new Consultation();
        $plainText = 'Paciente relatou crise de ansiedade antes da sessao';

        $encrypted = $cast->set($model, 'clinical_observations', $plainText, []);

        $this->assertStringStartsWith('enc_session:v2:', $encrypted);
        $this->assertStringNotContainsString($plainText, $encrypted);
        $this->assertSame(
            $plainText,
            $cast->get($model, 'clinical_observations', $encrypted, [])
        );
    }

    public function test_non_admin_consultation_creation_uses_logged_psychologist_as_owner(): void
    {
        $employee = new Employee(['name' => 'Psicologa']);
        $employee->id = 15;

        $this->actingAs($employee, 'employee');
        session(['is_admin' => false]);

        $repository = Mockery::mock(ConsultationRepository::class);
        $repository
            ->shouldReceive('store')
            ->once()
            ->with(Mockery::on(function (array $data) use ($employee) {
                return $data['employee_id'] === $employee->id
                    && $data['scheduled_at'] === '2026-05-15 10:00:00';
            }))
            ->andReturn(new Consultation(['employee_id' => $employee->id]));

        $service = new ConsultationService($repository);

        $consultation = $service->store([
            'tenant_id' => 1,
            'patient_id' => 'patient-id',
            'client_appointment_id' => 10,
            'day' => '2026-05-15',
            'hour' => '10:00:00',
            'employee_id' => 99,
        ]);

        $this->assertSame($employee->id, $consultation->employee_id);
    }

    public function test_repository_scopes_non_admin_queries_to_logged_psychologist(): void
    {
        $employee = new Employee(['name' => 'Psicologa']);
        $employee->id = 22;

        $this->actingAs($employee, 'employee');
        session(['is_admin' => false]);

        $query = Mockery::mock(Builder::class);
        $query
            ->shouldReceive('where')
            ->once()
            ->with('employee_id', $employee->id)
            ->andReturnSelf();
        $query->shouldNotReceive('whereRaw');

        $this->callScopeToCurrentEmployee($query);

        $this->assertTrue(true);
    }

    public function test_repository_blocks_non_admin_queries_when_no_psychologist_is_logged(): void
    {
        session(['is_admin' => false]);

        $query = Mockery::mock(Builder::class);
        $query
            ->shouldReceive('whereRaw')
            ->once()
            ->with('1 = 0')
            ->andReturnSelf();
        $query->shouldNotReceive('where');

        $this->callScopeToCurrentEmployee($query);

        $this->assertTrue(true);
    }

    public function test_repository_does_not_scope_admin_queries_by_psychologist(): void
    {
        session(['is_admin' => true]);

        $query = Mockery::mock(Builder::class);
        $query->shouldNotReceive('where');
        $query->shouldNotReceive('whereRaw');

        $this->callScopeToCurrentEmployee($query);

        $this->assertTrue(true);
    }

    private function callScopeToCurrentEmployee(Builder $query): void
    {
        $repository = app(ConsultationRepository::class);
        $method = new ReflectionMethod($repository, 'scopeToCurrentEmployee');
        $method->setAccessible(true);
        $method->invoke($repository, $query);
    }
}
