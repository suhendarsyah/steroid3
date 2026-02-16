<?php

namespace App\Filament\Resources\Users\Schemas;

use App\Models\Bidang;
use App\Models\Upt;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Spatie\Permission\Models\Role;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([

            /*
            |--------------------------------------------------------------------------
            | IDENTITAS DASAR USER
            |--------------------------------------------------------------------------
            */
            TextInput::make('name')
                ->label('Nama User')
                ->required()
                ->maxLength(255),

            TextInput::make('email')
                ->label('Email')
                ->email()
                ->required()
                ->unique(ignoreRecord: true),

            TextInput::make('password')
                ->label('Password')
                ->password()
                ->visibleOn('create')
                ->required()
                ->dehydrateStateUsing(fn ($state) => bcrypt($state)),

            DateTimePicker::make('email_verified_at')
                ->label('Email Terverifikasi')
                ->hiddenOn('create'),

            /*
            |--------------------------------------------------------------------------
            | ROLE (FIELD KONTROL FORM)
            |--------------------------------------------------------------------------
            | Field ini BUKAN kolom database.
            | Dipakai untuk:
            | - Menentukan field Bidang / UPT muncul
            | - Disinkronkan ke Spatie Role saat simpan
            */
            // Select::make('role_name')
            //     ->label('Role')
            //     ->options(
            //         Role::query()->pluck('name', 'name')->toArray()
            //     )
            //     ->required()
            //     ->reactive(),

            Select::make('role_name')
                ->label('Role')
                ->options(
                    Role::query()->pluck('name', 'name')->toArray()
                )
                ->required()
                ->reactive()
                ->dehydrated(false), // 🔥 PENTING


            /*
            |--------------------------------------------------------------------------
            | BIDANG (KHUSUS ROLE: kepala_bidang)
            |--------------------------------------------------------------------------
            */
            Select::make('bidang_id')
                ->label('Bidang')
                ->options(
                    Bidang::query()->pluck('nama', 'id')->toArray()
                )
                ->searchable()
                ->nullable()
                ->visible(fn ($get) => $get('role_name') === 'kepala_bidang')
                ->required(fn ($get) => $get('role_name') === 'kepala_bidang')
                ->helperText('Wajib diisi untuk user Kepala Bidang'),

            /*
            |--------------------------------------------------------------------------
            | UPT (KHUSUS ROLE: upt)
            |--------------------------------------------------------------------------
            */
            Select::make('upt_id')
                ->label('UPT')
                ->options(
                    Upt::query()->pluck('nama', 'id')->toArray()
                )
                ->searchable()
                ->nullable()
                ->visible(fn ($get) => $get('role_name') === 'upt')
                ->required(fn ($get) => $get('role_name') === 'upt')
                ->helperText('Wajib diisi untuk user UPT')
                ->dehydrated(true) ,

        ]);
    }
}
