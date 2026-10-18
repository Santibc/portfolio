<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class ResumenMovilRequest extends FormRequest
{
    private const MAX_DIAS = 366;

    /** La autorización la hace el middleware de llave de la app móvil. */
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'desde' => ['nullable', 'date_format:Y-m-d'],
            'hasta' => ['nullable', 'date_format:Y-m-d', 'after_or_equal:desde'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if ($validator->errors()->isNotEmpty()) {
                return;
            }
            if ($this->desde()->diffInDays($this->hasta()) > self::MAX_DIAS) {
                $validator->errors()->add('hasta', 'El rango no puede superar '.self::MAX_DIAS.' días.');
            }
        });
    }

    public function desde(): Carbon
    {
        return $this->filled('desde')
            ? Carbon::createFromFormat('Y-m-d', (string) $this->input('desde'))->startOfDay()
            : Carbon::now()->startOfMonth();
    }

    public function hasta(): Carbon
    {
        return $this->filled('hasta')
            ? Carbon::createFromFormat('Y-m-d', (string) $this->input('hasta'))->endOfDay()
            : Carbon::now()->endOfMonth();
    }
}
