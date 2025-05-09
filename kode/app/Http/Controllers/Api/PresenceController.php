<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Models\Seller;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PresenceController extends Controller
{
    public function saveLastSeen(Request $request)
    {
        $role = $request->role;
        $id = $request->user_id;
        $time = $request->last_seen;

        if ($role === 'customer') {
            $user = User::find($id);
        } elseif ($role === 'seller') {
            $user = Seller::find($id);
        }

        if ($user) {
            $user->last_seen = $time;
            $user->save();
            return response()->json(['status' => 'success']);
        }

        return response()->json(['status' => 'not found'], 404);
    }

    public function getLastSeen(Request $request)
    {
        $role = $request->query('role');
        $id = $request->query('id');

        $model = $role === 'seller' ? Seller::find($id) : User::find($id);

        return response()->json([
            'last_seen' => optional($model)->last_seen,
        ]);
    }
}
