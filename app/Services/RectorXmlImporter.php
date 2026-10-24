<?php
namespace App\Services;

use Illuminate\Support\Facades\DB;
use RuntimeException;

class RectorXmlImporter
{
    public function import(string $path, string $filename, ?int $userId = null): array
    {
        $raw = file_get_contents($path);
        if ($raw === false) throw new RuntimeException('Не удалось прочитать XML.');
        // Rector-College 1.7 exports Windows-1251. After conversion the XML declaration
        // must also be changed, otherwise libxml interprets UTF-8 bytes as Windows-1251.
        $encoding = 'Windows-1251';
        if (preg_match('/<\\?xml[^>]+encoding=["\\']([^"\\']+)["\\']/i', substr($raw, 0, 256), $m)) {
            $encoding = $m[1];
        } elseif (str_starts_with($raw, "\xEF\xBB\xBF") || mb_check_encoding($raw, 'UTF-8')) {
            $encoding = 'UTF-8';
        }
        $utf8 = mb_convert_encoding($raw, 'UTF-8', $encoding);
        $utf8 = preg_replace('/(<\\?xml[^>]+encoding=["\\'])[^"\\']+(["\\'])/i', '$1UTF-8$2', $utf8, 1) ?? $utf8;

        libxml_use_internal_errors(true);
        libxml_clear_errors();
        $xml = simplexml_load_string($utf8, 'SimpleXMLElement', LIBXML_NOCDATA | LIBXML_NONET);
        if (!$xml) {
            $errors = array_map(fn($e) => trim($e->message).' (строка '.$e->line.')', libxml_get_errors());
            libxml_clear_errors();
            throw new RuntimeException('Некорректный XML Rector-College: '.implode('; ', array_slice($errors, 0, 3)));
        }
        if ($xml->getName() !== 'timetable' || (string)($xml->format['name'] ?? '') !== 'Rector-College') {
            throw new RuntimeException('Файл не является выгрузкой Rector-College.');
        }

        return DB::transaction(function () use ($xml, $raw, $filename, $userId) {
            $begin = $this->date((string)$xml->term->begin_date);
            $end = $this->date((string)$xml->term->end_date);
            $startYear = (int)substr($begin, 0, 4);
            $yearName = $startYear.'/'.($startYear + 1);

            $yearId = DB::table('academic_years')->where('name', $yearName)->value('id');
            if (!$yearId) $yearId = DB::table('academic_years')->insertGetId([
                'name'=>$yearName,'starts_on'=>$startYear.'-08-01','ends_on'=>($startYear+1).'-07-31',
                'is_current'=>0,'is_closed'=>0,'created_at'=>now(),'updated_at'=>now()
            ]);

            $periodNo = (int)substr($begin,5,2) >= 7 ? 1 : 2;
            $periodId = DB::table('academic_periods')->where(['academic_year_id'=>$yearId,'number'=>$periodNo])->value('id');
            $periodData = ['name'=>$periodNo===1?'I полугодие':'II полугодие','starts_on'=>$begin,'ends_on'=>$end,'updated_at'=>now()];
            if ($periodId) DB::table('academic_periods')->where('id',$periodId)->update($periodData);
            else $periodId = DB::table('academic_periods')->insertGetId($periodData + ['academic_year_id'=>$yearId,'number'=>$periodNo,'is_current'=>0,'is_closed'=>0,'created_at'=>now()]);

            $runId = DB::table('import_runs')->insertGetId([
                'academic_period_id'=>$periodId,'user_id'=>$userId,'filename'=>$filename,
                'sha256'=>hash('sha256',$raw),'status'=>'processing','created_at'=>now(),'updated_at'=>now()
            ]);

            foreach ($xml->rooms->room as $r) $this->upsert('rooms',(int)$r->id,[
                'name'=>(string)$r->name,'capacity'=>$this->nullableInt($r->capacity),'building'=>$this->nullableString($r->building)
            ]);
            foreach ($xml->subjects->subject as $s) $this->upsert('subjects',(int)$s->id,[
                'short_name'=>$this->nullableString($s->short_name),'name'=>(string)$s->full_name
            ]);
            foreach ($xml->specialities->speciality as $s) $this->upsert('specialties',(int)$s->id,[
                'code'=>$this->nullableString($s->short_name),'name'=>(string)$s->full_name
            ]);
            foreach ($xml->study_types->study_type as $s) $this->upsert('study_types',(int)$s->id,[
                'name'=>(string)$s->full_name
            ]);
            foreach ($xml->teachers->teacher as $t) {
                $p=$t->person;
                $this->upsert('teachers',(int)$p->id,[
                    'last_name'=>(string)$p->surname,'first_name'=>$this->nullableString($p->first_name),
                    'middle_name'=>$this->nullableString($p->second_name),'active'=>1,'is_visible'=>1
                ]);
            }
            foreach ($xml->classes->class as $c) {
                $specialtyId=DB::table('specialties')->where('source_id',(int)$c->speciality_id)->value('id');
                $this->upsert('study_groups',(int)$c->id,[
                    'name'=>(string)$c->name,'specialty_id'=>$specialtyId,
                    'expected_students'=>$this->nullableInt($c->student),'active'=>1,'is_visible'=>1
                ]);
            }

            // Rector class_id у преподавателя — закреплённая группа (классное руководство).
            foreach ($xml->teachers->teacher as $t) {
                $p=$t->person; $classSource=(int)$t->class_id;
                if ($classSource >= 0) {
                    $teacherId=DB::table('teachers')->where('source_id',(int)$p->id)->value('id');
                    DB::table('study_groups')->where('source_id',$classSource)->update(['curator_teacher_id'=>$teacherId,'updated_at'=>now()]);
                }
            }

            // Праздники храним отдельно и исключаем из генерации занятий.
            foreach ($xml->holidays->day as $h) {
                $dateText = trim((string)$h);
                if ($dateText === '') continue;
                DB::table('calendar_exceptions')->updateOrInsert(
                    ['academic_period_id'=>$periodId,'exception_date'=>$this->date($dateText),'type'=>'holiday'],
                    ['title'=>'Праздничный/неучебный день','updated_at'=>now(),'created_at'=>now()]
                );
            }

            // Замены Rector сохраняем без потери исходных полей. Их применение к конкретным занятиям
            // выполняется отдельным слоем, потому что варианты XML замен различаются между версиями Rector.
            DB::table('schedule_replacements')->where('academic_period_id',$periodId)->delete();
            foreach ($xml->replacements->replacement as $rep) {
                $arr=json_decode(json_encode($rep),true) ?: [];
                $date=null;
                foreach (['date','replacement_date','lesson_date'] as $key) if (!empty($arr[$key])) {$date=$this->date((string)$arr[$key]);break;}
                DB::table('schedule_replacements')->insert([
                    'academic_period_id'=>$periodId,'replacement_date'=>$date,
                    'source_id'=>isset($arr['id'])?(int)$arr['id']:null,'payload'=>json_encode($arr,JSON_UNESCAPED_UNICODE),
                    'created_at'=>now(),'updated_at'=>now()
                ]);
            }

            $n=1;
            foreach ($xml->times->time as $tm) {
                $timeText = trim((string)$tm);
                if (!preg_match('/(\\d{1,2}:\\d{2})\\s*-\\s*(\\d{1,2}:\\d{2})/', $timeText, $m)) continue;
                DB::table('lesson_times')->updateOrInsert(
                    ['academic_period_id'=>$periodId,'lesson_number'=>$n++],
                    ['starts_at'=>$m[1].':00','ends_at'=>$m[2].':00','created_at'=>now(),'updated_at'=>now()]
                );
            }

            // Новый импорт деактивирует только старую версию расписания. Журнал не трогаем.
            DB::table('schedule_entries')->where('academic_period_id',$periodId)->update(['active'=>0,'updated_at'=>now()]);

            $loads=[];
            DB::table('teaching_load_plans')->where('academic_period_id',$periodId)->delete();
            foreach ($xml->loads->load as $load) {
                $classSource=$this->firstListInt($load->klass_id_list);
                $details=[];
                foreach ($load->groups->group as $g) $details[]=[
                    'teacher'=>(int)$g->teacher_id,'subject'=>(int)$g->subject_id,'study_type'=>(int)$g->study_type_id
                ];
                $loads[(int)$load->id]=['class'=>$classSource,'details'=>$details];
                $groupId=$classSource===null?null:DB::table('study_groups')->where('source_id',$classSource)->value('id');
                foreach($details as $subgroup=>$d){$g=$load->groups->group[$subgroup];$teacherId=DB::table('teachers')->where('source_id',$d['teacher'])->value('id');$subjectId=DB::table('subjects')->where('source_id',$d['subject'])->value('id');$studyTypeId=DB::table('study_types')->where('source_id',$d['study_type'])->value('id');DB::table('teaching_load_plans')->updateOrInsert(['academic_period_id'=>$periodId,'source_load_id'=>(int)$load->id,'teacher_id'=>$teacherId,'subject_id'=>$subjectId],['study_group_id'=>$groupId,'study_type_id'=>$studyTypeId,'hours_total'=>$this->nullableDecimal($g->hours_total),'hours_week'=>$this->nullableDecimal($g->hours_per_week),'created_at'=>now(),'updated_at'=>now()]);}
            }

            $count=0;
            foreach ($xml->scheds->sched as $scheduleIndex=>$s) {
                $loadId=(int)$s->load_id; $load=$loads[$loadId]??null;
                if (!$load || $load['class']===null) continue;
                $groupId=DB::table('study_groups')->where('source_id',$load['class'])->value('id');
                if (!$groupId) continue;

                // Rector 1.7 stores the load subgroup index in <sched><group>.
                // A sched node has no own <id>, so use its stable file position as source_id.
                $subgroup=(int)$s->group;
                $d=$load['details'][$subgroup]??null;
                if (!$d) continue;
                $teacherId=DB::table('teachers')->where('source_id',$d['teacher'])->value('id');
                $subjectId=DB::table('subjects')->where('source_id',$d['subject'])->value('id');
                $studyTypeId=DB::table('study_types')->where('source_id',$d['study_type'])->value('id');
                $roomId=DB::table('rooms')->where('source_id',(int)$s->room_id)->value('id');
                DB::table('schedule_entries')->updateOrInsert(
                    ['academic_period_id'=>$periodId,'source_id'=>$scheduleIndex + 1],
                    ['subgroup'=>$subgroup,'source_load_id'=>$loadId,'study_group_id'=>$groupId,'teacher_id'=>$teacherId,'subject_id'=>$subjectId,
                     'room_id'=>$roomId,'study_type_id'=>$studyTypeId,'weekday'=>(int)$s->day,'lesson_number'=>(int)$s->hour,
                     'starts_on'=>$this->date((string)$s->begin_date),'ends_on'=>$this->date((string)$s->end_date),
                     'active'=>1,'import_run_id'=>$runId,'created_at'=>now(),'updated_at'=>now()]
                );
                $count++;
            }

            DB::table('import_runs')->where('id',$runId)->update([
                'status'=>'done','stats'=>json_encode(['schedule_entries'=>$count],JSON_UNESCAPED_UNICODE),'updated_at'=>now()
            ]);
            return ['year'=>$yearName,'period'=>$periodNo,'period_id'=>$periodId,'schedule_entries'=>$count];
        });
    }

    private function upsert(string $table,int $sourceId,array $data): void {
        DB::table($table)->updateOrInsert(['source_id'=>$sourceId],$data+['created_at'=>now(),'updated_at'=>now()]);
    }
    private function date(string $value): string {
        $d=\DateTime::createFromFormat('d.m.Y',trim($value));
        if(!$d) throw new RuntimeException('Некорректная дата в XML: '.$value);
        return $d->format('Y-m-d');
    }
    private function firstListInt($list): ?int {
        if(!$list) return null;
        foreach($list->children() as $item) return (int)$item;
        return null;
    }
    private function nullableDecimal($v): ?float {$s=str_replace(',','.',trim((string)$v));return $s===''?null:(float)$s;}
    private function nullableInt($v): ?int {$s=trim((string)$v);return $s===''?null:(int)$s;}
    private function nullableString($v): ?string {$s=trim((string)$v);return $s===''?null:$s;}
}
