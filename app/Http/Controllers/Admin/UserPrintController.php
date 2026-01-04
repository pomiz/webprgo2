<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class UserPrintController extends Controller
{
    public function __invoke(Request $request)
    {
        $users = User::query()
            ->orderByDesc('created_at')
            ->get();

        $pdf = Pdf::loadView('print.users', [
            'users' => $users,
            'generatedAt' => now(),
        ])->setPaper('a4', 'portrait');

        return $pdf->stream('users.pdf');
    }
}
