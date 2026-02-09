<?php

namespace App\Filament\Resources\ObjekProduksis;

use App\Filament\Resources\ObjekProduksis\Pages\CreateObjekProduksi;
use App\Filament\Resources\ObjekProduksis\Pages\EditObjekProduksi;
use App\Filament\Resources\ObjekProduksis\Pages\ListObjekProduksis;
use App\Filament\Resources\ObjekProduksis\Schemas\ObjekProduksiForm;
use App\Filament\Resources\ObjekProduksis\Tables\ObjekProduksisTable;
use App\Models\ObjekProduksi;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ObjekProduksiResource extends Resource
{
    protected static ?string $model = ObjekProduksi::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    // Secara logika ini lebih cocok operasional
    protected static string|UnitEnum|null $navigationGroup = 'Operasional UPT';

    protected static ?string $navigationLabel = 'Objek Produksi';

    protected static ?string $recordTitleAttribute = 'nama';

    protected static ?int $navigationSort = 1;

    /*
    |--------------------------------------------------------------------------
    | FORM & TABLE
    |--------------------------------------------------------------------------
    */
    public static function form(Schema $schema): Schema
    {
        return ObjekProduksiForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ObjekProduksisTable::configure($table);
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

        // Role lain (super_admin, perencanaan, kadis, kabid)
        // melihat semua data (default)

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
            'upt',
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
            'index' => ListObjekProduksis::route('/'),
            'create' => CreateObjekProduksi::route('/create'),
            'edit' => EditObjekProduksi::route('/{record}/edit'),
        ];
    }

    public static function mutateFormDataBeforeCreate(array $data): array
    {
        $data['upt_id'] = auth()->user()->upt_id;

        return $data;
    }
}
