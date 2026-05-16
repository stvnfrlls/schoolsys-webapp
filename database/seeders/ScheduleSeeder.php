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
    /**
     * Time slots (excluding recess 9:30-10:00 and lunch 12:00-13:00)
     */
    private array $timeSlots = [
        ['07:30:00', '08:30:00'],
        ['08:30:00', '09:30:00'],
        // RECESS: 09:30:00 - 10:00:00 (break)
        ['10:00:00', '11:00:00'],
        ['11:00:00', '12:00:00'],
        // LUNCH: 12:00:00 - 13:00:00 (break)
        ['13:00:00', '14:00:00'],
        ['14:00:00', '15:00:00'],
        ['15:00:00', '16:00:00'],
    ];

    private array $days = [1, 2, 3, 4, 5]; // Mon–Fri

    /**
     * Track faculty schedules to prevent conflicts:
     * [facultyId => [['day' => 1, 'time_start' => '07:30:00'], ...]]
     */
    private array $facultySchedule = [];

    /**
     * Track section schedules to prevent double-booking sections:
     * [sectionId => [['day' => 1, 'time_start' => '07:30:00'], ...]]
     */
    private array $sectionSchedule = [];

    /**
     * Track subject hours per faculty to enforce max 1hr per subject:
     * [facultyId => [subjectId => totalMinutes]]
     */
    private array $facultySubjectHours = [];

    public function run(): void
    {
        $schoolYear = SchoolYear::where('is_active', 'active')->first();
        if (! $schoolYear) {
            $this->command->warn('No active school year found. Skipping ScheduleSeeder.');
            return;
        }

        $allFaculty = Faculty::all();
        if ($allFaculty->isEmpty()) {
            $this->command->warn('No faculty found. Skipping ScheduleSeeder.');
            return;
        }

        $gradeLevels = GradeLevel::orderBy('level')->get();
        if ($gradeLevels->isEmpty()) {
            $this->command->warn('No grade levels found. Skipping ScheduleSeeder.');
            return;
        }

        foreach ($gradeLevels as $gradeLevel) {
            $sections = Section::where('grade_level_id', $gradeLevel->id)
                ->orderBy('id')
                ->get();

            $subjectPerLevels = SubjectPerLevel::where('gradelevel_id', $gradeLevel->id)
                ->where('is_active', 'active')
                ->with('subject')
                ->get();

            $subjects = $subjectPerLevels->pluck('subject')->all();

            if (empty($subjects)) {
                $this->command->warn("No subjects found for {$gradeLevel->name}. Skipping.");
                continue;
            }

            $roomPrefix = $gradeLevel->level * 100;

            foreach ($sections as $sectionIndex => $section) {
                $room = 'Room ' . ($roomPrefix + $sectionIndex + 1);

                // Assign each subject to a time slot
                foreach ($subjects as $subject) {
                    // Find a faculty who can teach this subject at an available time
                    $assignment = $this->findAvailableFaculty(
                        $allFaculty,
                        $subject->id,
                        $section->id
                    );

                    if (! $assignment) {
                        $this->command?->warn(
                            "No available faculty for {$subject->name} in {$gradeLevel->name} / {$section->name}"
                        );
                        continue;
                    }

                    $faculty = $assignment['faculty'];
                    $slot = $assignment['slot'];

                    // Create schedule entries for each day (Mon-Fri)
                    foreach ($this->days as $day) {
                        Schedule::create([
                            'school_year_id' => $schoolYear->id,
                            'section_id'     => $section->id,
                            'subject_id'     => $subject->id,
                            'faculty_id'     => $faculty->id,
                            'day_of_week'    => $day,
                            'time_start'     => $slot['time_start'],
                            'time_end'       => $slot['time_end'],
                            'room'           => $room,
                        ]);
                    }

                    // Mark slot as taken for this faculty and section
                    $this->markSlotTaken($faculty->id, $section->id, $slot['day'], $slot['time_start']);

                    // Track hours per faculty per subject (60 minutes per week)
                    $this->trackSubjectHours($faculty->id, $subject->id, 60);
                }
            }
        }

        $this->command->info('ScheduleSeeder completed successfully.');
    }

    /**
     * Find an available faculty for a given subject/section combo.
     * Returns ['faculty' => Faculty, 'slot' => ['day' => X, 'time_start' => Y, 'time_end' => Z]]
     * or null if no one is available.
     */
    private function findAvailableFaculty($allFaculty, int $subjectId, int $sectionId): ?array
    {
        // Try each faculty in order
        foreach ($allFaculty as $faculty) {
            // Check if faculty has already taught this subject for 1+ hour
            $hoursTeachingSubject = $this->facultySubjectHours[$faculty->id][$subjectId] ?? 0;
            if ($hoursTeachingSubject >= 60) {
                // Faculty has already taught 1hr of this subject
                continue;
            }

            // Find next available slot for this faculty
            $slot = $this->findNextAvailableSlotForFaculty($faculty->id, $sectionId);

            if ($slot) {
                return [
                    'faculty' => $faculty,
                    'slot'    => $slot,
                ];
            }
        }

        return null;
    }

    /**
     * Find the next available time slot for a faculty where they don't have a conflict
     * and the section isn't already booked.
     */
    private function findNextAvailableSlotForFaculty(int $facultyId, int $sectionId): ?array
    {
        foreach ($this->days as $day) {
            foreach ($this->timeSlots as [$timeStart, $timeEnd]) {
                // Check if faculty is busy at this time
                $facultyBusy = in_array(
                    [$day, $timeStart],
                    $this->facultySchedule[$facultyId] ?? []
                );

                // Check if section is booked at this time
                $sectionBusy = in_array(
                    [$day, $timeStart],
                    $this->sectionSchedule[$sectionId] ?? []
                );

                if (! $facultyBusy && ! $sectionBusy) {
                    return [
                        'day'        => $day,
                        'time_start' => $timeStart,
                        'time_end'   => $timeEnd,
                    ];
                }
            }
        }

        return null;
    }

    /** Mark a time slot as taken for faculty and section. */
    private function markSlotTaken(int $facultyId, int $sectionId, int $day, string $timeStart): void
    {
        if (! isset($this->facultySchedule[$facultyId])) {
            $this->facultySchedule[$facultyId] = [];
        }
        if (! isset($this->sectionSchedule[$sectionId])) {
            $this->sectionSchedule[$sectionId] = [];
        }

        $this->facultySchedule[$facultyId][] = [$day, $timeStart];
        $this->sectionSchedule[$sectionId][] = [$day, $timeStart];
    }

    /** Track total hours a faculty teaches a specific subject. */
    private function trackSubjectHours(int $facultyId, int $subjectId, int $minutes): void
    {
        if (! isset($this->facultySubjectHours[$facultyId])) {
            $this->facultySubjectHours[$facultyId] = [];
        }

        if (! isset($this->facultySubjectHours[$facultyId][$subjectId])) {
            $this->facultySubjectHours[$facultyId][$subjectId] = 0;
        }

        $this->facultySubjectHours[$facultyId][$subjectId] += $minutes;
    }
}
