<?php

namespace App\Filament\Resources\Komoditas;

use App\Filament\Resources\Komoditas\Pages\CreateKomoditas;
use App\Filament\Resources\Komoditas\Pages\EditKomoditas;
use App\Filament\Resources\Komoditas\Pages\ListKomoditas;
use App\Filament\Resources\Komoditas\Schemas\KomoditasForm;
use App\Filament\Resources\Komoditas\Tables\KomoditasTable;
use App\Models\Komoditas;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class KomoditasResource extends Resource

{
    protected static ?string $navigationLabel = 'Jenis Usaha/ Komoditas';
    protected static string|UnitEnum|null $navigationGroup = 'Data Master';
    protected static ?string $model = Komoditas::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'nama_komoditas';

    public static function form(Schema $schema): Schema
    {
        return KomoditasForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return KomoditasTable::configure($table);
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
            'index' => ListKomoditas::route('/'),
            'create' => CreateKomoditas::route('/create'),
            'edit' => EditKomoditas::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->hasRole('super_admin')?? false;
    }
    
}
