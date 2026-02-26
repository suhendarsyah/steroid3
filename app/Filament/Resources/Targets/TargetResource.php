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
use Illuminate\Database\Eloquent\Model;

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

        if (!$user) {
            return $query->whereRaw('1 = 0');
        }

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
        ]) ?? false;
    }

    // 🔹 SIAPA BOLEH CREATE
    public static function canCreate(): bool
    {
        $user = auth()->user();

        return $user
            && $user->hasAnyRole([
                'kepala_bidang',
                'super_admin',
                'perencanaan',
            ]);
    }

    // 🔹 SIAPA BOLEH EDIT
    public static function canEdit(Model $record): bool
    {
        $user = auth()->user();

        if (!$user) {
            return false;
        }

        // super_admin & perencanaan boleh edit semua
        if ($user->hasAnyRole(['super_admin','perencanaan','kepala_bidang'])) {
            return true;
        }

        // kepala_bidang hanya boleh edit miliknya
        if ($user->hasRole('kepala_bidang')) {
            return $record->master_bidang_id === $user->bidang_id;
        }

        return false;
    }

    // 🔹 DELETE DIMATIKAN
    public static function canDelete(Model $record): bool
    {
        return false;
    }
}