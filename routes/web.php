<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Models (inline bindings / queries for closures)
|--------------------------------------------------------------------------
*/
use App\Models\User;

/*
|--------------------------------------------------------------------------
| Controllers
|--------------------------------------------------------------------------
*/
// Auth – shared (Student guard)
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterWithRegcardController;
use App\Http\Controllers\Auth\StudentRegcardController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\NewPasswordController;

// Faculty auth
use App\Http\Controllers\Auth\FacultyLoginController;
use App\Http\Controllers\Auth\FacultyRegisterController;

// Admin auth
use App\Http\Controllers\Auth\AdminLoginController;

// Student app
use App\Http\Controllers\Student\SettingsController;
use App\Http\Controllers\Student\ReturnDemoController;
use App\Http\Controllers\Student\StudentNurseReferenceController;
use App\Http\Controllers\Student\DrugGuideController as StudentDrugGuideController;
use App\Http\Controllers\Student\EmergencyProtocolController as StudentEmergencyProtocolController;
use App\Http\Controllers\Student\WardOrientationController as StudentWardOrientationController;
use App\Http\Controllers\Student\AssessmentGuideController as StudentAssessmentGuideController;
use App\Http\Controllers\Student\StudentSkillMasteryController;
use App\Http\Controllers\Student\CompetencyRequirementController as StudentCompetencyRequirementController;
use App\Http\Controllers\Student\DashboardController;
use App\Http\Controllers\Student\StudentClinicalExperienceController;

// Faculty app
use App\Http\Controllers\Faculty\SettingsController as FacultySettingsController;
use App\Http\Controllers\Faculty\ProfileController;
use App\Http\Controllers\Faculty\ProceduresController;
use App\Http\Controllers\Faculty\DrugGuideController;
use App\Http\Controllers\Faculty\ChartingsController;
use App\Http\Controllers\Faculty\NursingReferencesController;
use App\Http\Controllers\Faculty\EmergencyProtocolController;
use App\Http\Controllers\Faculty\FacultyDashboardController;
use App\Http\Controllers\Faculty\WardOrientationController;
use App\Http\Controllers\Faculty\AssessmentGuideController;
use App\Http\Controllers\Faculty\SkillMasteryController;
use App\Http\Controllers\Faculty\CompetencyRequirementController;
use App\Http\Controllers\Faculty\ClinicalExperienceController;

// Admin app
use App\Http\Controllers\Admin\AdminUsersController;
use App\Http\Controllers\Admin\AdminSettingsController;
use App\Http\Controllers\Admin\FacultyApprovalsPageController;
use App\Http\Controllers\Admin\AdminProcedureController;
use App\Http\Controllers\Admin\AdminTermController;
use App\Http\Controllers\Admin\AdminResourcesPageController;
use App\Http\Controllers\Admin\DrugGuideAdminController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\Admin\AdminReturnDemoSkillController;
use App\Http\Controllers\Admin\NursingReferenceAdminController;
use App\Http\Controllers\Admin\EmergencyProtocolAdminController;
use App\Http\Controllers\Admin\PatientController;

// Files
use App\Http\Controllers\FileStreamController;
/*
|--------------------------------------------------------------------------
| Middleware
|--------------------------------------------------------------------------
*/
use App\Http\Middleware\EnsureStudentActiveForCurrentTerm;
use App\Http\Middleware\EnsureFacultyApproved;

/*
|--------------------------------------------------------------------------
| Feature flags
|--------------------------------------------------------------------------
*/
$designMode = (bool) env('DESIGN_MODE', true); // set false in production


/*
|--------------------------------------------------------------------------
| Landing / Home (guard-aware)
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    if (Auth::guard('admin')->check())
        return redirect()->route('admin.dashboard');
    if (Auth::guard('faculty')->check())
        return redirect()->route('faculty.dashboard');
    if (Auth::check())
        return redirect()->route('student.return_demo.index');

    return view('index');
})->name('home');

Route::view('/try', 'try')->name('try');

Route::middleware('guest')->group(function () {

    // Step 1: Show "Forgot Password" page
    Route::get('/forgot-password', [PasswordResetLinkController::class, 'create'])
        ->name('password.request');

    // Step 2: Handle email submission
    Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])
        ->name('password.email');

    // Step 3: Show "Reset Password" form (from email link)
    Route::get('/reset-password/{token}', [NewPasswordController::class, 'create'])
        ->name('password.reset');

    // Step 4: Handle new password submission
    Route::post('/reset-password', [NewPasswordController::class, 'store'])
        ->name('password.update');
});



/*
|--------------------------------------------------------------------------
| DESIGN MODE (Student) – static views; procedures use real controller
|--------------------------------------------------------------------------
*/
if ($designMode) {
    // ---------------------------
// Student Routes
// ---------------------------
    Route::prefix('student')->name('student.')->group(function () {
        Route::redirect('/', '/student/return-demo')->name('home');

        Route::view('/settings', 'student.students-settings')->name('settings');

        // Nursing References (student)
        Route::get('/nursing-references', [StudentNurseReferenceController::class, 'index'])
            ->name('nursing_references.index');


        Route::prefix('return-demo')->name('return_demo.')->group(function () {
            // Dynamic index
            Route::get('/', [ReturnDemoController::class, 'index'])
                ->name('index');

            // (optional) slugged show route for later
            Route::view('/{skill:slug}', 'student.return-demo.show')
                ->where('skill', '^[A-Za-z0-9\-\_]+$')
                ->name('show');
        });
    });




}


/*
|--------------------------------------------------------------------------
| Student Auth (guest)
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::view('/login', 'auth.login')->name('login');
    Route::view('/register', 'auth.register')->name('register');

    Route::post('/login', [LoginController::class, 'store'])
        ->middleware('throttle:login')
        ->name('login.store');

    Route::post('/register', [RegisterWithRegcardController::class, 'store'])
        ->name('register.store');
});

// Student logout
Route::post('/logout', [LoginController::class, 'destroy'])
    ->middleware('auth:web')
    ->name('logout');

/*
|--------------------------------------------------------------------------
| Student – Authenticated Area
|--------------------------------------------------------------------------
*/
Route::middleware('auth:web')->group(function () {
    /*
    |----------------------------------------------------------------------
    | Student – Revalidation (may NOT yet be active)
    |----------------------------------------------------------------------
    */
    Route::get(
        '/student/regcard/revalidate',
        [StudentRegcardController::class, 'show']
    )->name('student.regcard.revalidate');

    Route::post(
        '/student/regcard/revalidate',
        [StudentRegcardController::class, 'store']
    )->name('student.regcard.upload');

    /*
    |----------------------------------------------------------------------
    | Student – Dashboard
    |----------------------------------------------------------------------
    */
    Route::get('/student', [DashboardController::class, 'index'])
        ->name('student.dashboard');

    /*
    |----------------------------------------------------------------------
    | Student – Settings (profile/password/avatar)
    |----------------------------------------------------------------------
    */
    Route::post(
        '/student/settings/profile',
        [SettingsController::class, 'updateProfile']
    )->name('student.settings.profile');

    Route::post(
        '/student/settings/password',
        [SettingsController::class, 'updatePassword']
    )->name('student.settings.password');

    Route::delete(
        '/student/settings/avatar',
        [SettingsController::class, 'removeAvatar']
    )->name('student.settings.avatar.remove');

    /*
    |----------------------------------------------------------------------
    | Student – Drug Guide
    |----------------------------------------------------------------------
    */
    Route::get(
        '/student/drug-guide',
        [StudentDrugGuideController::class, 'index']
    )->name('student.drugs.index');

    Route::get(
        '/student/drug-guide/data',
        [StudentDrugGuideController::class, 'data']
    )->name('student.drugs.data');

    Route::get(
        '/student/drug-guide/{id}',
        [StudentDrugGuideController::class, 'show']
    )->name('student.drugs.show');

    /*
    |----------------------------------------------------------------------
    | Student – Emergency Protocol Guides
    |----------------------------------------------------------------------
    */
    Route::get(
        '/student/emergency-protocols',
        [StudentEmergencyProtocolController::class, 'index']
    )->name('student.emergency.index');

    Route::get(
        '/student/emergency-protocols/{slug}',
        [StudentEmergencyProtocolController::class, 'show']
    )->name('student.emergency.show');

    /*
    |----------------------------------------------------------------------
    | Student – Ward Orientation
    |----------------------------------------------------------------------
    */
    Route::get(
        '/student/ward-orientation',
        [StudentWardOrientationController::class, 'index']
    )->name('student.wards.index');

    Route::get(
        '/student/ward-orientation/{orientation:slug}',
        [StudentWardOrientationController::class, 'show']
    )->name('student.wards.show');

    /*
    |----------------------------------------------------------------------
    | Student – Assessment Guides
    |----------------------------------------------------------------------
    */
    Route::get(
        '/student/assessment-guides',
        [StudentAssessmentGuideController::class, 'index']
    )->name('student.assessment.index');

    Route::get(
        '/student/assessment-guides/{assessmentGuide}',
        [StudentAssessmentGuideController::class, 'show']
    )->name('student.assessment.show');

    /*
    |----------------------------------------------------------------------
    | Student – Skill Mastery Checklists
    |----------------------------------------------------------------------
    */
    Route::prefix('student/skill-checklists')
        ->name('student.skills.')
        ->controller(StudentSkillMasteryController::class)
        ->group(function () {
            // student.skills.index
            Route::get('/', 'index')->name('index');

            // student.skills.show (slug-based)
            Route::get('/{slug}', 'show')->name('show');
        });

    /*
    |----------------------------------------------------------------------
    | Student – Competency Requirements
    |----------------------------------------------------------------------
    */
    Route::prefix('student/competency-requirements')
        ->name('student.competencies.')
        ->controller(StudentCompetencyRequirementController::class)
        ->group(function () {
            // student.competencies.index
            Route::get('/', 'index')->name('index');

            // student.competencies.show
            Route::get('/{competency}', 'show')
                ->whereNumber('competency')
                ->name('show');
        });

    /*
    |----------------------------------------------------------------------
    | Student – Clinical Experiences (view-only)
    |----------------------------------------------------------------------
    */
    Route::prefix('student/clinical-experiences')
        ->name('student.experiences.')
        ->controller(StudentClinicalExperienceController::class)
        ->group(function () {
            // student.experiences.index  → list of published CI stories
            Route::get('/', 'index')->name('index');

            // student.experiences.show  → read a single story (ID-based for now)
            Route::get('/{experience}', 'show')
                ->whereNumber('experience')
                ->name('show');
        });
});





/*
|--------------------------------------------------------------------------
| File streaming (public disk) — only safe filename part
|--------------------------------------------------------------------------
*/
Route::get('/files/procedures/{path}', [FileStreamController::class, 'procedure'])
    ->where('path', '[A-Za-z0-9][A-Za-z0-9._-]*')
    ->name('files.procedure');

/* ============================================================
 | FACULTY AREA
 |============================================================ */
Route::prefix('faculty')->name('faculty.')->group(function () {

    /* -------------------------
     | AUTH (guest)
     |------------------------- */
    Route::middleware('guest:faculty')->group(function () {
        Route::view('/login', 'auth.faculty-login')->name('login');
        Route::view('/register', 'auth.register-faculty')->name('register');

        Route::post('/login', [FacultyLoginController::class, 'store'])
            ->middleware('throttle:login')
            ->name('login.store');

        Route::post('/register', [FacultyRegisterController::class, 'store'])
            ->name('register.store');
    });

    /* -------------------------
     | SIGNED-IN (pending approval)
     |------------------------- */
    Route::middleware('auth:faculty')->group(function () {
        Route::view('/pending', 'faculty.pending')->name('pending');
    });

    /* -------------------------
     | APPROVED AREA (core CI tools)
     |------------------------- */
    Route::middleware(['auth:faculty', EnsureFacultyApproved::class])->group(function () {

        /* ---- Dashboard ---- */
        Route::get('/dashboard', [FacultyDashboardController::class, 'index'])
            ->name('dashboard');

        /* ---- Instructor Mode landing ---- */
        Route::view('/instructor-mode', 'faculty.instructor-mode.index')
            ->name('instructor-mode.index');

        /* ---- CI Procedures (shortcut) ---- */
        Route::get('/ci-procedures', [ProceduresController::class, 'index'])
            ->name('ci_procedures');

        /* ---- Procedures CRUD ---- */
        Route::controller(ProceduresController::class)
            ->prefix('procedures')
            ->name('procedures.')
            ->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('/create', 'create')->name('create');
                Route::post('/', 'store')->name('store');
                Route::get('/import', 'import')->name('import');
                Route::get('/{slug}/assist', 'assist')->name('assist');
                Route::get('/{slug}/edit', 'edit')->name('edit');
                Route::match(['post', 'put'], '/{slug}', 'update')->name('update'); // ⬅️ changed
                Route::get('/{slug}', 'show')->name('show');
            });


        /* ---- Alias to procedures ---- */
        Route::redirect('/skills', '/faculty/procedures')->name('skills.index');

        /* ---- Drug Guide ---- */
        Route::prefix('drug-guide')->name('drug_guide.')->group(function () {
            Route::get('/', [DrugGuideController::class, 'index'])->name('index');
            Route::get('/data', [DrugGuideController::class, 'data'])->name('data'); // must be before {id}
            Route::get('/create', [DrugGuideController::class, 'create'])->name('create');
            Route::post('/', [DrugGuideController::class, 'store'])->name('store');
            Route::get('/{id}/edit', [DrugGuideController::class, 'edit'])
                ->whereNumber('id')->name('edit');
            Route::put('/{id}', [DrugGuideController::class, 'update'])
                ->whereNumber('id')->name('update');
            Route::post('/{id}/enrich', [DrugGuideController::class, 'enrich'])
                ->whereNumber('id')->name('enrich');
            Route::get('/{id}', [DrugGuideController::class, 'show'])
                ->whereNumber('id')->name('show');
        });

        /* ⚠️ Emergency Protocols (replaces SimChart) ---- */
        Route::prefix('emergency-protocols')
            ->name('emergency.')
            ->controller(EmergencyProtocolController::class)
            ->group(function () {
                // List all protocols (faculty-owned)
                Route::get('/', 'index')->name('index');

                // Create new protocol
                Route::get('/create', 'create')->name('create');
                Route::post('/', 'store')->name('store');

                // Edit/update protocol (slug-based like Procedures)
                Route::get('/{slug}/edit', 'edit')->name('edit');
                Route::put('/{slug}', 'update')->name('update');

                // Archive/unarchive + archives listing
                Route::patch('/{slug}/archive', 'archive')->name('archive');
                Route::get('/archives', 'archivesIndex')->name('archives.index');

                // View protocol details
                Route::get('/{slug}', 'show')->name('show');
            });

        /* ---- Chartings (hub + per-patient) ---- */
        Route::prefix('chartings')
            ->name('chartings.')
            ->controller(ChartingsController::class)
            ->group(function () {

                /** 🩺 Main Chartings Hub */
                Route::get('/', 'index')->name('index');                                // Active + discharged patients
                Route::post('/patients', 'storePatient')->name('patients.store');       // Create patient
                Route::patch('/patients/{patient}', 'updatePatient')                    // Update patient
                    ->whereNumber('patient')->name('patients.update');
                Route::get('/patient/{patient}', 'showPatient')                         // Per-patient hub
                    ->whereNumber('patient')->name('patient');
                Route::patch('/patient/{patient}/archive', 'archivePatient')            // Archive
                    ->whereNumber('patient')->name('patients.archive');
                Route::get('/archives', 'archivesIndex')->name('archives.index');       // Archives list
                Route::patch('/patient/{patient}/restore', 'restorePatient')           // Restore
                    ->whereNumber('patient')->name('patients.restore');
                Route::delete('/patient/{patient}', 'destroyPatient')                   // Permanent delete
                    ->whereNumber('patient')->name('patients.destroy');
                Route::get('/create', 'create')->name('create');

                /* =========================================================
                 |  VIEW PAGES — Original 10 Chartings
                 * ======================================================== */
                Route::get('/patient/{patient}/notes', 'notesIndex')
                    ->whereNumber('patient')->name('notes.index');
                Route::get('/patient/{patient}/vitals', 'vitalsIndex')
                    ->whereNumber('patient')->name('vitals.index');
                Route::get('/patient/{patient}/io', 'ioIndex')
                    ->whereNumber('patient')->name('io.index');
                Route::get('/patient/{patient}/mar', 'marIndex')
                    ->whereNumber('patient')->name('mar.index');
                Route::get('/patient/{patient}/ncp', 'ncpIndex')
                    ->whereNumber('patient')->name('ncp.index');
                Route::get('/patient/{patient}/treatment', 'treatmentIndex')
                    ->whereNumber('patient')->name('treatment.index');
                Route::get('/patient/{patient}/assessment', 'assessmentIndex')
                    ->whereNumber('patient')->name('assessment.index');
                Route::get('/patient/{patient}/shift', 'shiftIndex')
                    ->whereNumber('patient')->name('shift.index');
                Route::get('/patient/{patient}/kardex', 'kardexIndex')
                    ->whereNumber('patient')->name('kardex.index');
                Route::get('/patient/{patient}/summary', 'summaryIndex')
                    ->whereNumber('patient')->name('summary.index');

                /* =========================================================
                 |  VIEW PAGES — NEW 7 CHARTINGS
                 * ======================================================== */
                Route::get('/patient/{patient}/diagnostic', 'diagnosticIndex')
                    ->whereNumber('patient')->name('diagnostic.index');
                Route::get('/patient/{patient}/education', 'educationIndex')
                    ->whereNumber('patient')->name('education.index');
                Route::get('/patient/{patient}/medprep', 'medPrepIndex')
                    ->whereNumber('patient')->name('medprep.index');
                Route::get('/patient/{patient}/allergies', 'allergiesIndex')
                    ->whereNumber('patient')->name('allergies.index');
                Route::get('/patient/{patient}/pain', 'painIndex')
                    ->whereNumber('patient')->name('pain.index');
                Route::get('/patient/{patient}/safety', 'safetyIndex')
                    ->whereNumber('patient')->name('safety.index');
                Route::get('/patient/{patient}/neuro', 'neuroIndex')
                    ->whereNumber('patient')->name('neuro.index');

                /* =========================================================
                 |  STORE ENDPOINTS — First 5
                 * ======================================================== */
                Route::post('/patient/{patient}/notes', 'storeNotes')
                    ->whereNumber('patient')->name('notes.store');
                Route::post('/patient/{patient}/vitals', 'storeVitals')
                    ->whereNumber('patient')->name('vitals.store');
                Route::post('/patient/{patient}/io', 'storeIntakeOutput')
                    ->whereNumber('patient')->name('io.store');
                Route::post('/patient/{patient}/mar', 'storeMar')
                    ->whereNumber('patient')->name('mar.store');
                Route::post('/patient/{patient}/ncp', 'storeNcp')
                    ->whereNumber('patient')->name('ncp.store');

                /* =========================================================
                 |  STORE ENDPOINTS — Next 5
                 * ======================================================== */
                Route::post('/patient/{patient}/treatment', 'storeTreatment')
                    ->whereNumber('patient')->name('treatment.store');
                Route::post('/patient/{patient}/assessment', 'storeAssessment')
                    ->whereNumber('patient')->name('assessment.store');
                Route::post('/patient/{patient}/shift', 'storeShift')
                    ->whereNumber('patient')->name('shift.store');
                Route::post('/patient/{patient}/kardex', 'storeKardex')
                    ->whereNumber('patient')->name('kardex.store');
                Route::post('/patient/{patient}/summary', 'storeSummary')
                    ->whereNumber('patient')->name('summary.store');

                /* =========================================================
                 |  STORE ENDPOINTS — NEW 7 CHARTINGS
                 * ======================================================== */
                Route::post('/patient/{patient}/diagnostic', 'storeDiagnostic')
                    ->whereNumber('patient')->name('diagnostic.store');
                Route::post('/patient/{patient}/education', 'storeEducation')
                    ->whereNumber('patient')->name('education.store');
                Route::post('/patient/{patient}/medprep', 'storeMedPrep')
                    ->whereNumber('patient')->name('medprep.store');
                Route::post('/patient/{patient}/allergies', 'storeAllergy')
                    ->whereNumber('patient')->name('allergies.store');
                Route::post('/patient/{patient}/pain', 'storePain')
                    ->whereNumber('patient')->name('pain.store');
                Route::post('/patient/{patient}/safety', 'storeSafety')
                    ->whereNumber('patient')->name('safety.store');
                Route::post('/patient/{patient}/neuro', 'storeNeuro')
                    ->whereNumber('patient')->name('neuro.store');
            });

        /* ---- Profile & Settings ---- */
        if (class_exists(ProfileController::class)) {
            Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
            Route::post('/profile', [ProfileController::class, 'update'])->name('profile.update');
        }

        Route::view('/settings', 'faculty.settings')->name('settings');
        Route::post('/settings/profile', [FacultySettingsController::class, 'updateProfile'])->name('settings.profile');
        Route::delete('/settings/avatar', [FacultySettingsController::class, 'removeAvatar'])->name('settings.avatar.remove');
        Route::post('/settings/password', [FacultySettingsController::class, 'updatePassword'])->name('settings.password');
        Route::view('/instructor-settings', 'faculty.instructor-settings')->name('instructor.settings');

                Route::get('/knowledge-hub', [NursingReferencesController::class, 'index'])
            ->name('knowledge_hub.index');
        /*
        |------------------------------------------------------------------
        | Instructor Mode – submodules (Ward, Assessment, Skill Mastery, Competencies, Experiences)
        |------------------------------------------------------------------
        */
Route::prefix('instructor-mode')->name('instructor.')->group(function () {

    /* ---- Ward Orientation ---- */
    Route::prefix('ward-orientation')
        ->name('orientation.')
        ->controller(WardOrientationController::class)
        ->group(function () {
            // faculty.instructor.orientation.index
            Route::get('/', 'index')->name('index');
            Route::get('/create', 'create')->name('create');
            Route::post('/', 'store')->name('store');
            Route::get('/{orientation}/edit', 'edit')->name('edit');
            Route::put('/{orientation}', 'update')->name('update');
            Route::post('/{orientation}/archive', 'archive')->name('archive');
            Route::delete('/{orientation}', 'destroy')->name('destroy');
        });

    /*
    |------------------------------------------------------------------
    | Faculty – Assessment Guides (CI Mode)
    |------------------------------------------------------------------
    */
    Route::prefix('assessment-guides')
        ->name('assessment.')
        ->controller(AssessmentGuideController::class)
        ->group(function () {
            // faculty.instructor.assessment.index
            Route::get('/', 'index')->name('index');

            // faculty.instructor.assessment.create
            Route::get('/create', 'create')->name('create');

            // faculty.instructor.assessment.store
            Route::post('/', 'store')->name('store');

            // faculty.instructor.assessment.edit
            Route::get('/{assessmentGuide}/edit', 'edit')
                ->whereNumber('assessmentGuide')
                ->name('edit');

            // faculty.instructor.assessment.update
            Route::put('/{assessmentGuide}', 'update')
                ->whereNumber('assessmentGuide')
                ->name('update');

            // faculty.instructor.assessment.destroy
            Route::delete('/{assessmentGuide}', 'destroy')
                ->whereNumber('assessmentGuide')
                ->name('destroy');
        });

    /*
    |------------------------------------------------------------------
    | Faculty – Skill Mastery Checklists (CI Mode)
    |------------------------------------------------------------------
    */
    Route::prefix('skill-mastery')
        ->name('skills.')
        ->controller(SkillMasteryController::class)
        ->group(function () {
            // faculty.instructor.skills.index
            Route::get('/', 'index')->name('index');

            // faculty.instructor.skills.create
            Route::get('/create', 'create')->name('create');

            // faculty.instructor.skills.store
            Route::post('/', 'store')->name('store');

            // faculty.instructor.skills.steps.store
            Route::post('/{slug}/steps', 'storeStep')->name('steps.store');

            // faculty.instructor.skills.edit
            Route::get('/{slug}/edit', 'edit')->name('edit');

            // faculty.instructor.skills.update
            Route::put('/{slug}', 'update')->name('update');

            // faculty.instructor.skills.archive
            Route::post('/{slug}/archive', 'archive')->name('archive');

            // faculty.instructor.skills.show
            Route::get('/{slug}', 'show')->name('show');
        });

    /*
    |------------------------------------------------------------------
    | Faculty – Competency Requirements (CI Mode)
    |------------------------------------------------------------------
    */
    Route::prefix('competencies')
        ->name('competencies.')
        ->controller(CompetencyRequirementController::class)
        ->group(function () {
            // faculty.instructor.competencies.index
            Route::get('/', 'index')->name('index');

            // faculty.instructor.competencies.create
            Route::get('/create', 'create')->name('create');

            // faculty.instructor.competencies.store
            Route::post('/', 'store')->name('store');

            // faculty.instructor.competencies.categories.store  (AJAX create category)
            Route::post('/categories', 'storeCategory')->name('categories.store');

            // faculty.instructor.competencies.edit
            Route::get('/{competency}/edit', 'edit')
                ->whereNumber('competency')
                ->name('edit');

            // faculty.instructor.competencies.update
            Route::put('/{competency}', 'update')
                ->whereNumber('competency')
                ->name('update');

            // faculty.instructor.competencies.destroy (archive)
            Route::delete('/{competency}', 'destroy')
                ->whereNumber('competency')
                ->name('destroy');
        });

    /*
    |------------------------------------------------------------------
    | Faculty – My Clinical Experience (CI Mode)
    |------------------------------------------------------------------
    */
    Route::prefix('experiences')
        ->name('experiences.')
        ->controller(ClinicalExperienceController::class)
        ->group(function () {

            // faculty.instructor.experiences.index
            Route::get('/', 'index')->name('index');

            // faculty.instructor.experiences.create
            Route::get('/create', 'create')->name('create');

            // faculty.instructor.experiences.store
            Route::post('/', 'store')->name('store');

            // faculty.instructor.experiences.show
            Route::get('/{experience}', 'show')
                ->whereNumber('experience')
                ->name('show');

            // faculty.instructor.experiences.edit
            Route::get('/{experience}/edit', 'edit')
                ->whereNumber('experience')
                ->name('edit');

            // faculty.instructor.experiences.update
            Route::put('/{experience}', 'update')
                ->whereNumber('experience')
                ->name('update');

            // faculty.instructor.experiences.archive (soft archive via status = 'archived')
            Route::post('/{experience}/archive', 'archive')
                ->whereNumber('experience')
                ->name('archive');

            // faculty.instructor.experiences.destroy (hard delete story + media)
            Route::delete('/{experience}', 'destroy')
                ->whereNumber('experience')
                ->name('destroy');

            // faculty.instructor.experiences.attachments.destroy (delete ONLY media)
            Route::delete('attachments/{attachment}', 'destroyAttachment')
                ->whereNumber('attachment')
                ->name('attachments.destroy');
        });
});
        /* -------------------------
         | LOGOUT
         |------------------------- */
        Route::post('/logout', [FacultyLoginController::class, 'destroy'])
            ->middleware('auth:faculty')
            ->name('logout');
    });

});







/*
|--------------------------------------------------------------------------
| Admin – Auth + Dashboard + Users + Settings + Procedures
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->name('admin.')->group(function () {

    /* -------------------- Guest (admin) -------------------- */
    Route::middleware('guest:admin')->group(function () {
        Route::view('/login', 'auth.admin-login')->name('login');
        Route::post('/login', [AdminLoginController::class, 'store'])
            ->middleware('throttle:login')
            ->name('login.store');
    });

    /* ---------------- Authenticated (admin) ---------------- */
    Route::middleware('auth:admin')->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
        Route::post('/logout', [AdminLoginController::class, 'destroy'])->name('logout');

        // Faculty approvals
        Route::get('/approvals', [FacultyApprovalsPageController::class, 'index'])->name('faculty.approvals');
        Route::post('/approvals/{id}/approve', [FacultyApprovalsPageController::class, 'approve'])->name('faculty.approve');
        Route::post('/approvals/{id}/reject', [FacultyApprovalsPageController::class, 'reject'])->name('faculty.reject');

        // Resources landing (procedures library page shell)
        Route::get('/resources', [AdminResourcesPageController::class, 'index'])->name('resources.index');

        // ===== DRUG GUIDE (Admin) — simple index shortcut (optional) =====
        Route::get('/drug-guide', [DrugGuideAdminController::class, 'index'])->name('drug_guide.index');

        // ⚙️ Settings
        Route::get('/settings', [AdminSettingsController::class, 'edit'])->name('settings');
        Route::post('/settings/avatar', [AdminSettingsController::class, 'uploadAvatar'])->name('settings.avatar.upload');
        Route::delete('/settings/avatar', [AdminSettingsController::class, 'removeAvatar'])->name('settings.avatar.remove');

        // 👥 Settings → Admin management (Add / Remove Admin)
        Route::post('/settings/add-admin', [AdminSettingsController::class, 'addAdmin'])->name('settings.add-admin');
        Route::delete('/settings/admins/{admin}', [AdminSettingsController::class, 'removeAdmin'])->name('settings.remove-admin');

        // Terms
        Route::post('/terms/change', [AdminTermController::class, 'change'])
            ->middleware('throttle:6,1')
            ->name('term.change');

        // Users
        $idPattern = '[A-Za-z0-9\-_]+';

        // Place this BEFORE any /users/{id} routes
        Route::get('/users/admins', [AdminUsersController::class, 'admins'])->name('users.admins');

        // other specific paths...
        Route::get('/users/archives', [AdminUsersController::class, 'archives'])->name('users.archives');

        // wildcard/id routes AFTER
        Route::get('/users', [AdminUsersController::class, 'index'])->name('users.index');
        Route::post('/users/{id}/archive', [AdminUsersController::class, 'archive'])->where('id', $idPattern)->name('users.archive');
        Route::get('/users/{id}', [AdminUsersController::class, 'show'])->where('id', $idPattern)->name('users.show');
        Route::post('/users/{id}/restore', [AdminUsersController::class, 'restore'])->where('id', $idPattern)->name('users.restore');
        Route::delete('/users/{id}', [AdminUsersController::class, 'destroy'])->where('id', $idPattern)->name('users.destroy');
        Route::post('/users/{id}/destroy', [AdminUsersController::class, 'destroy'])->where('id', $idPattern)->name('users.destroy.post');
        Route::get('/users/{id}/view', [AdminUsersController::class, 'view'])->where('id', $idPattern)->name('users.view');
        Route::get('/users/{id}/edit', [AdminUsersController::class, 'edit'])->where('id', $idPattern)->name('users.edit');
        Route::post('/users/{id}', [AdminUsersController::class, 'update'])->where('id', $idPattern)->name('users.update');

        // Slug pattern like "tb-dots-screening"
        $procSlug = '[A-Za-z0-9\-]+';

        /* ===================== Procedures ===================== */

        // Active list (non-archived by default; filters via query string)
        Route::get('/procedures', [AdminProcedureController::class, 'index'])
            ->name('procedures.index');

        // Archived list (dedicated page)
        Route::get('/procedures/archives', [AdminProcedureController::class, 'archived'])
            ->name('procedures.archived');

        /* ----- Create / Store ----- */
        Route::get('/procedures/create', [AdminProcedureController::class, 'create'])
            ->name('procedures.create');
        Route::post('/procedures', [AdminProcedureController::class, 'store'])
            ->name('procedures.store');

        /* ----- Status actions ----- */
        Route::patch('/procedures/{procedure}/publish', [AdminProcedureController::class, 'publish'])
            ->where('procedure', $procSlug)
            ->name('procedures.publish');

        Route::patch('/procedures/{procedure}/unpublish', [AdminProcedureController::class, 'unpublish'])
            ->where('procedure', $procSlug)
            ->name('procedures.unpublish');

        /* ----- Archive / Restore (soft) ----- */
        Route::patch('/procedures/{procedure}/archive', [AdminProcedureController::class, 'archive'])
            ->where('procedure', $procSlug)
            ->name('procedures.archive');

        Route::patch('/procedures/{procedure}/restore', [AdminProcedureController::class, 'restore'])
            ->where('procedure', $procSlug)
            ->name('procedures.restore');

        /* ----- Show / Edit / Update / Destroy ----- */
        Route::get('/procedures/{procedure}', [AdminProcedureController::class, 'show'])
            ->where('procedure', $procSlug)
            ->name('procedures.show');

        Route::get('/procedures/{procedure}/edit', [AdminProcedureController::class, 'edit'])
            ->where('procedure', $procSlug)
            ->name('procedures.edit');

        Route::put('/procedures/{procedure}', [AdminProcedureController::class, 'update'])
            ->where('procedure', $procSlug)
            ->name('procedures.update');

        // Hard delete (only allowed if already archived by controller policy)
        Route::delete('/procedures/{procedure}', [AdminProcedureController::class, 'destroy'])
            ->where('procedure', $procSlug)
            ->name('procedures.destroy');

        /* ===================== Return Demo Skills ===================== */

        Route::prefix('return-demo/skills')
            ->name('return_demo.skills.')
            ->where(['skill' => '[A-Za-z0-9\-_]+'])
            ->group(function () {

                /* ===== Lists ===== */
                Route::get('/', [AdminReturnDemoSkillController::class, 'index'])->name('index');
                Route::get('/archives', [AdminReturnDemoSkillController::class, 'archived'])->name('archived');

                /* ===== Picker JSON (for modal) ===== */
                Route::get('/procedures', [AdminReturnDemoSkillController::class, 'procedures'])
                    ->name('procedures');

                /* ===== Import from Procedures (modal “Add Selected”) ===== */
                Route::post('/import-from-procedures', [AdminReturnDemoSkillController::class, 'importFromProcedures'])
                    ->name('import_from_procedures');

                /* ===== (Optional) Manual create/store ===== */
                Route::get('/create', [AdminReturnDemoSkillController::class, 'create'])->name('create');
                Route::post('/', [AdminReturnDemoSkillController::class, 'store'])->name('store');

                /* ===== Status actions ===== */
                Route::patch('/{skill}/publish', [AdminReturnDemoSkillController::class, 'publish'])->name('publish');
                Route::patch('/{skill}/unpublish', [AdminReturnDemoSkillController::class, 'unpublish'])->name('unpublish');

                /* ===== Archive / Restore (soft) ===== */
                Route::patch('/{skill}/archive', [AdminReturnDemoSkillController::class, 'archive'])->name('archive');
                Route::patch('/{skill}/restore', [AdminReturnDemoSkillController::class, 'restore'])->name('restore');

                /* ===== Show / Edit / Update / Destroy ===== */
                Route::get('/{skill}', [AdminReturnDemoSkillController::class, 'show'])->name('show');
                Route::get('/{skill}/edit', [AdminReturnDemoSkillController::class, 'edit'])->name('edit');
                Route::put('/{skill}', [AdminReturnDemoSkillController::class, 'update'])->name('update');
                Route::delete('/{skill}', [AdminReturnDemoSkillController::class, 'destroy'])->name('destroy');
            });

        /* ============== DRUG GUIDE (Admin, full CRUD) ============== */
        Route::prefix('drug-guide')->name('drug_guide.')->group(function () {
            Route::get('/', [DrugGuideAdminController::class, 'index'])->name('index');
            Route::get('/create', [DrugGuideAdminController::class, 'create'])->name('create');
            Route::post('/', [DrugGuideAdminController::class, 'store'])->name('store');
            Route::get('/{product}', [DrugGuideAdminController::class, 'show'])->whereNumber('product')->name('show');
            Route::get('/{product}/edit', [DrugGuideAdminController::class, 'edit'])->whereNumber('product')->name('edit');
            Route::put('/{product}', [DrugGuideAdminController::class, 'update'])->whereNumber('product')->name('update');
            Route::delete('/{product}', [DrugGuideAdminController::class, 'destroy'])->whereNumber('product')->name('destroy');
        });
        /* ======================== ADMINS (Admin) ===================== */
        Route::prefix('admins')->name('admins.')->group(function () {
            Route::get('/', [AdminController::class, 'index'])->name('index');
            Route::get('/create', [AdminController::class, 'create'])->name('create');
            Route::post('/', [AdminController::class, 'store'])->name('store');
            Route::get('/{admin}', [AdminController::class, 'show'])->whereNumber('admin')->name('show');
            Route::get('/{admin}/edit', [AdminController::class, 'edit'])->whereNumber('admin')->name('edit');
            Route::put('/{admin}', [AdminController::class, 'update'])->whereNumber('admin')->name('update');
            Route::delete('/{admin}', [AdminController::class, 'destroy'])->whereNumber('admin')->name('destroy');
        });

        /* ============ NURSING REFERENCES (Admin, full CRUD) ========= */
        Route::prefix('nursing-references')->name('nursing_references.')->group(function () {
            Route::get('/', [NursingReferenceAdminController::class, 'index'])->name('index');
            Route::get('/create', [NursingReferenceAdminController::class, 'create'])->name('create');
            Route::post('/', [NursingReferenceAdminController::class, 'store'])->name('store');
            Route::get('/{reference}', [NursingReferenceAdminController::class, 'show'])->whereNumber('reference')->name('show');
            Route::get('/{reference}/edit', [NursingReferenceAdminController::class, 'edit'])->whereNumber('reference')->name('edit');
            Route::put('/{reference}', [NursingReferenceAdminController::class, 'update'])->whereNumber('reference')->name('update');
            Route::delete('/{reference}', [NursingReferenceAdminController::class, 'destroy'])->whereNumber('reference')->name('destroy');
        });

        /* ========== EMERGENCY PROTOCOLS (Admin, full CRUD) ========= */
        Route::prefix('emergency-protocols')->name('emergency_protocols.')->group(function () {
            Route::get('/', [EmergencyProtocolAdminController::class, 'index'])->name('index');
            Route::get('/create', [EmergencyProtocolAdminController::class, 'create'])->name('create');
            Route::post('/', [EmergencyProtocolAdminController::class, 'store'])->name('store');

            // 🔹 Archives listing page
            Route::get('/archives', [EmergencyProtocolAdminController::class, 'archived'])
                ->name('archived');

            Route::get('/{protocol}', [EmergencyProtocolAdminController::class, 'show'])
                ->whereNumber('protocol')->name('show');
            Route::get('/{protocol}/edit', [EmergencyProtocolAdminController::class, 'edit'])
                ->whereNumber('protocol')->name('edit');
            Route::put('/{protocol}', [EmergencyProtocolAdminController::class, 'update'])
                ->whereNumber('protocol')->name('update');
            Route::delete('/{protocol}', [EmergencyProtocolAdminController::class, 'destroy'])
                ->whereNumber('protocol')->name('destroy');

            // Optional archive/restore if your controller supports it
            Route::patch('/{protocol}/archive', [EmergencyProtocolAdminController::class, 'archive'])
                ->whereNumber('protocol')->name('archive');
            Route::patch('/{protocol}/restore', [EmergencyProtocolAdminController::class, 'restore'])
                ->whereNumber('protocol')->name('restore');
        });
        /* ========== PATIENT DATA (Admin, VIEW + ARCHIVE only) ========= */
        Route::prefix('patient-data')->name('patient_data.')->group(function () {
            Route::get('/', [PatientController::class, 'index'])
                ->name('index');

            Route::get('/archives', [PatientController::class, 'archived'])
                ->name('archived');

            Route::get('/{patient}', [PatientController::class, 'show'])
                ->name('show');

            Route::post('/{patient}/archive', [PatientController::class, 'archive'])
                ->name('archive');
        });

    });
});


// ---------------------------------------------------------
// Public Legal Pages (No Auth Required)
// ---------------------------------------------------------
Route::view('/terms-of-service', 'legal.terms')->name('legal.terms');
Route::view('/privacy-policy', 'legal.privacy')->name('legal.privacy');
Route::view('/cookie-policy', 'legal.cookies')->name('legal.cookies');
/*
|--------------------------------------------------------------------------
| Convenience redirect for /home
|--------------------------------------------------------------------------
*/
if ($designMode) {
    Route::redirect('/home', '/student/dashboard');
} else {
    Route::redirect('/home', '/student/dashboard')->middleware(['auth:web', 'student.active']);
}


/*
|--------------------------------------------------------------------------
| Fallback
|--------------------------------------------------------------------------
*/
Route::fallback(fn() => redirect()->route('home'));