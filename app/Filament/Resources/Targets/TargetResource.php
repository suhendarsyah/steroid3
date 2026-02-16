<?php

namespace App\Filament\Resources\Targets;

use App\Filament\Resources\Targets\Pages\CreateTarget;
use App\Filament\Resources\Targets\Pages\EditTarget;
use App\Filament\Resources\Targets\Pages\ListTargets;
use App\Filament\Resources\Targets\Schemas\TargetForm;
use App\Filament\Resources\Targets\Tables\TargetsTable;
use App\Models\Target;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class TargetResource extends Resource
{
    protected static ?string $model = Target::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'nama_target';

    /**
     * ==============================
     * FORM
     * ==============================
     */
    public static function form(Schema $schema): Schema
    {
        return TargetForm::configure($schema);
    }

    /**
     * ==============================
     * TABLE
     * ==============================
     */
    public static function table(Table $table): Table
    {
        return TargetsTable::configure($table);
    }

    /**
     * ==============================
     * DATA FILTER (SUMBER KEBENARAN)
     * ==============================
     */
    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $user  = auth()->user();

        // 🔓 ROLE STRATEGIS → LIHAT SEMUA DATA
        if ($user->hasAnyRole([
            'super_admin',
            'kepala_dinas',
            'perencanaan',
        ])) {
            return $query;
        }

        // 🔐 KEPALA BIDANG → HANYA TARGET BIDANGNYA
        if ($user->hasRole('kepala_bidang')) {
            return $query->where('master_bidang_id', $user->bidang_id);
        }

        // 🔒 ROLE LAIN (misalnya UPT) → TIDAK MELIHAT DATA
        return $query->whereRaw('1 = 0');
    }

    /**
     * ==============================
     * RELATIONS
     * ==============================
     */
    public static function getRelations(): array
    {
        return [];
    }

    /**
     * ==============================
     * PAGES
     * ==============================
     */
    public static function getPages(): array
    {
        return [
            'index'  => ListTargets::route('/'),
            'create' => CreateTarget::route('/create'),
            'edit'   => EditTarget::route('/{record}/edit'),
        ];
    }

    /**
     * ==============================
     * ACCESS CONTROL (MENU & AKSI)
     * ==============================
     */

    // 🔹 SIAPA BOLEH MELIHAT MENU
    public static function canViewAny(): bool
    {
        return auth()->user()?->hasAnyRole([
            'perencanaan',
            'kepala_dinas',
            'super_admin',
            'kepala_bidang',
        ])?? false;
    }

    // 🔹 SIAPA BOLEH CREATE
    public static function canCreate(): bool
    {
        return auth()->user()?->hasRole('perencanaan')?? false;
    }

    // 🔹 SIAPA BOLEH EDIT
    public static function canEdit(\Illuminate\Database\Eloquent\Model $record): bool
    {
        return auth()->user()?->hasRole('perencanaan')?? false;
    }

    // 🔹 DELETE DIMATIKAN
    public static function canDelete(\Illuminate\Database\Eloquent\Model $record): bool
    {
        return false;
    }


    // untuk menghilangkan tombol new target
    

}
