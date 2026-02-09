<?php

namespace App\Filament\Resources\Upts;

use App\Filament\Resources\Upts\Pages\CreateUpt;
use App\Filament\Resources\Upts\Pages\EditUpt;
use App\Filament\Resources\Upts\Pages\ListUpts;
use App\Filament\Resources\Upts\Schemas\UptForm;
use App\Filament\Resources\Upts\Tables\UptsTable;
use App\Models\Upt;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class UptResource extends Resource
{
     protected static string|UnitEnum|null $navigationGroup = 'Data Master';

    protected static ?string $model = Upt::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'nama_upt';

    public static function form(Schema $schema): Schema
    {
        return UptForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return UptsTable::configure($table);
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
            'index' => ListUpts::route('/'),
            'create' => CreateUpt::route('/create'),
            'edit' => EditUpt::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        return auth()->user()->hasRole('super_admin');
    }
}
