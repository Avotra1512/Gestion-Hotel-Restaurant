<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $query = ActivityLog::with('user');

        if ($request->filled('module'))  $query->where('module', $request->module);
        if ($request->filled('niveau'))  $query->where('niveau', $request->niveau);
        if ($request->filled('role'))    $query->where('role', $request->role);
        if ($request->filled('search'))  $query->where('description', 'like', '%' . $request->search . '%');

        $logs = $query->latest()->paginate(30)->withQueryString();

        $stats = [
            'total'   => ActivityLog::count(),
            'today'   => ActivityLog::whereDate('created_at', today())->count(),
            'danger'  => ActivityLog::where('niveau', 'danger')->count(),
            'warning' => ActivityLog::where('niveau', 'warning')->count(),
        ];

        return view('admin.logs.index', compact('logs', 'stats'));
    }

    public function purge()
    {
        $nb = ActivityLog::where('created_at', '<', now()->subDays(30))->delete();
        return back()->with('success', $nb . ' logs supprimés (plus de 30 jours).');
    }
}
