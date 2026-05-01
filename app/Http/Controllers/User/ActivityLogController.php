<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\DataTables\User\ActivityLogDataTable;

class ActivityLogController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view activity logs')->only(['index']);
    }

    public function index(ActivityLogDataTable $dataTable)
    {
        return $dataTable->render('activitylogs.index');
    }
}
