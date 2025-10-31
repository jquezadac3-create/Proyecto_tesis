<?php

namespace App\Enums;

enum FormaPago: string
{
    case SIN_SISTEMA_FINANCIERO = '01';
    case COMPENSACION_DEUDAS = '15';
    case TARJETA_DEBITO = '16';
    case DINERO_ELECTRONICO = '17';
    case TARJETA_PREPAGO = '18';
    case TARJETA_CREDITO = '19';
    case OTROS_SISTEMA_FINANCIERO = '20';
    case ENDOSO_TITULOS = '21';

    public static function fromCodigo(string|int $codigo): ?self
    {
        foreach (self::cases() as $case) {
            if ($case->value === str_pad((string) $codigo, 2, '0', STR_PAD_LEFT)) {
                return $case;
            }
        }
        return null;
    }

    public function label(): string
    {
        return match ($this) {
            self::SIN_SISTEMA_FINANCIERO => 'SIN UTILIZACIÓN DEL SISTEMA FINANCIERO',
            self::COMPENSACION_DEUDAS => 'COMPENSACIÓN DE DEUDAS',
            self::TARJETA_DEBITO => 'TARJETA DE DÉBITO',
            self::DINERO_ELECTRONICO => 'DINERO ELECTRÓNICO',
            self::TARJETA_PREPAGO => 'TARJETA PREPAGO',
            self::TARJETA_CREDITO => 'TARJETA DE CRÉDITO',
            self::OTROS_SISTEMA_FINANCIERO => 'OTROS CON UTILIZACIÓN DEL SISTEMA FINANCIERO',
            self::ENDOSO_TITULOS => 'ENDOSO DE TÍTULOS',
        };
    }
}