<?php

namespace Database\Seeders;

use App\Models\Faculty;
use App\Models\GradeLevel;
use App\Models\Schedule;
use App\Models\SchoolYear;
use App\Models\Section;
use App\Models\SubjectPerLevel;
use Illuminate\Database\Seeder;

class ScheduleSeeder extends Seeder
{
    /** Available time slots per day (start, end). */
    private array $timeSlots = [
        ['07:30:00', '08:30:00'],
        ['08:30:00', '09:30:00'],
        ['09:30:00', '10:30:00'],
        ['10:30:00', '11:30:00'],
        ['13:00:00', '14:00:00'],
        ['14:00:00', '15:00:00'],
        ['15:00:00', '16:00:00'],
    ];

    private array $days = [1, 2, 3, 4, 5]; // Mon–Fri

    /**
     * Conflict trackers keyed by [id][day][time_start].
     * facultySlots uses faculty DB id; facultyLoad uses employee_number.
     */
    private array $facultySlots = [];
    private array $sectionSlots = [];

    /** Session count per employee number — used for load balancing. */
    private array $facultyLoad = [];

    // ─────────────────────────────────────────────────────────────────────────
    // Each grade level gets its own pool so shared teachers (Filipino, MAPEH,
    // TLE, AP) are not spread across more grades than they can physically cover.
    //
    // Max capacity per teacher = 7 slots × 5 days = 35 sessions/week.
    // When a subject pool lists >1 employee number, pickFaculty() automatically
    // assigns new sections to the least-loaded teacher.
    //
    // ⚠ EMP-021 … EMP-024 must exist in your FacultySeeder:
    //   EMP-021 → second MAPEH teacher       (covers Grades 9 & 10)
    //   EMP-022 → second Filipino teacher    (covers Grade 9)
    //   EMP-023 → second SHS Math teacher    (covers Grade 12 General Math)
    //   EMP-024 → second SHS Stats teacher   (covers Grade 12 Statistics)
    // ─────────────────────────────────────────────────────────────────────────
    private array $facultyPools = [

        // ── Grade 7 ──────────────────────────────────────────────────────────
        'grade_7' => [
            'Mathematics'                         => ['EMP-001'],
            'Science'                             => ['EMP-005'],
            'English'                             => ['EMP-009'],
            'Filipino'                            => ['EMP-014'],
            'Edukasyon sa Pagpapakatao'            => ['EMP-014'],
            'MAPEH'                               => ['EMP-016'],
            'Araling Panlipunan'                  => ['EMP-017'],
            'Technology and Livelihood Education' => ['EMP-018'],
        ],

        // ── Grade 8 ──────────────────────────────────────────────────────────
        // EMP-014 can still handle Grade 8 Filipino + EdukP because Grade 7
        // only uses ~18 of their 35 slots (3 sections × 2 subjects × 3 hrs).
        // If hours_per_week >= 3 and warnings reappear, swap to EMP-022 below.
        'grade_8' => [
            'Mathematics'                         => ['EMP-001'],
            'Science'                             => ['EMP-005'],
            'English'                             => ['EMP-009'],
            'Filipino'                            => ['EMP-014'],   // swap → EMP-022 if overloaded
            'Edukasyon sa Pagpapakatao'            => ['EMP-014'],   // swap → EMP-022 if overloaded
            'MAPEH'                               => ['EMP-016'],
            'Araling Panlipunan'                  => ['EMP-017'],
            'Technology and Livelihood Education' => ['EMP-018'],
        ],

        // ── Grade 9 ──────────────────────────────────────────────────────────
        // Filipino moved to EMP-022 so EMP-014 is not triple-loaded.
        'grade_9' => [
            'Mathematics'                         => ['EMP-002'],
            'Science'                             => ['EMP-006'],
            'English'                             => ['EMP-010'],
            'Filipino'                            => ['EMP-022'],   // relieved from EMP-014
            'Edukasyon sa Pagpapakatao'            => ['EMP-015'],
            'MAPEH'                               => ['EMP-021'],   // relieved from EMP-016
            'Araling Panlipunan'                  => ['EMP-017'],
            'Technology and Livelihood Education' => ['EMP-018'],
        ],

        // ── Grade 10 ─────────────────────────────────────────────────────────
        'grade_10' => [
            'Mathematics'                         => ['EMP-003'],
            'Science'                             => ['EMP-006'],
            'English'                             => ['EMP-011'],
            'Filipino'                            => ['EMP-015'],
            'Edukasyon sa Pagpapakatao'            => ['EMP-015'],
            'MAPEH'                               => ['EMP-021'],   // relieved from EMP-016
            'Araling Panlipunan'                  => ['EMP-017'],
            'Technology and Livelihood Education' => ['EMP-018'],
        ],

        // ── Grade 11 ─────────────────────────────────────────────────────────
        'grade_11' => [
            'General Mathematics'             => ['EMP-004'],
            'Statistics and Probability'      => ['EMP-004'],
            'Physical Science'                => ['EMP-007'],
            'Earth and Life Science'          => ['EMP-008'],
            'Oral Communication'              => ['EMP-012'],
            'Reading and Writing'             => ['EMP-012'],
            '21st Century Literature'         => ['EMP-013'],
            'Personal Development'            => ['EMP-019'],
            'Media and Information Literacy'  => ['EMP-020'],
        ],

        // ── Grade 12 ─────────────────────────────────────────────────────────
        // EMP-004 handles 2 subjects × 3 sections in Grade 11 (~18 sessions).
        // Grade 12 adds the same load again, so two dedicated teachers split it.
        'grade_12' => [
            'General Mathematics'             => ['EMP-023'],   // relieved from EMP-004
            'Statistics and Probability'      => ['EMP-024'],   // relieved from EMP-004
            'Physical Science'                => ['EMP-007'],
            'Earth and Life Science'          => ['EMP-008'],
            'Oral Communication'              => ['EMP-013'],
            'Reading and Writing'             => ['EMP-013'],
            '21st Century Literature'         => ['EMP-012'],
            'Personal Development'            => ['EMP-019'],
            'Media and Information Literacy'  => ['EMP-020'],
        ],
    ];

    // ─────────────────────────────────────────────────────────────────────────

    public function run(): void
    {
        $schoolYear = SchoolYear::where('is_active', 'active')->first();
        if (! $schoolYear) {
            $this->command->warn('No active school year found. Skipping ScheduleSeeder.');
            return;
        }

        $facultyMap = Faculty::all()->keyBy('employee_number');

        $gradeLevels = GradeLevel::orderBy('level')->get();

        foreach ($gradeLevels as $gradeLevel) {
            $poolKey = $this->getPoolKey($gradeLevel->level);

            if (! isset($this->facultyPools[$poolKey])) {
                $this->command->warn("No faculty pool defined for key '{$poolKey}'. Skipping.");
                continue;
            }

            $sections = Section::where('grade_level_id', $gradeLevel->id)
                ->orderBy('id')
                ->get();

            $subjectPerLevels = SubjectPerLevel::where('gradelevel_id', $gradeLevel->id)
                ->where('is_active', 'active')
                ->with('subject')
                ->get()
                ->sortByDesc('hours_per_week'); // high-frequency subjects pick slots first

            $roomPrefix = $gradeLevel->level * 100;

            foreach ($sections as $sectionIndex => $section) {
                $room = 'Room ' . ($roomPrefix + $sectionIndex + 1);

                foreach ($subjectPerLevels as $spl) {
                    $subject        = $spl->subject;
                    $sessionsNeeded = min($spl->hours_per_week, 5);

                    // Pick the least-loaded eligible faculty member
                    $facultyEmpNo = $this->pickFaculty($this->facultyPools[$poolKey], $subject->name);
                    if (! $facultyEmpNo) {
                        $this->command?->warn(
                            "No faculty mapped: {$gradeLevel->name} / {$section->name} / {$subject->name}"
                        );
                        continue;
                    }

                    $faculty = $facultyMap->get($facultyEmpNo);
                    if (! $faculty) {
                        $this->command?->warn(
                            "Faculty not in DB: {$facultyEmpNo} "
                                . "({$gradeLevel->name} / {$section->name} / {$subject->name})"
                        );
                        continue;
                    }

                    $slots = $this->findAvailableSlots($faculty->id, $section->id, $sessionsNeeded);

                    if (empty($slots)) {
                        $this->command?->warn(
                            "No slot found: {$gradeLevel->name} / {$section->name} / {$subject->name} "
                                . "(faculty: {$facultyEmpNo})"
                        );
                        continue;
                    }

                    foreach ($slots as $slot) {
                        Schedule::create([
                            'school_year_id' => $schoolYear->id,
                            'section_id'     => $section->id,
                            'subject_id'     => $subject->id,
                            'faculty_id'     => $faculty->id,
                            'day_of_week'    => $slot['day'],
                            'time_start'     => $slot['time_start'],
                            'time_end'       => $slot['time_end'],
                            'room'           => $room,
                        ]);

                        $this->facultySlots[$faculty->id][$slot['day']][$slot['time_start']] = true;
                        $this->sectionSlots[$section->id][$slot['day']][$slot['time_start']] = true;
                    }

                    // Track load so pickFaculty can balance across pool members
                    $this->facultyLoad[$facultyEmpNo] =
                        ($this->facultyLoad[$facultyEmpNo] ?? 0) + count($slots);
                }
            }
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Slot finder
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Collect $sessions free (day, time) pairs across the week.
     * Sessions of the same subject may fall on different time slots.
     *
     * @return array<int, array{day: int, time_start: string, time_end: string}>
     */
    private function findAvailableSlots(int $facultyId, int $sectionId, int $sessions): array
    {
        $found = [];

        foreach ($this->days as $day) {
            foreach ($this->timeSlots as [$timeStart, $timeEnd]) {
                if (count($found) >= $sessions) {
                    break 2;
                }

                $facultyBusy = isset($this->facultySlots[$facultyId][$day][$timeStart]);
                $sectionBusy = isset($this->sectionSlots[$sectionId][$day][$timeStart]);

                if (! $facultyBusy && ! $sectionBusy) {
                    $found[] = [
                        'day'        => $day,
                        'time_start' => $timeStart,
                        'time_end'   => $timeEnd,
                    ];
                }
            }
        }

        return count($found) >= $sessions ? $found : [];
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Helpers
    // ─────────────────────────────────────────────────────────────────────────

    /** Map a grade level number to its faculty pool key. */
    private function getPoolKey(int $level): string
    {
        return match ($level) {
            7       => 'grade_7',
            8       => 'grade_8',
            9       => 'grade_9',
            10      => 'grade_10',
            11      => 'grade_11',
            default => 'grade_12',
        };
    }

    /**
     * Return the employee number of the least-loaded eligible faculty member.
     *
     * With a single candidate the behaviour is identical to the original
     * first-pick logic. With multiple candidates it automatically load-balances,
     * so you can list ['EMP-016', 'EMP-021'] and the seeder will spread sections
     * evenly between them without any extra code.
     */
    private function pickFaculty(array $pool, string $subjectName): ?string
    {
        $candidates = $pool[$subjectName] ?? [];
        if (empty($candidates)) {
            return null;
        }

        usort(
            $candidates,
            fn(string $a, string $b) => ($this->facultyLoad[$a] ?? 0) <=> ($this->facultyLoad[$b] ?? 0)
        );

        return $candidates[0];
    }
}
