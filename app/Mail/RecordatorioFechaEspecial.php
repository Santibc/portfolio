<?php

namespace App\Mail;

use App\Models\FechaEspecialCliente;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class RecordatorioFechaEspecial extends Mailable
{
    use Queueable, SerializesModels;

    public $fechaEspecial;
    public $productosRecomendados;

    /**
     * Create a new message instance.
     */
    public function __construct(FechaEspecialCliente $fechaEspecial, $productosRecomendados = [])
    {
        $this->fechaEspecial = $fechaEspecial;
        $this->productosRecomendados = $productosRecomendados;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject("Recordatorio: {$this->fechaEspecial->nombre} se acerca")
                    ->view('emails.recordatorio-fecha-especial');
    }
}
