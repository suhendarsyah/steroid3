<?php

namespace App\Filament\Resources\UnitLayanans;

use App\Filament\Resources\UnitLayanans\Pages\CreateUnitLayanan;
use App\Filament\Resources\UnitLayanans\Pages\EditUnitLayanan;
use App\Filament\Resources\UnitLayanans\Pages\ListUnitLayanans;
use App\Filament\Resources\UnitLayanans\Schemas\UnitLayananForm;
use App\Filament\Resources\UnitLayanans\Tables\UnitLayanansTable;
use App\Models\UnitLayanan;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class UnitLayananResource extends Resource
{
    protected static ?string $model = UnitLayanan::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $navigationLabel = 'Unit Layanan';

    protected static ?string $recordTitleAttribute = 'nama';

    /*
    |--------------------------------------------------------------------------
    | FORM & TABLE
    |--------------------------------------------------------------------------
    */
    public static function form(Schema $schema): Schema
    {
        return UnitLayananForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return UnitLayanansTable::configure($table);
    }

    /*
    |--------------------------------------------------------------------------
    | FILTER DATA BERDASARKAN ROLE
    |--------------------------------------------------------------------------
    */
    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        $user = auth()->user();

        // ===============================
        // ROLE: UPT
        // ===============================
        if ($user->hasRole('upt')) {
            $query->where('upt_id', $user->upt_id);
        }

        // Role lain → lihat semua (default)

        return $query;
    }

    /*
    |--------------------------------------------------------------------------
    | AKSES MENU
    |--------------------------------------------------------------------------
    */
    public static function canViewAny(): bool
    {
        return auth()->user()->hasAnyRole([
            'super_admin',
            // 'upt',
            'perencanaan',
            // tambahkan jika perlu: 'kepala_bidang', 'kepala_dinas'
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | PAGES
    |--------------------------------------------------------------------------
    */
    public static function getPages(): array
    {
        return [
            'index' => ListUnitLayanans::route('/'),
            'create' => CreateUnitLayanan::route('/create'),
            'edit' => EditUnitLayanan::route('/{record}/edit'),
        ];
    }
}
