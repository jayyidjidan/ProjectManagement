<?php

namespace App\Http\Controllers;

use App\Models\Roles;
use App\Models\Users;
use App\Models\Members;
use App\Models\Jabatan;
use App\Models\Skills;
use App\Models\InvitationToken;

use Illuminate\Support\Facades\Mail;
use App\Mail\InvitationMail;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class InvitationController extends Controller
{
    public function index()
    {
        // 1. Tangkap keyword pencarian global
        $keyword = request('q');

        $invitations = InvitationToken::with('role')
            // 2. TAMBAHKAN LOGIKA SEARCH DI SINI
            ->when($keyword, function ($query, $keyword) {
                // Mencari berdasarkan email undangan
                return $query->where('email', 'like', "%{$keyword}%")
                             // ATAU mencari berdasarkan nama role (via relasi)
                             ->orWhereHas('role', function ($q) use ($keyword) {
                                 // Sesuaikan 'role_name' dengan nama kolom di tabel role-mu
                                 $q->where('role_name', 'like', "%{$keyword}%");
                             });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString(); // Agar keyword pencarian tetap terbawa saat pindah page pagination

        return view(
            'invitations.index',
            compact('invitations')
        );
    }

    public function create()
    {
        $roles = Roles::all();

        return view(
            'invitations.create',
            compact('roles')
        );
    }

    public function store(Request $request)
    {
        $request->validate([
            // Tambahkan unique ke tabel users dan invitation_tokens
            'email' => 'required|email|unique:users,email|unique:invitation_tokens,email',
            'id_role' => 'required|exists:roles,id_role'
        ], [
            // (Opsional) Custom pesan error agar lebih mudah dipahami
            'email.unique' => 'Email ini sudah terdaftar sebagai user atau sudah pernah diundang sebelumnya.'
        ]);

        $token = Str::random(64);

        $invitation = InvitationToken::create([
            'email'      => $request->email,
            'id_role'    => $request->id_role,
            'token'      => $token,
            'expired_at' => now()->addDays(7)
        ]);

        $url = route('invite.register', $token);

        Mail::to($request->email)->send(
            new InvitationMail($url)
        );

        return redirect()
            ->route('invitations.index')
            ->with('success', 'Invitation sent successfully.');
    }

    public function destroy(
        InvitationToken $invitation
    )
    {
        $invitation->delete();

        return redirect()
            ->route(
                'invitations.index'
            )
            ->with(
                'success',
                'Invitation deleted.'
            );
    }

    public function registerForm($token)
    {
        $invitation = InvitationToken::where('token', $token)->firstOrFail();

        // 1. Cek apakah link sudah pernah digunakan
        if ($invitation->is_used) {
            return view('auth.invite-invalid', [
                'title' => 'Link Already Used',
                'message' => 'This invitation link has already been used to register an account. If you already have an account, please log in.'
            ]);
        }

        // 2. Cek apakah link sudah kadaluarsa (expired)
        if ($invitation->expired_at && now()->greaterThan($invitation->expired_at)) {
            return view('auth.invite-invalid', [
                'title' => 'Link Expired',
                'message' => 'This invitation link has expired. Please contact your administrator to request a new invitation.'
            ]);
        }

        $positions = Jabatan::orderBy('position_name')->get();

        $skills = Skills::orderBy('skill_name')->get();

        return view('auth.invite-register', compact(
            'invitation',
            'positions',
            'skills'
        ));
    }

    public function register(
        Request $request,
        $token
    )
    {
        $invitation =
            InvitationToken::where(
                'token',
                $token
            )
            ->firstOrFail();

        if (
            $invitation->is_used
        ) {

            return back()->with(
                'error',
                'Invitation already used.'
            );
        }

        if (
            $invitation->expired_at &&
            now()->greaterThan(
                $invitation->expired_at
            )
        ) {

            return back()->with(
                'error',
                'Invitation expired.'
            );
        }

        $request->validate([

            'username' =>
                'required|max:100|unique:users,username',

            'member_name' =>
                'required|max:150',

            'id_position' =>
                'required|exists:jabatans,id_position',

            'skills' =>
                'nullable|array',

            'profile_photo' =>
                'nullable|image|max:2048',

            'password' =>
                'required|min:8|confirmed',

        ]);

        $user = Users::create([

            'username' =>
                $request->username,

            'email' =>
                $invitation->email,

            'password' =>
                Hash::make(
                    $request->password
                ),

            'id_role' =>
                $invitation->id_role

        ]);

        $photoPath = null;

        if (
            $request->hasFile(
                'profile_photo'
            )
        ) {

            $photoPath = $request
                ->file(
                    'profile_photo'
                )
                ->store(
                    'profile-photos',
                    'public'
                );
        }

        $member = Members::create([

            'id_user' =>
                $user->id_user,

            'member_name' =>
                $request->member_name,

            'profile_photo' =>
                $photoPath,

            'id_position' =>
                $request->id_position,

            'joined_date' =>
                now(),

            'total_cuti' => 0,

            'total_WFH' => 0,

            'total_overtime' => 0,

            'point' => 0

        ]);

        foreach (
            $request->skills ?? []
            as $skillInput
        ) {

            if (
                is_numeric(
                    $skillInput
                )
            ) {

                $member->skills()
                    ->syncWithoutDetaching([
                        $skillInput
                    ]);

            } else {

                $skill =
                    Skills::firstOrCreate([

                        'skill_name' =>
                            trim(
                                $skillInput
                            )

                    ]);

                $member->skills()
                    ->syncWithoutDetaching([

                        $skill->id_skill

                    ]);
            }
        }

        $invitation->update([

            'is_used' => true

        ]);

        Auth::login($user);

        return redirect()
            ->route(
                'dashboard'
            )
            ->with(
                'success',
                'Registration successful.'
            );
    }
}