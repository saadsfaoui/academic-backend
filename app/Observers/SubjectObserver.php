<?php

namespace App\Observers;

use App\Models\Subject;
use Illuminate\Support\Facades\DB;

class SubjectObserver
{
    /**
     * Handle the Subject "created" event.
     *
     * @param  \App\Models\Subject  $subject
     * @return void
     */
   /* public function created(Subject $subject)
 {
    DB::table('grades')->insert([
        'student_name' => $subject->user->name, // Nom de l'utilisateur connecté
        'subject' => $subject->name,
        'score' => $subject->score,
        'quarter' => 'N/A', // Vous pouvez personnaliser
        'created_at' => now(),
        'updated_at' => now(),
    ]);
 }*/

}

