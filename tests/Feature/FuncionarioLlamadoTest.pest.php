<?php

namespace Tests\Feature;

use App\Models\Usuario;
use App\Models\Turno;
use App\Models\Meson;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FuncionarioLlamadoTest extends TestCase
{
    use RefreshDatabase;

    public function test_funcionario_puede_llamar_y_finalizar_turno()
    {
        $funcionario = Usuario::factory()->create(['rol' => 'funcionario']);
        $meson = Meson::factory()->create(['nombre' => 'Mesón 1']);
        $turno = Turno::factory()->create(['estado' => 'pendiente']);

        $this->actingAs($funcionario);

        $meson->usuario_id = $funcionario->id;
        $meson->save();

        $response = $this->post(route('turnos.llamar'), [
            'turno_id' => $turno->id,
            'meson_nombre' => $meson->nombre,
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('turnos', [
            'id' => $turno->id,
            'estado' => 'atendiendo',
        ]);

        $response = $this->post(route('turnos.finalizar'), [
            'turno_id' => $turno->id,
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('turnos', [
            'id' => $turno->id,
            'estado' => 'atendido',
        ]);
    }
}
