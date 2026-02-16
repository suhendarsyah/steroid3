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
    protected static ?string $modelLabel = 'Laporan Produksi';
    protected static ?string $navigationLabel = 'Laporan Produksi';
    protected static ?string $model = DataTeknis::class;

    protected static ?int $navigationSort = 3;


    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    // protected static ?string $recordTitleAttribute = 'nama_datateknis';
    protected static ?string $recordTitleAttribute = 'id';


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
        return auth()->user()?->hasAnyRole([
            'super_admin',
            'upt',
            'kepala_bidang',
            'kepala_dinas',
        ]) ?? false;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->hasRole(['upt','super_admin']) ?? false;
    }

    public static function canEdit(Model $record): bool
    {
        return auth()->user()?->hasRole(['upt','super_admin']) ?? false;
    }

    public static function canDelete(Model $record): bool
    {
        return false;
    }




public static function getEloquentQuery(): Builder
{
    $query = parent::getEloquentQuery()
        ->with(['objekProduksi', 'kegiatan', 'upt']);

    $user = auth()->user();

    /**
     * 🟣 ROLE UPT → hanya lihat datanya sendiri
     */
    if ($user?->hasRole('upt') && $user->upt_id) {
        $query->where('upt_id', $user->upt_id);
    }

    /**
     * 🔵 ROLE KEPALA BIDANG
     */
    if ($user?->hasRole('kepala_bidang') && $user->bidang_id) {
        $query->whereHas('kegiatan', function ($q) use ($user) {
            $q->where('bidang_id', $user->bidang_id);
        });
    }

    return $query;
}











}
