<?php
namespace App\Filament\Resources;
use App\Models\StudentMovement;
use App\Filament\Resources\StudentMovementResource\Pages;
use Illuminate\Support\Facades\DB;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
class StudentMovementResource extends Resource
{
 protected static ?string $model=StudentMovement::class;
 protected static ?string $navigationGroup='Учебная часть';
 protected static ?string $navigationLabel='Движение студентов';
 protected static ?string $pluralModelLabel='Движение студентов';
 public static function table(Table $table):Table{return $table->columns([
  Tables\Columns\TextColumn::make('effective_on')->label('Дата')->date('d.m.Y')->sortable(),
  Tables\Columns\TextColumn::make('student_id')->label('Студент')->formatStateUsing(function($id){$s=DB::table('students')->find($id);return $s?trim($s->last_name.' '.$s->first_name.' '.$s->middle_name):'—';}),
  Tables\Columns\BadgeColumn::make('type')->label('Операция'),
  Tables\Columns\TextColumn::make('from_group_id')->label('Из группы')->formatStateUsing(fn($id)=>DB::table('study_groups')->where('id',$id)->value('name')?:'—'),
  Tables\Columns\TextColumn::make('to_group_id')->label('В группу')->formatStateUsing(fn($id)=>DB::table('study_groups')->where('id',$id)->value('name')?:'—'),
  Tables\Columns\TextColumn::make('order_number')->label('Приказ')
 ]);}
 public static function getEloquentQuery():\Illuminate\Database\Eloquent\Builder{$q=parent::getEloquentQuery();$p=request()->attributes->get('academic_period');return $p?$q->where('academic_period_id',$p->id):$q;}
 public static function canViewAny():bool{$u=auth()->user();return $u&&($u->isSuperAdmin()||$u->hasRole('admin')||$u->hasRole('head_academic')||$u->canDo('students.manage'));}
 public static function canCreate():bool{return static::canViewAny();}
 public static function canEdit($record):bool{return false;}
 public static function canDelete($record):bool{return false;}
 public static function getPages():array{return['index'=>Pages\ListStudentMovements::route('/'),'create'=>Pages\CreateStudentMovement::route('/create')];}
}