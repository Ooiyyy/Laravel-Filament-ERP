<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\User;
use Filament\Tables;
use Filament\Forms\Get;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use function Laravel\Prompts\select;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\AkunResource\Pages;
use Symfony\Contracts\Service\Attribute\Required;

use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\AkunResource\RelationManagers;

class AkunResource extends Resource
{
    protected static ?string $model = User::class;


    public static function shouldRegisterNavigation(): bool
    {
        return Auth::user()?->role === 'Admin';
    }

    protected static ?string $label = 'Akun';
    protected static ?string $pluralLabel = 'Akun';
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->required(),
                TextInput::make('email')
                    ->required(),
                TextInput::make('password')
                    ->required()
                    ->dehydrateStateUsing(fn($state) => bcrypt($state))
                    ->password()
                    ->dehydrated(fn($state) => filled($state)),
                select::make('role')
                    ->required()
                    ->options([
                        'admin' => 'Admin',
                        'sales' => 'Sales',
                    ]),
                select::make('status')
                    ->required()
                    ->options([
                        'aktif' => 'Aktif',
                        'tidak aktif' => 'Tidak Aktif',
                        'banned' => 'Banned',
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nama'),
                TextColumn::make('email')
                    ->label('E-mail'),
                TextColumn::make('password')
                    ->label('Password'),
                TextColumn::make('role')
                    ->label('Role'),
                TextColumn::make('status')
                    ->label('Status'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
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
            'index' => Pages\ListAkuns::route('/'),
            'create' => Pages\CreateAkun::route('/create'),
            'edit' => Pages\EditAkun::route('/{record}/edit'),
        ];
    }
}
