<?php

namespace App\Filament\Resources\DataTeknis;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;



use App\Filament\Resources\DataTeknis\Pages\CreateDataTeknis;
use App\Filament\Resources\DataTeknis\Pages\EditDataTeknis;
use App\Filament\Resources\DataTeknis\Pages\ListDataTeknis;
use App\Filament\Resources\DataTeknis\Schemas\DataTeknisForm;
use App\Filament\Resources\DataTeknis\Tables\DataTeknisTable;
use App\Models\DataTeknis;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class DataTeknisResource extends Resource
{
    protected static ?string $model = DataTeknis::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'nama_datateknis';

    public static function form(Schema $schema): Schema
    {
        return DataTeknisForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DataTeknisTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListDataTeknis::route('/'),
            'create' => CreateDataTeknis::route('/create'),
            'edit' => EditDataTeknis::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        return auth()->user()->hasAnyRole([
            'super_admin',
            'upt',
            'kepala_bidang',
            // 'kepala_dinas',
        ]);
    }

    public static function canCreate(): bool
    {
        return auth()->user()->hasRole(['upt','super_admin']);
    }

    public static function canEdit(Model $record): bool
    {
        return auth()->user()->hasRole(['upt','super_admin']);
    }

    public static function canDelete(Model $record): bool
    {
        return false;
    }

//    public static function getEloquentQuery(): Builder
//     {
//     //     $query = parent::getEloquentQuery();
//     // $user = auth()->user();
//     // if ($user->hasRole('upt')) {
//     //     $query->where('upt_id', $user->upt_id);
//     // }

//     $query = parent::getEloquentQuery();

//     if (auth()->user()->hasRole('upt')) {
//         $query->where('upt_id', auth()->user()->upt_id);
//     }
//     return $query;
//     }


public static function getEloquentQuery(): Builder
{
    $query = parent::getEloquentQuery()
        ->with(['objekProduksi.unitLayanan', 'objekProduksi.upt']);

    $user = auth()->user();

    // =========================
    // ROLE: UPT
    // =========================
    if ($user->hasRole('upt')) {
        $query->whereHas('objekProduksi', function ($q) use ($user) {
            $q->where('upt_id', $user->upt_id);
        });
    }

    // =========================
    // ROLE: KEPALA BIDANG
    // =========================
    if ($user->hasRole('kepala_bidang')) {
        $query->whereHas('objekProduksi.upt', function ($q) use ($user) {
            $q->where('bidang_id', $user->bidang_id);
        });
    }

    return $query;
}





}
