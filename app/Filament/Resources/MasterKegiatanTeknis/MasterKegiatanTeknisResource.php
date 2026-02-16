<?php

namespace App\Filament\Resources\MasterKegiatanTeknis;

use App\Filament\Resources\MasterKegiatanTeknis\Pages\CreateMasterKegiatanTeknis;
use App\Filament\Resources\MasterKegiatanTeknis\Pages\EditMasterKegiatanTeknis;
use App\Filament\Resources\MasterKegiatanTeknis\Pages\ListMasterKegiatanTeknis;
use App\Filament\Resources\MasterKegiatanTeknis\Schemas\MasterKegiatanTeknisForm;
use App\Filament\Resources\MasterKegiatanTeknis\Tables\MasterKegiatanTeknisTable;
use App\Models\MasterKegiatanTeknis;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;


class MasterKegiatanTeknisResource extends Resource
{
    protected static string|UnitEnum|null $navigationGroup = 'Data Master';
    protected static ?string $model = MasterKegiatanTeknis::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'nama_masterkegiatanteknis';

    public static function form(Schema $schema): Schema
    {
        return MasterKegiatanTeknisForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MasterKegiatanTeknisTable::configure($table);
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
            'index' => ListMasterKegiatanTeknis::route('/'),
            'create' => CreateMasterKegiatanTeknis::route('/create'),
            'edit' => EditMasterKegiatanTeknis::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->hasRole('super_admin')?? false;
    }
}
