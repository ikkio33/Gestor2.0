<?php
namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Turno;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TurnoPendienteTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function muestra_turnos_pendientes_para_funcionario()
    {
        Turno::factory()->create(['estado' => 'pendiente']);
        Turno::factory()->create(['estado' => 'finalizado']);

        $response = $this->get('/funcionario/turnos/pendientes');

        $response->assertStatus(200);
        $response->assertSee('pendiente'); // Si tu vista los muestra directamente
        $this->assertCount(1, Turno::where('estado', 'pendiente')->get());
    }
}
