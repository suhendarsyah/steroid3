<?php

namespace App\Filament\Resources\Pemiliks;

use App\Filament\Resources\Pemiliks\Pages\CreatePemilik;
use App\Filament\Resources\Pemiliks\Pages\EditPemilik;
use App\Filament\Resources\Pemiliks\Pages\ListPemiliks;
use App\Filament\Resources\Pemiliks\Schemas\PemilikForm;
use App\Filament\Resources\Pemiliks\Tables\PemiliksTable;
use App\Models\Pemilik;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class PemilikResource extends Resource
{
    protected static string|UnitEnum|null $navigationGroup = 'Data Master';
    protected static ?string $model = Pemilik::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'nama_pemilik';

    public static function form(Schema $schema): Schema
    {
        return PemilikForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PemiliksTable::configure($table);
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
            'index' => ListPemiliks::route('/'),
            'create' => CreatePemilik::route('/create'),
            'edit' => EditPemilik::route('/{record}/edit'),
        ];
    }


public static function canViewAny(): bool
{
    return auth()->user()->hasAnyRole([
        'kepala_dinas',
        // 'perencanaan',
        'super_admin',
    ]);
}

}
