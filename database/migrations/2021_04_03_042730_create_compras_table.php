<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateComprasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('compras', function (Blueprint $table) {
            $table->id();
            $table->integer('fraccion_id')->unsigned();
            $table->integer('producto_id')->unsigned();
            $table->date('fecha_ingreso');
            $table->float('precio_compra');
            $table->float('costo_unitario');
            $table->date('fecha_vencimiento');
            $table->integer('unidades');
            $table->integer('nlote');
            $table->integer('proveedor_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('compras');
    }
}
