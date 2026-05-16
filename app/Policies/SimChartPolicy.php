<?php

namespace App\Policies;

use App\Models\Sim\SimChart;

class SimChartPolicy
{
    /** Faculty owner OR the student who owns this chart can view */
    public function view($user, SimChart $chart): bool
    {
        // Faculty who owns the assignment
        if ((int) ($chart->assignment->faculty_id ?? 0) === (int) $user->id) return true;
        // Student who owns the chart
        if ((int) $chart->student_id === (int) $user->id) return true;
        return false;
    }

    /** Faculty grading */
    public function grade($user, SimChart $chart): bool
    {
        return (int) ($chart->assignment->faculty_id ?? 0) === (int) $user->id;
    }

    /** Student can update while not submitted/locked and only their own chart */
    public function update($user, SimChart $chart): bool
    {
        if ((int) $chart->student_id !== (int) $user->id) return false;
        return !in_array($chart->status, ['submitted', 'locked'], true);
    }
}
