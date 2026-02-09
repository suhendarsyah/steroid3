<?php

namespace App\Filament\Resources\Bidangs;

use UnitEnum;
use BackedEnum;
use App\Models\Bidang;
use Filament\Tables\Table;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Model;
use App\Filament\Resources\Bidangs\Pages\EditBidang;
use App\Filament\Resources\Bidangs\Pages\ListBidangs;
use App\Filament\Resources\Bidangs\Pages\CreateBidang;
use App\Filament\Resources\Bidangs\Schemas\BidangForm;
use App\Filament\Resources\Bidangs\Tables\BidangsTable;

class BidangResource extends Resource
{
    protected static string|UnitEnum|null $navigationGroup = 'Data Master';


    protected static ?string $model = Bidang::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'nama_bidang';

    public static function form(Schema $schema): Schema
    {
        return BidangForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return BidangsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    // public static function canViewAny(): bool
    // {
    //     return auth()->user()->hasRole('super_admin');
    // }

    public static function getPages(): array
    {
        return [
            'index' => ListBidangs::route('/'),
            'create' => CreateBidang::route('/create'),
            'edit' => EditBidang::route('/{record}/edit'),
        ];
    }

   public static function canViewAny(): bool
    {
        return auth()->user()->hasAnyRole([
            // 'kepala_dinas',
            'perencanaan',
            'super_admin',
        ]);
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit(Model $record): bool
    {
        return false;
    }

    public static function canDelete(Model $record): bool
    {
        return false;
    }



}
